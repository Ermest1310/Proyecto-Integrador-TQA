<?php

namespace App\Services;

use App\Models\SesionEntrenamiento;
use App\Models\Usuario;
use App\Models\Aparato;
use App\Models\Lesion;
use App\Services\Support\SesionAStarNode;
use App\Services\Support\AStarPriorityQueue;
use Carbon\Carbon;

class AsignacionSesionAStarService
{
    private array $gimnastas;
    private array $entrenadores;
    private array $aparatos;
    private array $bloques;
    private array $restriccionesLesion;
    private array $capacidadAparatos;
    private int $maxIteraciones;

    public function __construct(int $maxIteraciones = 100000)
    {
        $this->maxIteraciones = $maxIteraciones;
        $this->capacidadAparatos = [
            'Suelo' => 4,
            'Caballo con Arcos' => 2,
            'Anillas' => 2,
            'Salto' => 2,
            'Barras Paralelas' => 2,
            'Barra Fija' => 2,
            'Barras Asimetricas' => 2,
        ];
    }

    public function optimizar(array $gimnastasIds, string $fecha, array $rangoHorario = ['08:00', '18:00'], int $duracionBloque = 60): array
    {
        $this->cargarDatos($gimnastasIds, $fecha);

        $this->bloques = $this->generarBloques($fecha, $rangoHorario[0], $rangoHorario[1], $duracionBloque);

        $this->restriccionesLesion = $this->cargarRestriccionesLesion($gimnastasIds);

        $resultado = $this->buscarAsignacionOptima();

        return $this->formatearResultado($resultado, $fecha);
    }

    private function cargarDatos(array $gimnastasIds, string $fecha): void
    {
        $this->gimnastas = Usuario::whereIn('ID_Usuario', $gimnastasIds)
            ->whereHas('rol', function ($q) {
                $q->where('NombreRol', 'Gimnasta');
            })
            ->where('activo', true)
            ->get()
            ->mapWithKeys(function ($g) {
                return [(string) $g->ID_Usuario => [
                    'id' => (string) $g->ID_Usuario,
                    'nombre' => $g->Nombre . ' ' . $g->Apellido,
                    'nivel' => $g->Nivel ?? 'Principiante',
                ]];
            })
            ->toArray();

        $this->entrenadores = Usuario::whereHas('rol', function ($q) {
            $q->where('NombreRol', 'Entrenador');
        })
            ->where('activo', true)
            ->get()
            ->mapWithKeys(function ($e) {
                return [(string) $e->ID_Usuario => [
                    'id' => (string) $e->ID_Usuario,
                    'nombre' => $e->Nombre . ' ' . $e->Apellido,
                    'carga_actual' => SesionEntrenamiento::where('ID_Entrenador', $e->ID_Usuario)
                        ->where('Fecha', '>=', now()->subMonth())
                        ->where('Estado', '!=', 'Cancelada')
                        ->count(),
                ]];
            })
            ->toArray();

        $this->aparatos = Aparato::where('activo', true)
            ->where('EstadoMantenimiento', 'Operativo')
            ->get()
            ->mapWithKeys(function ($a) {
                return [(string) $a->ID_Aparato => [
                    'id' => (string) $a->ID_Aparato,
                    'nombre' => $a->Nombre,
                    'capacidad' => $this->capacidadAparatos[$a->Nombre] ?? 2,
                ]];
            })
            ->toArray();
    }

