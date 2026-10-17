<?php

namespace App\Http\Controllers;

use App\Models\SolicitudNivel;
use App\Models\Usuario;
use App\Services\AuditService;
use Illuminate\Http\Request;

class SolicitudNivelController extends Controller
{
    protected $auditService;

    public function __construct(AuditService $auditService)
    {
        $this->auditService = $auditService;
    }

    // Vista para gimnastas
    public function index()
    {
        $usuario = Usuario::find(session('usuario_id'));
        $solicitudes = SolicitudNivel::where('ID_Gimnasta', $usuario->ID_Usuario)
            ->with(['entrenador', 'aprobador'])
            ->orderBy('fecha_solicitud', 'desc')
            ->get();

        $siguienteNivel = SolicitudNivel::siguienteNivel($usuario->Nivel);
        $tieneSolicitudPendiente = $solicitudes->where('estado', 'pendiente')->isNotEmpty();

        return view('gimnasta.solicitudes-nivel.index', compact('usuario', 'solicitudes', 'siguienteNivel', 'tieneSolicitudPendiente'));
    }

    // Crear solicitud de evaluación (gimnasta)
    public function store(Request $request)
    {
        $usuario = Usuario::find(session('usuario_id'));
        
        $request->validate([
            'comentarios_gimnasta' => 'nullable|string|max:500'
        ]);

        $siguienteNivel = SolicitudNivel::siguienteNivel($usuario->Nivel);
        
        if (!$siguienteNivel) {
            return back()->with('error', 'Ya estás en el nivel máximo');
        }

        // Verificar si ya tiene una solicitud pendiente
        $solicitudPendiente = SolicitudNivel::where('ID_Gimnasta', $usuario->ID_Usuario)
            ->where('estado', 'pendiente')
            ->exists();

        if ($solicitudPendiente) {
            return back()->with('error', 'Ya tienes una solicitud pendiente');
        }

        $solicitud = SolicitudNivel::create([
            'ID_Gimnasta' => $usuario->ID_Usuario,
            'nivel_actual' => $usuario->Nivel,
            'nivel_solicitado' => $siguienteNivel,
            'tipo' => 'evaluacion',
            'comentarios_gimnasta' => $request->comentarios_gimnasta,
            'estado' => 'pendiente'
        ]);

        $this->auditService->log('SOLICITUD_NIVEL_CREADA', $solicitud, ['descripcion' => 'Solicitud de nivel creada']);

        return back()->with('success', 'Solicitud de evaluación enviada correctamente');
    }

    // Vista para entrenadores
    public function indexEntrenador()
    {
        $solicitudes = SolicitudNivel::with(['gimnasta', 'entrenador', 'aprobador'])
            ->orderBy('estado', 'asc')
            ->orderBy('fecha_solicitud', 'desc')
            ->get();

        $gimnastas = Usuario::gimnastas()->activos()->get();

        return view('entrenador.solicitudes-nivel.index', compact('solicitudes', 'gimnastas'));
    }

    // Crear recomendación (entrenador)
    public function recomendar(Request $request)
    {
        $request->validate([
            'ID_Gimnasta' => 'required|exists:usuarios,ID_Usuario',
            'nivel_solicitado' => 'required|in:Junior,Senior',
            'comentarios_entrenador' => 'required|string|max:500'
        ]);

        $gimnasta = Usuario::findOrFail($request->ID_Gimnasta);
        
        if (!SolicitudNivel::puedeSubirNivel($gimnasta->Nivel, $request->nivel_solicitado)) {
            return back()->with('error', 'Solo se puede subir un nivel a la vez');
        }

        // Verificar si ya tiene una solicitud pendiente
        $solicitudPendiente = SolicitudNivel::where('ID_Gimnasta', $request->ID_Gimnasta)
            ->where('estado', 'pendiente')
            ->exists();

        if ($solicitudPendiente) {
            return back()->with('error', 'El gimnasta ya tiene una solicitud pendiente');
        }

        $solicitud = SolicitudNivel::create([
            'ID_Gimnasta' => $request->ID_Gimnasta,
            'nivel_actual' => $gimnasta->Nivel,
            'nivel_solicitado' => $request->nivel_solicitado,
            'tipo' => 'recomendacion',
            'ID_Entrenador' => session('usuario_id'),
            'comentarios_entrenador' => $request->comentarios_entrenador,
            'estado' => 'pendiente'
        ]);

        $this->auditService->log('RECOMENDACION_NIVEL_CREADA', $solicitud, ['descripcion' => 'Recomendación de nivel creada']);

        return back()->with('success', 'Recomendación enviada correctamente');
    }

