<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\SesionEntrenamiento;
use App\Models\Aparato;
use App\Models\Lesion;
use App\Models\Competencia;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $rol = session('usuario_rol');
        
        switch($rol) {
            case 'Administrador':
                // Marcar automáticamente como completadas las sesiones pasadas
                SesionEntrenamiento::where('Estado', 'Programada')
                    ->where(function($query) {
                        $query->where('Fecha', '<', now()->toDateString())
                              ->orWhere(function($q) {
                                  $q->where('Fecha', '=', now()->toDateString())
                                    ->where('Hora_Fin', '<', now()->toTimeString());
                              });
                    })
                    ->update(['Estado' => 'Completada']);
                
                // Estadísticas generales
                $estadisticas = [
                    'total_usuarios' => Usuario::where('activo', true)->count(),
                    'total_gimnastas' => Usuario::whereHas('rol', function($q) {
                        $q->where('NombreRol', 'Gimnasta');
                    })->where('activo', true)->count(),
                    'total_entrenadores' => Usuario::whereHas('rol', function($q) {
                        $q->where('NombreRol', 'Entrenador');
                    })->where('activo', true)->count(),
                    'sesiones_hoy' => SesionEntrenamiento::where('Estado', '!=', 'Cancelada')
                        ->where('Fecha', now()->toDateString())->count(),
                    'sesiones_programadas' => SesionEntrenamiento::where('Estado', 'Programada')->count(),
                    'sesiones_completadas' => SesionEntrenamiento::where('Estado', 'Completada')->count(),
                    'aparatos_operativos' => Aparato::where('activo', true)
                        ->where('EstadoMantenimiento', 'Operativo')->count(),
                    'lesiones_activas' => Lesion::where('activo', true)->count()
                ];
                
                // Próximas sesiones (máximo 5)
                $proximasSesiones = SesionEntrenamiento::with(['gimnasta', 'entrenador', 'aparato'])
                    ->where('Estado', 'Programada')
                    ->where('Fecha', '>=', now()->toDateString())
                    ->orderBy('Fecha', 'asc')
                    ->orderBy('Hora_Inicio', 'asc')
                    ->limit(5)
                    ->get();
                
                // Actividad reciente (últimas 5 sesiones completadas)
                $actividadReciente = SesionEntrenamiento::with(['gimnasta', 'entrenador', 'aparato'])
                    ->where('Estado', 'Completada')
                    ->orderBy('Fecha', 'desc')
                    ->orderBy('Hora_Inicio', 'desc')
                    ->limit(5)
                    ->get();
                
                return view('dashboard.index', compact('estadisticas', 'proximasSesiones', 'actividadReciente'));
            case 'Entrenador':
                return redirect()->route('entrenador.dashboard');
            case 'Médico':
            case 'Fisioterapeuta':
                return redirect()->route('medico.dashboard');
            case 'Gimnasta':
                return redirect()->route('gimnasta.dashboard');
            case 'Juez':
                return redirect()->route('juez.dashboard');
            default:
                return redirect()->route('login');
        }
    }
    
    public function adminInicio()
    {
        // Estadísticas básicas para el inicio
        $estadisticas = [
            'total_usuarios' => Usuario::where('activo', true)->count(),
            'total_gimnastas' => Usuario::whereHas('rol', function($q) {
                $q->where('NombreRol', 'Gimnasta');
            })->where('activo', true)->count(),
            'sesiones_hoy' => SesionEntrenamiento::where('Estado', '!=', 'Cancelada')
                ->where('Fecha', now()->toDateString())->count(),
            'postulaciones_pendientes' => \App\Models\Postulacion::where('estado', 'Pendiente')->count()
        ];
        
        return view('admin.inicio', compact('estadisticas'));
    }
    
    public function adminHistorial(Request $request)
    {
        $query = SesionEntrenamiento::with(['gimnasta', 'entrenador', 'aparato'])
            ->whereIn('Estado', ['Completada', 'Cancelada']);
        
        // Filtros
        if ($request->filled('estado')) {
            $query->where('Estado', $request->estado);
        }
        
        if ($request->filled('gimnasta')) {
            $query->where('ID_Gimnasta', $request->gimnasta);
        }
        
        if ($request->filled('entrenador')) {
            $query->where('ID_Entrenador', $request->entrenador);
        }
        
        if ($request->filled('aparato')) {
            $query->where('ID_Aparato', $request->aparato);
        }
        
        if ($request->filled('fecha_inicio')) {
            $query->where('Fecha', '>=', $request->fecha_inicio);
        }
        
        if ($request->filled('fecha_fin')) {
            $query->where('Fecha', '<=', $request->fecha_fin);
        }
        
        $sesiones = $query->orderBy('Fecha', 'desc')
            ->orderBy('Hora_Inicio', 'desc')
            ->paginate(15);
        
        // Datos para filtros
        $gimnastas = Usuario::whereHas('rol', function($q) {
            $q->where('NombreRol', 'Gimnasta');
        })->where('activo', true)->orderBy('Nombre')->get();
        
        $entrenadores = Usuario::whereHas('rol', function($q) {
            $q->where('NombreRol', 'Entrenador');
        })->where('activo', true)->orderBy('Nombre')->get();
        
        $aparatos = Aparato::where('activo', true)->orderBy('Nombre')->get();
        
        return view('admin.historial', compact('sesiones', 'gimnastas', 'entrenadores', 'aparatos'));
    }
}
