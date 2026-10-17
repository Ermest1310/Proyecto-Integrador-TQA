<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\Rol;
use App\Models\SesionEntrenamiento;
use Illuminate\Support\Facades\Hash;

class EntrenadorController extends Controller
{
    public function dashboard()
    {
        $entrenadorId = session('usuario_id');
        
        // Debug: verificar datos
        $totalSesiones = SesionEntrenamiento::where('ID_Entrenador', $entrenadorId)->count();
        $todasSesiones = SesionEntrenamiento::where('ID_Entrenador', $entrenadorId)->get();
        
        // Estadísticas del entrenador
        $estadisticas = [
            'total_sesiones' => SesionEntrenamiento::where('ID_Entrenador', $entrenadorId)->count(),
            'sesiones_completadas' => SesionEntrenamiento::where('ID_Entrenador', $entrenadorId)
                ->where('Estado', 'Completada')->count(),
            'sesiones_programadas' => SesionEntrenamiento::where('ID_Entrenador', $entrenadorId)
                ->where('Estado', 'Programada')->count(),
            'gimnastas_activos' => Usuario::whereHas('sesionesComoGimnasta', function($q) use ($entrenadorId) {
                $q->where('ID_Entrenador', $entrenadorId);
            })->distinct()->count()
        ];
        
        // Próximas sesiones
        $proximasSesiones = SesionEntrenamiento::with(['gimnasta', 'aparato'])
            ->where('ID_Entrenador', $entrenadorId)
            ->orderBy('Fecha', 'asc')
            ->orderBy('Hora_Inicio', 'asc')
            ->limit(5)
            ->get();
        
        return view('entrenador.dashboard', compact('estadisticas', 'proximasSesiones'));
    }

    public function misGimnastas()
    {
        $entrenadorId = session('usuario_id');
        
        // Gimnastas con sesiones programadas con este entrenador
        $gimnastas = Usuario::with(['rol', 'lesiones' => function($q) {
            $q->where('activo', true)->where('Estado', 'Lesionado');
        }])
        ->whereHas('rol', function($q) {
            $q->where('NombreRol', 'Gimnasta');
        })
        ->whereHas('sesionesComoGimnasta', function($q) use ($entrenadorId) {
            $q->where('ID_Entrenador', $entrenadorId)
              ->where('Estado', '!=', 'Cancelada');
        })
        ->where('activo', true)
        ->distinct()
        ->get();
        
        return view('entrenador.mis-gimnastas', compact('gimnastas'));
    }

    public function crearGimnasta()
    {
        return view('entrenador.crear-gimnasta');
    }

    public function guardarGimnasta(Request $request)
    {
        return redirect()->route('entrenador.dashboard')->with('error', 'Los entrenadores ya no pueden crear gimnastas directamente. Los usuarios deben postularse y ser aprobados por el administrador.');
    }

    public function misSesiones()
    {
        $entrenadorId = session('usuario_id');
        
        $sesiones = SesionEntrenamiento::with(['gimnasta', 'aparato'])
            ->where('ID_Entrenador', $entrenadorId)
            ->orderBy('Fecha', 'desc')
            ->orderBy('Hora_Inicio', 'desc')
            ->paginate(15);
        
        return view('entrenador.mis-sesiones', compact('sesiones'));
    }

    public function verGimnasta($id)
    {
        $gimnasta = Usuario::with(['rol', 'lesiones' => function($q) {
            $q->where('activo', true);
        }])->findOrFail($id);
        
        // Verificar que es un gimnasta
        if ($gimnasta->rol->NombreRol !== 'Gimnasta') {
            abort(403);
        }

        $estadisticas = [
            'total_sesiones' => SesionEntrenamiento::where('ID_Gimnasta', $id)
                ->where('Estado', '!=', 'Cancelada')->count(),
            'sesiones_completadas' => SesionEntrenamiento::where('ID_Gimnasta', $id)
                ->where('Estado', 'Completada')->count(),
            'sesiones_programadas' => SesionEntrenamiento::where('ID_Gimnasta', $id)
                ->where('Estado', 'Programada')->count(),
            'sesiones_este_mes' => SesionEntrenamiento::where('ID_Gimnasta', $id)
                ->where('Estado', '!=', 'Cancelada')
                ->whereMonth('Fecha', now()->month)
                ->whereYear('Fecha', now()->year)->count(),
            'lesiones_activas' => $gimnasta->lesiones->where('Estado', 'Lesionado')->count(),
            'promedio_duracion' => SesionEntrenamiento::where('ID_Gimnasta', $id)
                ->where('Estado', 'Completada')
                ->whereNotNull('Duracion_Minutos')
                ->avg('Duracion_Minutos'),
        ];

        $aparatosSesiones = SesionEntrenamiento::selectRaw('ID_Aparato, COUNT(*) as total')
            ->where('ID_Gimnasta', $id)
            ->where('Estado', '!=', 'Cancelada')
            ->whereNotNull('ID_Aparato')
            ->groupBy('ID_Aparato')
            ->with('aparato')
            ->get();

        $sesionesMensuales = SesionEntrenamiento::selectRaw("DATE_FORMAT(Fecha, '%Y-%m') as mes, COUNT(*) as total")
            ->where('ID_Gimnasta', $id)
            ->where('Estado', '!=', 'Cancelada')
            ->where('Fecha', '>=', now()->subMonths(6)->startOfMonth())
            ->groupBy('mes')
            ->orderBy('mes')
            ->pluck('total', 'mes');

        $gimnastaRecord = \App\Models\Gimnasta::where('ID_Usuario', $id)->first();
        $evaluacionNutricional = null;
        $evaluacionPsicologica = null;
        $evaluacionFisiologica = null;
        if ($gimnastaRecord) {
            $evaluacionNutricional = $gimnastaRecord->evaluacionesNutricionales()->latest('FechaEvaluacion')->first();
            $evaluacionPsicologica = $gimnastaRecord->evaluacionesPsicologicas()->latest('FechaEvaluacion')->first();
            $evaluacionFisiologica = $gimnastaRecord->evaluacionesFisiologicas()->latest('FechaEvaluacion')->first();
        }

        return view('entrenador.ver-gimnasta', compact(
            'gimnasta', 'estadisticas', 'aparatosSesiones', 'sesionesMensuales',
            'evaluacionNutricional', 'evaluacionPsicologica', 'evaluacionFisiologica'
        ));
    }

