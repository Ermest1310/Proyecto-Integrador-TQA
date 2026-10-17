<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\SesionEntrenamientoRequest;
use App\Models\SesionEntrenamiento;
use App\Models\Usuario;
use App\Models\Aparato;
use App\Services\AsignacionSesionAStarService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SesionEntrenamientoController extends Controller
{
    public function index(Request $request)
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
        
        $query = SesionEntrenamiento::with(['gimnasta', 'entrenador', 'aparato'])
            ->where('Estado', '!=', 'Cancelada');
        
        // Filtros
        if ($request->fecha_desde) {
            $query->where('Fecha', '>=', $request->fecha_desde);
        }
        if ($request->fecha_hasta) {
            $query->where('Fecha', '<=', $request->fecha_hasta);
        }
        if ($request->gimnasta) {
            $query->where('ID_Gimnasta', $request->gimnasta);
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
        $gimnastas = Usuario::whereHas('rol', function($q) {
            $q->where('NombreRol', 'Gimnasta');
        })->where('activo', true)->get();
        
        $entrenadores = Usuario::whereHas('rol', function($q) {
            $q->where('NombreRol', 'Entrenador');
        })->where('activo', true)->get();
        
        $aparatos = Aparato::where('activo', true)->where('EstadoMantenimiento', 'Operativo')->get();
        
        // Estadísticas
        $estadisticas = [
            'total_sesiones' => SesionEntrenamiento::where('Estado', '!=', 'Cancelada')->count(),
            'sesiones_completadas' => SesionEntrenamiento::where('Estado', 'Completada')->count(),
            'sesiones_programadas' => SesionEntrenamiento::where('Estado', 'Programada')->count(),
            'sesiones_hoy' => SesionEntrenamiento::where('Estado', '!=', 'Cancelada')
                ->where('Fecha', now()->toDateString())->count()
        ];
        
        return view('sesiones.index', compact('sesiones', 'gimnastas', 'entrenadores', 'aparatos', 'estadisticas'));
    }
    
    public function historial(Request $request)
    {
        $query = SesionEntrenamiento::with(['gimnasta', 'entrenador', 'aparato'])
            ->whereIn('Estado', ['Completada', 'Cancelada']);
        
        // Filtros
        if ($request->fecha_desde) {
            $query->where('Fecha', '>=', $request->fecha_desde);
        }
        if ($request->fecha_hasta) {
            $query->where('Fecha', '<=', $request->fecha_hasta);
        }
        if ($request->gimnasta) {
            $query->where('ID_Gimnasta', $request->gimnasta);
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
        $gimnastas = Usuario::whereHas('rol', function($q) {
            $q->where('NombreRol', 'Gimnasta');
        })->where('activo', true)->get();
        
        $entrenadores = Usuario::whereHas('rol', function($q) {
            $q->where('NombreRol', 'Entrenador');
        })->where('activo', true)->get();
        
        $aparatos = Aparato::where('activo', true)->where('EstadoMantenimiento', 'Operativo')->get();
        
        return view('sesiones.historial', compact('sesiones', 'gimnastas', 'entrenadores', 'aparatos'));
    }

    public function create()
    {
        $gimnastas = Usuario::whereHas('rol', function($q) {
            $q->where('NombreRol', 'Gimnasta');
        })->where('activo', true)->get();
        
        $aparatos = Aparato::where('activo', true)
            ->where('EstadoMantenimiento', 'Operativo')
            ->where(function($query) {
                $query->whereNull('ProximoMantenimiento')
                      ->orWhere('ProximoMantenimiento', '>', now()->toDateString());
            })
            ->get();
        
        return view('sesiones.create', compact('gimnastas', 'aparatos'));
    }

    public function store(SesionEntrenamientoRequest $request)
    {
        // Las validaciones se manejan automáticamente por SesionEntrenamientoRequest

        // Asignación automática si no se especifica entrenador
        if (!$request->ID_Entrenador) {
            $entrenadorId = SesionEntrenamiento::asignarEntrenadorAutomatico(
                $request->Fecha,
                $request->Hora_Inicio,
                $request->Hora_Fin,
                $request->ID_Gimnasta
            );
            
            if (!$entrenadorId) {
                return back()->withErrors(['error' => '🙅‍♂️ No hay entrenadores disponibles para el horario seleccionado (' . 
                    $request->Hora_Inicio . ' - ' . $request->Hora_Fin . '). ' .
                    'Por favor elige otro horario o selecciona un entrenador específico.'])->withInput();
            }
            
            $tipoAsignacion = 'Automatica';
        } else {
            $entrenadorId = $request->ID_Entrenador;
            $tipoAsignacion = 'Manual';
        }

        SesionEntrenamiento::create([
            'ID_Gimnasta' => $request->ID_Gimnasta,
            'ID_Entrenador' => $entrenadorId,
            'ID_Aparato' => $request->ID_Aparato,
            'Fecha' => $request->Fecha,
            'Hora_Inicio' => $request->Hora_Inicio,
            'Hora_Fin' => $request->Hora_Fin,
            'Tipo_Asignacion' => $tipoAsignacion,
            'Estado' => 'Programada'
        ]);

        return redirect()->route('sesiones.index')->with('success', '✅ ¡Sesión programada exitosamente! El gimnasta ' . 
            Usuario::find($request->ID_Gimnasta)->Nombre . ' tiene su entrenamiento el ' . 
            \Carbon\Carbon::parse($request->Fecha)->format('d/m/Y') . ' de ' . 
            $request->Hora_Inicio . ' a ' . $request->Hora_Fin);
    }

    public function edit($id)
    {
        $sesion = SesionEntrenamiento::with(['gimnasta', 'entrenador', 'aparato'])->findOrFail($id);
        
        // Solo permitir editar sesiones programadas
        if ($sesion->Estado !== 'Programada') {
            $estadoActual = $sesion->Estado == 'Completada' ? 'completada' : 
                           ($sesion->Estado == 'En_Curso' ? 'en curso' : 'cancelada');
            return redirect()->route('sesiones.index')
                ->withErrors(['error' => '❌ No puedes editar esta sesión porque ya está ' . $estadoActual . 
                    '. Solo las sesiones programadas pueden ser modificadas.']);
        }
        
        $gimnastas = Usuario::whereHas('rol', function($q) {
            $q->where('NombreRol', 'Gimnasta');
        })->where('activo', true)->get();
        
        $aparatos = Aparato::where('activo', true)
            ->where('EstadoMantenimiento', 'Operativo')
            ->where(function($query) {
                $query->whereNull('ProximoMantenimiento')
                      ->orWhere('ProximoMantenimiento', '>', now()->toDateString());
            })
            ->get();
        
        return view('sesiones.edit', compact('sesion', 'gimnastas', 'aparatos'));
    }

    public function update(SesionEntrenamientoRequest $request, $id)
    {
        $sesion = SesionEntrenamiento::findOrFail($id);
        
        // Solo permitir editar sesiones programadas
        if ($sesion->Estado !== 'Programada') {
            $estadoActual = $sesion->Estado == 'Completada' ? 'completada' : 
                           ($sesion->Estado == 'En_Curso' ? 'en curso' : 'cancelada');
            return redirect()->route('sesiones.index')
                ->withErrors(['error' => '❌ No puedes editar esta sesión porque ya está ' . $estadoActual . 
                    '. Solo las sesiones programadas pueden ser modificadas.']);
        }
        
        // Validar que el aparato esté disponible si se especifica
        if ($request->ID_Aparato) {
            $aparato = Aparato::find($request->ID_Aparato);
            if (!$aparato || !$aparato->activo || $aparato->EstadoMantenimiento !== 'Operativo') {
                return back()->withErrors(['error' => '❌ El aparato seleccionado no está disponible o está en mantenimiento.'])->withInput();
            }
            
            // Verificar si tiene mantenimiento en la fecha de la sesión
            if ($aparato->ProximoMantenimiento && $aparato->ProximoMantenimiento->equalTo(\Carbon\Carbon::parse($request->Fecha))) {
                return back()->withErrors(['error' => '❌ El aparato tiene mantenimiento programado para esa fecha (' . $aparato->ProximoMantenimiento->format('d/m/Y') . ').'])->withInput();
            }
        }
        
        // Las validaciones se manejan automáticamente por SesionEntrenamientoRequest

        // Asignación automática si no se especifica entrenador
        if (!$request->ID_Entrenador) {
            $entrenadorId = SesionEntrenamiento::asignarEntrenadorAutomatico(
                $request->Fecha,
                $request->Hora_Inicio,
                $request->Hora_Fin,
                $request->ID_Gimnasta,
                $id // Excluir la sesión actual
            );
            
            if (!$entrenadorId) {
                return back()->withErrors(['error' => '🙅‍♂️ No hay entrenadores disponibles para el horario seleccionado (' . 
                    $request->Hora_Inicio . ' - ' . $request->Hora_Fin . '). ' .
                    'Por favor elige otro horario o selecciona un entrenador específico.'])->withInput();
            }
            
            $tipoAsignacion = 'Automatica';
        } else {
            $entrenadorId = $request->ID_Entrenador;
            $tipoAsignacion = 'Manual';
        }

        $sesion->update([
            'ID_Gimnasta' => $request->ID_Gimnasta,
            'ID_Entrenador' => $entrenadorId,
            'ID_Aparato' => $request->ID_Aparato,
            'Fecha' => $request->Fecha,
            'Hora_Inicio' => $request->Hora_Inicio,
            'Hora_Fin' => $request->Hora_Fin,
            'Tipo_Asignacion' => $tipoAsignacion
        ]);

        return redirect()->route('sesiones.index')->with('success', '✅ ¡Sesión actualizada exitosamente! Los cambios han sido guardados correctamente.');
    }

    public function destroy($id)
    {
        $sesion = SesionEntrenamiento::findOrFail($id);
        
        // Solo permitir eliminar sesiones programadas
        if ($sesion->Estado !== 'Programada') {
            if ($sesion->Estado == 'Completada') {
                return response()->json([
                    'error' => '❌ No puedes cancelar esta sesión porque ya fue completada. Las sesiones completadas no pueden ser canceladas.'
                ], 400);
            } else {
                $estadoActual = $sesion->Estado == 'En_Curso' ? 'en curso' : 'cancelada';
                return response()->json([
                    'error' => '❌ No puedes cancelar esta sesión porque ya está ' . $estadoActual . 
                        '. Solo las sesiones programadas pueden ser canceladas.'
                ], 400);
            }
        }
        
        // Marcar como cancelada en lugar de eliminar (soft delete lógico)
        $sesion->update(['Estado' => 'Cancelada']);
        
        return response()->json(['success' => true, 'message' => '✅ Sesión cancelada exitosamente. El horario ahora está disponible.']);
    }

    public function optimizarAsignacion(Request $request)
    {
        $request->validate([
            'gimnastas' => 'required|array|min:1',
            'gimnastas.*' => 'integer|exists:usuarios,ID_Usuario',
            'fecha' => 'required|date|after_or_equal:today',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
            'duracion_bloque' => 'nullable|integer|min:30|max:120',
            'guardar' => 'nullable|boolean',
        ]);

        $servicio = new AsignacionSesionAStarService();

        $resultado = $servicio->optimizar(
            $request->input('gimnastas'),
            $request->input('fecha'),
            [$request->input('hora_inicio'), $request->input('hora_fin')],
            $request->input('duracion_bloque', 60)
        );

        if (!$resultado['success']) {
            return response()->json($resultado, 422);
        }

        if ($request->input('guardar', false)) {
            DB::beginTransaction();
            try {
                $guardado = $servicio->guardarAsignaciones($resultado, $request->input('fecha'));
                DB::commit();
                $resultado['guardado'] = $guardado;
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'mensaje' => 'Error al guardar asignaciones: ' . $e->getMessage(),
                ], 500);
            }
        }

        return response()->json($resultado);
    }

    public function getEntrenadoresDisponibles(Request $request)
    {
        $entrenadores = SesionEntrenamiento::entrenadoresDisponibles(
            $request->fecha,
            $request->hora_inicio,
            $request->hora_fin
        );

        return response()->json($entrenadores);
    }

    public function validarDisponibilidad(Request $request)
    {
        $conflictos = [];
        
        // Validar disponibilidad del gimnasta
        if ($request->id_gimnasta && $request->fecha && $request->hora_inicio && $request->hora_fin) {
            $query = SesionEntrenamiento::where('ID_Gimnasta', $request->id_gimnasta)
                ->where('Fecha', $request->fecha)
                ->where('Estado', '!=', 'Cancelada')
                ->where(function($q) use ($request) {
                    $q->where(function($q1) use ($request) {
                        $q1->where('Hora_Inicio', '<=', $request->hora_inicio)
                           ->where('Hora_Fin', '>', $request->hora_inicio);
                    })->orWhere(function($q2) use ($request) {
                        $q2->where('Hora_Inicio', '<', $request->hora_fin)
                           ->where('Hora_Fin', '>=', $request->hora_fin);
                    })->orWhere(function($q3) use ($request) {
                        $q3->where('Hora_Inicio', '>=', $request->hora_inicio)
                           ->where('Hora_Fin', '<=', $request->hora_fin);
                    });
                });
                
            if ($request->sesion_id) {
                $query->where('ID_Sesion', '!=', $request->sesion_id);
            }
            
            if ($query->exists()) {
                $conflictos[] = 'El gimnasta ya tiene una sesión programada en ese horario';
            }
        }
        
        // Validar disponibilidad del entrenador si se especifica
        if ($request->id_entrenador && $request->fecha && $request->hora_inicio && $request->hora_fin) {
            $query = SesionEntrenamiento::where('ID_Entrenador', $request->id_entrenador)
                ->where('Fecha', $request->fecha)
                ->where('Estado', '!=', 'Cancelada')
                ->where(function($q) use ($request) {
                    $q->where(function($q1) use ($request) {
                        $q1->where('Hora_Inicio', '<=', $request->hora_inicio)
                           ->where('Hora_Fin', '>', $request->hora_inicio);
                    })->orWhere(function($q2) use ($request) {
                        $q2->where('Hora_Inicio', '<', $request->hora_fin)
                           ->where('Hora_Fin', '>=', $request->hora_fin);
                    })->orWhere(function($q3) use ($request) {
                        $q3->where('Hora_Inicio', '>=', $request->hora_inicio)
                           ->where('Hora_Fin', '<=', $request->hora_fin);
                    });
                });
                
            if ($request->sesion_id) {
                $query->where('ID_Sesion', '!=', $request->sesion_id);
            }
            
            if ($query->exists()) {
                $conflictos[] = 'El entrenador ya tiene una sesión programada en ese horario';
            }
        }
        
        return response()->json([
            'disponible' => empty($conflictos),
            'conflictos' => $conflictos
        ]);
    }
}