    private function cargarRestriccionesLesion(array $gimnastasIds): array
    {
        $restricciones = [];

        $lesiones = Lesion::whereIn('ID_Gimnasta', $gimnastasIds)
            ->where('Estado', 'Lesionado')
            ->get();

        $mapeoAparatos = [
            'hombro' => ['Anillas', 'Barra Fija', 'Barras Paralelas'],
            'codo' => ['Anillas', 'Barra Fija', 'Barras Paralelas'],
            'muneca' => ['Suelo', 'Salto', 'Barras Asimetricas'],
            'rodilla' => ['Suelo', 'Salto', 'Caballo con Arcos'],
            'tobillo' => ['Suelo', 'Salto'],
            'espalda_baja' => ['Suelo', 'Caballo con Arcos', 'Barra Fija'],
            'cuello' => ['Anillas', 'Barra Fija'],
        ];

        foreach ($lesiones as $lesion) {
            $partes = json_decode($lesion->ParteCuerpo, true) ?? [$lesion->ParteCuerpo];

            foreach ($partes as $parte) {
                $parteLower = strtolower($parte);
                $aparatosProhibidos = $mapeoAparatos[$parteLower] ?? [];

                $restricciones[(string) $lesion->ID_Gimnasta] = array_merge(
                    $restricciones[(string) $lesion->ID_Gimnasta] ?? [],
                    $aparatosProhibidos
                );
            }
        }

        return $restricciones;
    }

    private function generarBloques(string $fecha, string $horaInicio, string $horaFin, int $duracionMinutos): array
    {
        $bloques = [];
        $inicio = Carbon::parse($fecha . ' ' . $horaInicio);
        $fin = Carbon::parse($fecha . ' ' . $horaFin);

        $i = 0;
        while ($inicio->copy()->addMinutes($duracionMinutos) <= $fin) {
            $bloques[] = [
                'id' => $i,
                'hora_inicio' => $inicio->format('H:i'),
                'hora_fin' => $inicio->copy()->addMinutes($duracionMinutos)->format('H:i'),
            ];
            $inicio->addMinutes($duracionMinutos);
            $i++;
        }

        return $bloques;
    }

    private function heurista(int $profundidadActual, int $totalGimnastas, array $cargaEntrenadores): float
    {
        $pendientes = $totalGimnastas - $profundidadActual;

        if ($pendientes <= 0) {
            return 0;
        }

        $cargas = array_column($this->entrenadores, 'carga_actual');
        foreach ($cargaEntrenadores as $id => $carga) {
            if (isset($cargas[array_search($id, array_column($this->entrenadores, 'id'))])) {
                $cargas[array_search($id, array_column($this->entrenadores, 'id'))] += $carga;
            }
        }

        $promedioCarga = array_sum($cargas) / max(1, count($cargas));
        $maxCarga = max($cargas);
        $desbalance = $maxCarga - $promedioCarga;

        $makespanEstimado = ceil($pendientes / max(1, count($this->aparatos))) * 60;

        return $makespanEstimado + ($desbalance * 30);
    }

    private function calcularCosto(string $gimnastaId, string $bloqueId, string $aparatoId, string $entrenadorId, array $cargaEntrenadores, array $usoAparatos): float
    {
        $costo = 0.0;

        $aparato = $this->aparatos[$aparatoId] ?? null;
        if (!$aparato) {
            return INF;
        }

        if (isset($this->restriccionesLesion[$gimnastaId]) && in_array($aparato['nombre'], $this->restriccionesLesion[$gimnastaId])) {
            $costo += 1000;
        }

        $capacidad = $aparato['capacidad'];
        $usoActual = $usoAparatos[$bloqueId . '_' . $aparatoId] ?? 0;
        if ($usoActual >= $capacidad) {
            $costo += 500;
        }

        $cargaEntrenador = $cargaEntrenadores[$entrenadorId] ?? 0;
        $cargas = array_column($this->entrenadores, 'carga_actual');
        $promedioCarga = array_sum($cargas) / max(1, count($cargas));

        if ($cargaEntrenador > $promedioCarga) {
            $costo += ($cargaEntrenador - $promedioCarga) * 50;
        }

        $nivelGimnasta = $this->gimnastas[$gimnastaId]['nivel'] ?? 'Principiante';
        $niveles = ['Principiante' => 1, 'Intermedio' => 2, 'Avanzado' => 3, 'Elite' => 4];
        $dificultad = $niveles[$nivelGimnasta] ?? 1;

        $costo += $dificultad * 5;

        return $costo;
    }

