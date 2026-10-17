<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\SesionEntrenamientoRequest;
use App\Models\Usuario;
use App\Models\SesionEntrenamiento;
use App\Models\Aparato;

class GimnastaController extends Controller
{
    public function dashboard()
    {
        $gimnasta = Usuario::with(['rol', 'entrenador'])
            ->find(session('usuario_id'));
        
        return view('gimnasta.dashboard', compact('gimnasta'));
    }

    public function miProgreso()
    {
        $gimnasta = Usuario::with(['rol', 'entrenador'])
            ->find(session('usuario_id'));
        
        return view('gimnasta.progreso', compact('gimnasta'));
    }

    public function miEntrenador()
    {
        $gimnasta = Usuario::with(['rol', 'entrenador'])
            ->find(session('usuario_id'));
        
        return view('gimnasta.entrenador', compact('gimnasta'));
    }

    public function misSesiones()
    {
        $gimnasta = Usuario::find(session('usuario_id'));
        $sesiones = SesionEntrenamiento::with(['entrenador', 'aparato'])
            ->where('ID_Gimnasta', $gimnasta->ID_Usuario)
            ->orderBy('Fecha', 'desc')
            ->orderBy('Hora_Inicio', 'desc')
            ->get();
        
        return view('gimnasta.sesiones', compact('gimnasta', 'sesiones'));
    }

    public function crearSesion()
    {
        $gimnasta = Usuario::find(session('usuario_id'));
        $aparatos = Aparato::where('activo', true)->get();
        
        return view('gimnasta.crear-sesion', compact('gimnasta', 'aparatos'));
    }

    public function guardarSesion(SesionEntrenamientoRequest $request)
    {
        // Asignación automática de entrenador
        $entrenadorId = SesionEntrenamiento::asignarEntrenadorAutomatico(
            $request->Fecha,
            $request->Hora_Inicio,
            $request->Hora_Fin,
            session('usuario_id')
        );
        
        if (!$entrenadorId) {
            return back()->withErrors(['error' => '🙅‍♂️ No hay entrenadores disponibles para el horario seleccionado (' . 
                $request->Hora_Inicio . ' - ' . $request->Hora_Fin . '). ' .
                'Por favor elige otro horario.'])->withInput();
        }

        SesionEntrenamiento::create([
            'ID_Gimnasta' => session('usuario_id'),
            'ID_Entrenador' => $entrenadorId,
            'ID_Aparato' => $request->ID_Aparato,
            'Fecha' => $request->Fecha,
            'Hora_Inicio' => $request->Hora_Inicio,
            'Hora_Fin' => $request->Hora_Fin,
            'Tipo_Asignacion' => 'Automatica',
            'Estado' => 'Programada'
        ]);

        return redirect()->route('gimnasta.sesiones')->with('success', '✅ ¡Sesión programada exitosamente! Tu entrenamiento está confirmado para el ' . 
            \Carbon\Carbon::parse($request->Fecha)->format('d/m/Y') . ' de ' . 
            $request->Hora_Inicio . ' a ' . $request->Hora_Fin);
    }

    public function editarSesion($id)
    {
        $gimnasta = Usuario::find(session('usuario_id'));
        $sesion = SesionEntrenamiento::with(['entrenador', 'aparato'])
            ->where('ID_Gimnasta', $gimnasta->ID_Usuario)
            ->where('ID_Sesion', $id)
            ->firstOrFail();
        
        if ($sesion->Estado !== 'Programada') {
            $estadoActual = $sesion->Estado == 'Completada' ? 'completada' : 
                           ($sesion->Estado == 'En_Curso' ? 'en curso' : 'cancelada');
            return redirect()->route('gimnasta.sesiones')
                ->withErrors(['error' => '❌ No puedes editar esta sesión porque ya está ' . $estadoActual . 
                    '. Solo las sesiones programadas pueden ser modificadas.']);
        }
        
        $aparatos = Aparato::where('activo', true)->get();
        
        return view('gimnasta.editar-sesion', compact('gimnasta', 'sesion', 'aparatos'));
    }

    public function actualizarSesion(SesionEntrenamientoRequest $request, $id)
    {
        $gimnasta = Usuario::find(session('usuario_id'));
        $sesion = SesionEntrenamiento::where('ID_Gimnasta', $gimnasta->ID_Usuario)
            ->where('ID_Sesion', $id)
            ->firstOrFail();
        
        if ($sesion->Estado !== 'Programada') {
            return redirect()->route('gimnasta.sesiones')
                ->withErrors(['error' => '❌ No puedes editar esta sesión porque ya no está programada.']);
        }

        // Asignación automática de entrenador
        $entrenadorId = SesionEntrenamiento::asignarEntrenadorAutomatico(
            $request->Fecha,
            $request->Hora_Inicio,
            $request->Hora_Fin,
            session('usuario_id'),
            $id
        );
        
        if (!$entrenadorId) {
            return back()->withErrors(['error' => '🙅‍♂️ No hay entrenadores disponibles para el horario seleccionado (' . 
                $request->Hora_Inicio . ' - ' . $request->Hora_Fin . '). ' .
                'Por favor elige otro horario.'])->withInput();
        }

        $sesion->update([
            'ID_Entrenador' => $entrenadorId,
            'ID_Aparato' => $request->ID_Aparato,
            'Fecha' => $request->Fecha,
            'Hora_Inicio' => $request->Hora_Inicio,
            'Hora_Fin' => $request->Hora_Fin,
            'Tipo_Asignacion' => 'Automatica'
        ]);

        return redirect()->route('gimnasta.sesiones')->with('success', '✅ ¡Sesión actualizada exitosamente! Los cambios han sido guardados correctamente.');
    }

    public function cancelarSesion($id)
    {
        $gimnasta = Usuario::find(session('usuario_id'));
        $sesion = SesionEntrenamiento::where('ID_Gimnasta', $gimnasta->ID_Usuario)
            ->where('ID_Sesion', $id)
            ->firstOrFail();
        
        if ($sesion->Estado !== 'Programada') {
            return response()->json([
                'error' => '❌ No puedes cancelar esta sesión porque ya no está programada.'
            ], 400);
        }
        
        $sesion->update(['Estado' => 'Cancelada']);
        
        return response()->json(['success' => true, 'message' => '✅ Sesión cancelada exitosamente. El horario ahora está disponible.']);
    }
}