    // Vista para admin
    public function indexAdmin()
    {
        $solicitudes = SolicitudNivel::with(['gimnasta', 'entrenador', 'aprobador'])
            ->orderBy('estado', 'asc')
            ->orderBy('fecha_solicitud', 'desc')
            ->get();

        return view('admin.solicitudes-nivel.index', compact('solicitudes'));
    }

    // Aprobar solicitud (admin)
    public function aprobar(Request $request, $id)
    {
        $request->validate([
            'comentarios_admin' => 'nullable|string|max:500'
        ]);

        $solicitud = SolicitudNivel::findOrFail($id);
        
        if ($solicitud->estado !== 'pendiente') {
            return back()->with('error', 'Esta solicitud ya fue procesada');
        }

        $solicitud->update([
            'estado' => 'aprobada',
            'comentarios_admin' => $request->comentarios_admin,
            'fecha_respuesta' => now(),
            'aprobado_por' => session('usuario_id')
        ]);

        // Actualizar nivel del gimnasta
        $gimnasta = $solicitud->gimnasta;
        $gimnasta->update(['Nivel' => $solicitud->nivel_solicitado]);

        $this->auditService->log('NIVEL_APROBADO', $gimnasta, ['descripcion' => "Nivel actualizado de {$solicitud->nivel_actual} a {$solicitud->nivel_solicitado}"]);

        return back()->with('success', 'Solicitud aprobada y nivel actualizado');
    }

    // Rechazar solicitud (admin)
    public function rechazar(Request $request, $id)
    {
        $request->validate([
            'comentarios_admin' => 'required|string|max:500'
        ]);

        $solicitud = SolicitudNivel::findOrFail($id);
        
        if ($solicitud->estado !== 'pendiente') {
            return back()->with('error', 'Esta solicitud ya fue procesada');
        }

        $solicitud->update([
            'estado' => 'rechazada',
            'comentarios_admin' => $request->comentarios_admin,
            'fecha_respuesta' => now(),
            'aprobado_por' => session('usuario_id')
        ]);

        $this->auditService->log('NIVEL_RECHAZADO', $solicitud, ['descripcion' => 'Solicitud de nivel rechazada']);

        return back()->with('success', 'Solicitud rechazada');
    }

    // Aprobar solicitud (entrenador)
    public function aprobarEntrenador(Request $request, $id)
    {
        $request->validate([
            'comentarios_entrenador' => 'nullable|string|max:500'
        ]);

        $solicitud = SolicitudNivel::findOrFail($id);
        
        if ($solicitud->estado !== 'pendiente') {
            return back()->with('error', 'Esta solicitud ya fue procesada');
        }

        $solicitud->update([
            'estado' => 'aprobada',
            'comentarios_entrenador' => $request->comentarios_entrenador ?? $solicitud->comentarios_entrenador,
            'fecha_respuesta' => now(),
            'aprobado_por' => session('usuario_id')
        ]);

        // Actualizar nivel del gimnasta
        $gimnasta = $solicitud->gimnasta;
        $gimnasta->update(['Nivel' => $solicitud->nivel_solicitado]);

        $this->auditService->log('NIVEL_APROBADO', $gimnasta, ['descripcion' => "Nivel actualizado de {$solicitud->nivel_actual} a {$solicitud->nivel_solicitado} por entrenador"]);

        return back()->with('success', 'Solicitud aprobada y nivel actualizado');
    }

    // Rechazar solicitud (entrenador)
    public function rechazarEntrenador(Request $request, $id)
    {
        $request->validate([
            'comentarios_entrenador' => 'required|string|max:500'
        ]);

        $solicitud = SolicitudNivel::findOrFail($id);
        
        if ($solicitud->estado !== 'pendiente') {
            return back()->with('error', 'Esta solicitud ya fue procesada');
        }

        $solicitud->update([
            'estado' => 'rechazada',
            'comentarios_entrenador' => $request->comentarios_entrenador,
            'fecha_respuesta' => now(),
            'aprobado_por' => session('usuario_id')
        ]);

        $this->auditService->log('NIVEL_RECHAZADO', $solicitud, ['descripcion' => 'Solicitud de nivel rechazada por entrenador']);

        return back()->with('success', 'Solicitud rechazada');
    }
}