    private function buscarAsignacionOptima(): ?SesionAStarNode
    {
        $cola = new AStarPriorityQueue();
        $visitados = [];

        $gimnastaIds = array_keys($this->gimnastas);
        $totalGimnastas = count($gimnastaIds);

        foreach ($gimnastaIds as $gimnastaId) {
            foreach ($this->bloques as $bloque) {
                foreach ($this->aparatos as $aparatoId => $aparato) {
                    foreach ($this->entrenadores as $entrenadorId => $entrenador) {
                        $cargaEntrenadores = [$entrenadorId => 1];
                        $usoAparatos = [$bloque['id'] . '_' . $aparatoId => 1];

                        $costo = $this->calcularCosto(
                            $gimnastaId,
                            (string) $bloque['id'],
                            $aparatoId,
                            $entrenadorId,
                            $cargaEntrenadores,
                            $usoAparatos
                        );

                        if ($costo >= 1000) {
                            continue;
                        }

                        $asignaciones = [[
                            'gimnasta_id' => $gimnastaId,
                            'bloque_id' => (string) $bloque['id'],
                            'aparato_id' => $aparatoId,
                            'entrenador_id' => $entrenadorId,
                        ]];

                        $h = $this->heurista(1, $totalGimnastas, $cargaEntrenadores);

                        $nodo = new SesionAStarNode(
                            $asignaciones,
                            $costo,
                            $h,
                            1,
                            60,
                            $cargaEntrenadores,
                            $usoAparatos
                        );

                        $cola->insert($nodo, $nodo->f);
                    }
                }
            }
        }

        $mejorSolucion = null;
        $mejorPuntaje = INF;

        $iteraciones = 0;

        while (!$cola->isEmpty() && $iteraciones < $this->maxIteraciones) {
            $iteraciones++;
            $actual = $cola->extract();

            if ($actual->f >= $mejorPuntaje) {
                continue;
            }

            $puntajeActual = $actual->g;

            if ($actual->profundidad === $totalGimnastas) {
                if ($puntajeActual < $mejorPuntaje) {
                    $mejorPuntaje = $puntajeActual;
                    $mejorSolucion = $actual;
                }
                continue;
            }

            $gimnastasAsignados = array_column($actual->asignaciones, 'gimnasta_id');
            $siguienteGimnasta = null;
            foreach ($gimnastaIds as $gid) {
                if (!in_array($gid, $gimnastasAsignados)) {
                    $siguienteGimnasta = $gid;
                    break;
                }
            }

            if (!$siguienteGimnasta) {
                continue;
            }

            $claveEstado = $actual->estadoKey();
            if (isset($visitados[$claveEstado]) && $visitados[$claveEstado] <= $actual->g) {
                continue;
            }
            $visitados[$claveEstado] = $actual->g;

            foreach ($this->bloques as $bloque) {
                foreach ($this->aparatos as $aparatoId => $aparato) {
                    foreach ($this->entrenadores as $entrenadorId => $entrenador) {
                        $nuevaCarga = $actual->cargaEntrenadores;
                        $nuevaCarga[$entrenadorId] = ($nuevaCarga[$entrenadorId] ?? 0) + 1;

                        $nuevoUso = $actual->usoAparatos;
                        $claveAparato = $bloque['id'] . '_' . $aparatoId;
                        $nuevoUso[$claveAparato] = ($nuevoUso[$claveAparato] ?? 0) + 1;

                        $costoArista = $this->calcularCosto(
                            $siguienteGimnasta,
                            (string) $bloque['id'],
                            $aparatoId,
                            $entrenadorId,
                            $nuevaCarga,
                            $nuevoUso
                        );

                        if ($costoArista >= 1000) {
                            continue;
                        }

                        $nuevoG = $actual->g + $costoArista;
                        $nuevaProfundidad = $actual->profundidad + 1;
                        $nuevoH = $this->heurista($nuevaProfundidad, $totalGimnastas, $nuevaCarga);

                        $nuevaMakespan = max($actual->makespanParcial, ($bloque['id'] + 1) * 60);

                        $nodoSucesor = new SesionAStarNode(
                            array_merge($actual->asignaciones, [[
                                'gimnasta_id' => $siguienteGimnasta,
                                'bloque_id' => (string) $bloque['id'],
                                'aparato_id' => $aparatoId,
                                'entrenador_id' => $entrenadorId,
                            ]]),
                            $nuevoG,
                            $nuevoH,
                            $nuevaProfundidad,
                            $nuevaMakespan,
                            $nuevaCarga,
                            $nuevoUso
                        );

                        $cola->insert($nodoSucesor, $nodoSucesor->f);
                    }
                }
            }
        }

        return $mejorSolucion;
    }

