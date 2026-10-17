<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\SesionEntrenamientoRequest;
use App\Models\Usuario;
use App\Models\SesionEntrenamiento;
use App\Models\Aparato;
use Carbon\Carbon;

class GimnastaController extends Controller
{
    public function dashboard()
    {
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
        
        $gimnasta = Usuario::with('rol')
            ->find(session('usuario_id'));
        
        // Estadísticas para el dashboard
        $estadisticas = [
            'total_sesiones' => SesionEntrenamiento::where('ID_Gimnasta', $gimnasta->ID_Usuario)
                ->where('Estado', '!=', 'Cancelada')->count(),
            'sesiones_completadas' => SesionEntrenamiento::where('ID_Gimnasta', $gimnasta->ID_Usuario)
                ->where('Estado', 'Completada')->count(),
            'sesiones_programadas' => SesionEntrenamiento::where('ID_Gimnasta', $gimnasta->ID_Usuario)
                ->where('Estado', 'Programada')->count(),
            'sesiones_este_mes' => SesionEntrenamiento::where('ID_Gimnasta', $gimnasta->ID_Usuario)
                ->where('Estado', '!=', 'Cancelada')
                ->whereMonth('Fecha', now()->month)
                ->whereYear('Fecha', now()->year)->count()
        ];
        
        // Próximas sesiones (máximo 3)
        $proximasSesiones = SesionEntrenamiento::with(['entrenador', 'aparato'])
            ->where('ID_Gimnasta', $gimnasta->ID_Usuario)
            ->where('Estado', 'Programada')
            ->where('Fecha', '>=', now()->toDateString())
            ->orderBy('Fecha', 'asc')
            ->orderBy('Hora_Inicio', 'asc')
            ->limit(3)
            ->get();
        
        // Actividad reciente (últimas 5 sesiones completadas)
        $actividadReciente = SesionEntrenamiento::with(['entrenador', 'aparato'])
            ->where('ID_Gimnasta', $gimnasta->ID_Usuario)
            ->where('Estado', 'Completada')
            ->orderBy('Fecha', 'desc')
            ->orderBy('Hora_Inicio', 'desc')
            ->limit(5)
            ->get();
        
        return view('gimnasta.dashboard', compact('gimnasta', 'estadisticas', 'proximasSesiones', 'actividadReciente'));
    }

    public function miProgreso()
    {
        $gimnasta = Usuario::with('rol')
            ->find(session('usuario_id'));
        
        return view('gimnasta.progreso', compact('gimnasta'));
    }