    public function dashboardGimnasta($id)
    {
        $entrenadorId = session('usuario_id');
        $gimnasta = Usuario::with(['rol', 'lesiones' => function($q) {
            $q->where('activo', true);
        }])->findOrFail($id);
        
        // Verificar que es un gimnasta
        if ($gimnasta->rol->NombreRol !== 'Gimnasta') {
            abort(403);
        }
        
        // Estadísticas del gimnasta con este entrenador
        $estadisticas = [
            'total_sesiones' => SesionEntrenamiento::where('ID_Gimnasta', $id)
                ->where('ID_Entrenador', $entrenadorId)->where('activo', true)->count(),
            'sesiones_completadas' => SesionEntrenamiento::where('ID_Gimnasta', $id)
                ->where('ID_Entrenador', $entrenadorId)->where('activo', true)
                ->where('Estado', 'Completada')->count(),
            'sesiones_programadas' => SesionEntrenamiento::where('ID_Gimnasta', $id)
                ->where('ID_Entrenador', $entrenadorId)->where('activo', true)
                ->where('Estado', 'Programada')->count(),
            'lesiones_activas' => $gimnasta->lesiones->where('Estado', 'Lesionado')->count()
        ];
        
        // Últimas sesiones
        $ultimasSesiones = SesionEntrenamiento::with('aparato')
            ->where('ID_Gimnasta', $id)
            ->where('ID_Entrenador', $entrenadorId)
            ->where('activo', true)
            ->orderBy('Fecha', 'desc')
            ->limit(5)
            ->get();
        
        // Próximas sesiones
        $proximasSesiones = SesionEntrenamiento::with('aparato')
            ->where('ID_Gimnasta', $id)
            ->where('ID_Entrenador', $entrenadorId)
            ->where('activo', true)
            ->where('Estado', 'Programada')
            ->where('Fecha', '>=', now()->toDateString())
            ->orderBy('Fecha', 'asc')
            ->limit(3)
            ->get();
        
        return view('entrenador.gimnasta-dashboard', compact('gimnasta', 'estadisticas', 'ultimasSesiones', 'proximasSesiones'));
    }

    public function calendario()
    {
        $entrenadorId = session('usuario_id');
        
        // Obtener todas las sesiones del entrenador para el calendario
        $sesiones = SesionEntrenamiento::with(['gimnasta', 'aparato'])
            ->where('ID_Entrenador', $entrenadorId)
            ->where('activo', true)
            ->get()
            ->map(function($sesion) {
                return [
                    'id' => $sesion->ID_Sesion,
                    'title' => ($sesion->aparato ? $sesion->aparato->Nombre : 'Entrenamiento general') . 
                              ' - ' . $sesion->gimnasta->Nombre,
                    'start' => $sesion->Fecha->format('Y-m-d') . 'T' . $sesion->Hora_Inicio->format('H:i:s'),
                    'end' => $sesion->Fecha->format('Y-m-d') . 'T' . $sesion->Hora_Fin->format('H:i:s'),
                    'backgroundColor' => $sesion->Estado == 'Completada' ? '#28a745' : '#007bff',
                    'borderColor' => $sesion->Estado == 'Completada' ? '#28a745' : '#007bff',
                    'extendedProps' => [
                        'estado' => $sesion->Estado,
                        'gimnasta' => $sesion->gimnasta->Nombre . ' ' . $sesion->gimnasta->Apellido,
                        'aparato' => $sesion->aparato ? $sesion->aparato->Nombre : 'Entrenamiento general',
                        'duracion' => $sesion->Duracion_Minutos
                    ]
                ];
            });
        
        return view('entrenador.calendario', compact('sesiones'));
    }
}