    private function formatearResultado(?SesionAStarNode $nodo, string $fecha): array
    {
        if (!$nodo) {
            return [
                'success' => false,
                'mensaje' => 'No se encontró una asignación válida con las restricciones dadas',
            ];
        }

        $asignacionesDetalle = [];
        $costoTotal = 0;

        foreach ($nodo->asignaciones as $asignacion) {
            $gimnasta = $this->gimnastas[$asignacion['gimnasta_id']] ?? null;
            $entrenador = $this->entrenadores[$asignacion['entrenador_id']] ?? null;
            $aparato = $this->aparatos[$asignacion['aparato_id']] ?? null;
            $bloque = $this->bloques[(int) $asignacion['bloque_id']] ?? null;

            if (!$gimnasta || !$entrenador || !$aparato || !$bloque) {
                continue;
            }

            $costo = $this->calcularCosto(
                $asignacion['gimnasta_id'],
                $asignacion['bloque_id'],
                $asignacion['aparato_id'],
                $asignacion['entrenador_id'],
                $nodo->cargaEntrenadores,
                $nodo->usoAparatos
            );
            $costoTotal += $costo;

            $asignacionesDetalle[] = [
                'gimnasta' => $gimnasta['nombre'],
                'gimnasta_id' => $asignacion['gimnasta_id'],
                'entrenador' => $entrenador['nombre'],
                'entrenador_id' => $asignacion['entrenador_id'],
                'aparato' => $aparato['nombre'],
                'aparato_id' => $asignacion['aparato_id'],
                'hora_inicio' => $bloque['hora_inicio'],
                'hora_fin' => $bloque['hora_fin'],
                'costo' => round($costo, 2),
            ];
        }

        usort($asignacionesDetalle, function ($a, $b) {
            return strcmp($a['hora_inicio'], $b['hora_inicio']);
        });

        return [
            'success' => true,
            'fecha' => $fecha,
            'total_gimnastas_asignados' => count($asignacionesDetalle),
            'makespan_minutos' => $nodo->makespanParcial,
            'costo_total' => round($costoTotal, 2),
            'carga_entrenadores' => array_map(function ($e) use ($nodo) {
                return [
                    'nombre' => $e['nombre'],
                    'sesiones_asignadas' => $nodo->cargaEntrenadores[$e['id']] ?? 0,
                ];
            }, $this->entrenadores),
            'asignaciones' => $asignacionesDetalle,
        ];
    }

    public function guardarAsignaciones(array $resultado, string $fecha): array
    {
        if (!$resultado['success']) {
            return ['guardadas' => 0, 'errores' => []];
        }

        $guardadas = 0;
        $errores = [];

        foreach ($resultado['asignaciones'] as $asignacion) {
            try {
                SesionEntrenamiento::create([
                    'ID_Gimnasta' => $asignacion['gimnasta_id'],
                    'ID_Entrenador' => $asignacion['entrenador_id'],
                    'ID_Aparato' => $asignacion['aparato_id'],
                    'Fecha' => $fecha,
                    'Hora_Inicio' => $asignacion['hora_inicio'],
                    'Hora_Fin' => $asignacion['hora_fin'],
                    'Tipo_Asignacion' => 'Automatica',
                    'Estado' => 'Programada',
                ]);
                $guardadas++;
            } catch (\Exception $e) {
                $errores[] = 'Error asignando ' . $asignacion['gimnasta'] . ': ' . $e->getMessage();
            }
        }

        return ['guardadas' => $guardadas, 'errores' => $errores];
    }
}