    public function miEntrenador()
    {
        $gimnasta = Usuario::find(session('usuario_id'));
        
        // Estadísticas por entrenador (solo sesiones no canceladas)
        $estadisticasEntrenadores = SesionEntrenamiento::with('entrenador')
            ->where('ID_Gimnasta', $gimnasta->ID_Usuario)
            ->where('Estado', '!=', 'Cancelada')
            ->selectRaw('ID_Entrenador, 
                COUNT(*) as total_sesiones,
                SUM(CASE WHEN Estado = "Completada" THEN 1 ELSE 0 END) as sesiones_completadas,
                SUM(CASE WHEN Estado = "Programada" THEN 1 ELSE 0 END) as sesiones_programadas')
            ->groupBy('ID_Entrenador')
            ->get();
        
        return view('gimnasta.entrenador', compact('gimnasta', 'estadisticasEntrenadores'));
    }

    public function misSesiones(Request $request)
    {
        $gimnasta = Usuario::find(session('usuario_id'));
        
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
        
        $query = SesionEntrenamiento::with(['entrenador', 'aparato'])
            ->where('ID_Gimnasta', $gimnasta->ID_Usuario)
            ->where('Estado', '!=', 'Cancelada');
        
        // Filtros
        if ($request->fecha_desde) {
            $query->where('Fecha', '>=', $request->fecha_desde);
        }
        if ($request->fecha_hasta) {
            $query->where('Fecha', '<=', $request->fecha_hasta);
        }
        if ($request->entrenador) {
            $query->where('ID_Entrenador', $request->entrenador);
        }
        if ($request->aparato) {
            $query->where('ID_Aparato', $request->aparato);
        }
        if ($request->estado) {
            $query->where('Estado', $request->estado);
        }
        
        $sesiones = $query->orderBy('Fecha', 'asc')
            ->orderBy('Hora_Inicio', 'asc')
            ->get();
        
        // Datos para filtros
        $entrenadores = Usuario::where('ID_Rol', 2)->get();
        $aparatos = Aparato::where('activo', true)
            ->where('EstadoMantenimiento', 'Operativo')
            ->where(function($query) {
                $query->whereNull('ProximoMantenimiento')
                      ->orWhere('ProximoMantenimiento', '>', now()->toDateString());
            })
            ->get();
        
        return view('gimnasta.sesiones', compact('gimnasta', 'sesiones', 'entrenadores', 'aparatos'));
    }

    public function crearSesion()
    {
        $gimnasta = Usuario::find(session('usuario_id'));
        
        if ($gimnasta->tieneLesionActiva()) {
            return redirect()->route('gimnasta.dashboard')
                ->with('error', 'No puedes crear sesiones mientras tengas una lesión activa.');
        }
        
        $aparatos = Aparato::where('activo', true)
            ->where('EstadoMantenimiento', 'Operativo')
            ->where(function($query) {
                $query->whereNull('ProximoMantenimiento')
                      ->orWhere('ProximoMantenimiento', '>', now()->toDateString());
            })
            ->get();
        
        return view('gimnasta.crear-sesion', compact('gimnasta', 'aparatos'));
    }

    public function guardarSesion(SesionEntrenamientoRequest $request)
    {
        // Asignación automática si no se especifica entrenador
        if (!$request->ID_Entrenador) {
            $entrenadorId = SesionEntrenamiento::asignarEntrenadorAutomatico(
                $request->Fecha,
                $request->Hora_Inicio,
                $request->Hora_Fin,
                session('usuario_id')
            );
            
            if (!$entrenadorId) {
                return back()->withErrors(['error' => '🙅♂️ No hay entrenadores disponibles para el horario seleccionado (' . 
                    $request->Hora_Inicio . ' - ' . $request->Hora_Fin . '). ' .
                    'Por favor elige otro horario o selecciona un entrenador específico.'])->withInput();
            }
            
            $tipoAsignacion = 'Automatica';
        } else {
            $entrenadorId = $request->ID_Entrenador;
            $tipoAsignacion = 'Manual';
        }

        SesionEntrenamiento::create([
            'ID_Gimnasta' => session('usuario_id'),
            'ID_Entrenador' => $entrenadorId,
            'ID_Aparato' => $request->ID_Aparato,
            'Fecha' => $request->Fecha,
            'Hora_Inicio' => $request->Hora_Inicio,
            'Hora_Fin' => $request->Hora_Fin,
            'Tipo_Asignacion' => $tipoAsignacion,
            'Estado' => 'Programada'
        ]);

        return redirect()->route('gimnasta.sesiones')->with('success', '✅ ¡Sesión programada exitosamente! Tu entrenamiento está confirmado para el ' . 
            \Carbon\Carbon::parse($request->Fecha)->format('d/m/Y') . ' de ' . 
            $request->Hora_Inicio . ' a ' . $request->Hora_Fin);
    }



    public function cancelarSesion($id)
    {
        $gimnasta = Usuario::find(session('usuario_id'));
        $sesion = SesionEntrenamiento::where('ID_Gimnasta', $gimnasta->ID_Usuario)
            ->where('ID_Sesion', $id)
            ->firstOrFail();
        
        if ($sesion->Estado !== 'Programada') {
            if ($sesion->Estado == 'Completada') {
                return response()->json([
                    'error' => '❌ No puedes cancelar esta sesión porque ya fue completada. Las sesiones completadas no pueden ser canceladas.'
                ], 400);
            } else {
                return response()->json([
                    'error' => '❌ No puedes cancelar esta sesión porque ya no está programada.'
                ], 400);
            }
        }
        
        $sesion->update(['Estado' => 'Cancelada']);
        
        return response()->json(['success' => true, 'message' => '✅ Sesión cancelada exitosamente. El horario ahora está disponible.']);
    }



    public function historial(Request $request)
    {
        $gimnasta = Usuario::find(session('usuario_id'));
        
        $query = SesionEntrenamiento::with(['entrenador', 'aparato'])
            ->where('ID_Gimnasta', $gimnasta->ID_Usuario)
            ->whereIn('Estado', ['Completada', 'Cancelada']);
        
        // Filtros
        if ($request->fecha_desde) {
            $query->where('Fecha', '>=', $request->fecha_desde);
        }
        if ($request->fecha_hasta) {
            $query->where('Fecha', '<=', $request->fecha_hasta);
        }
        if ($request->entrenador) {
            $query->where('ID_Entrenador', $request->entrenador);
        }
        if ($request->aparato) {
            $query->where('ID_Aparato', $request->aparato);
        }
        if ($request->estado) {
            $query->where('Estado', $request->estado);
        }
        
        $sesiones = $query->orderBy('Fecha', 'desc')
            ->orderBy('Hora_Inicio', 'desc')
            ->get();
        
        // Datos para filtros
        $entrenadores = Usuario::where('ID_Rol', 2)->get();
        $aparatos = Aparato::where('activo', true)
            ->where('EstadoMantenimiento', 'Operativo')
            ->where(function($query) {
                $query->whereNull('ProximoMantenimiento')
                      ->orWhere('ProximoMantenimiento', '>', now()->toDateString());
            })
            ->get();
        
        return view('gimnasta.historial', compact('gimnasta', 'sesiones', 'entrenadores', 'aparatos'));
    }

    public function calendario()
    {
        $gimnasta = Usuario::find(session('usuario_id'));
        
        // Obtener todas las sesiones para el calendario
        $sesiones = SesionEntrenamiento::with(['entrenador', 'aparato'])
            ->where('ID_Gimnasta', $gimnasta->ID_Usuario)
            ->where('Estado', '!=', 'Cancelada')
            ->get()
            ->map(function($sesion) {
                return [
                    'id' => $sesion->ID_Sesion,
                    'title' => ($sesion->aparato ? $sesion->aparato->Nombre : 'Entrenamiento general') . 
                              ' - ' . $sesion->entrenador->Nombre,
                    'start' => $sesion->Fecha->format('Y-m-d') . 'T' . $sesion->Hora_Inicio->format('H:i:s'),
                    'end' => $sesion->Fecha->format('Y-m-d') . 'T' . $sesion->Hora_Fin->format('H:i:s'),
                    'backgroundColor' => $sesion->Estado == 'Completada' ? '#28a745' : '#007bff',
                    'borderColor' => $sesion->Estado == 'Completada' ? '#28a745' : '#007bff',
                    'extendedProps' => [
                        'estado' => $sesion->Estado,
                        'entrenador' => $sesion->entrenador->Nombre . ' ' . $sesion->entrenador->Apellido,
                        'aparato' => $sesion->aparato ? $sesion->aparato->Nombre : 'Entrenamiento general',
                        'duracion' => $sesion->Duracion_Minutos
                    ]
                ];
            });
        
        return view('gimnasta.calendario', compact('gimnasta', 'sesiones'));
    }
}