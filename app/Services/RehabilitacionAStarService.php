<?php

namespace App\Services;

use App\Models\Lesion;
use App\Models\Gimnasta;
use App\Models\FaseRehabilitacion;
use App\Models\PlanRehabilitacion;
use App\Models\PlanFaseSeguimiento;
use App\Models\EvaluacionFisiologica;
use App\Services\Support\RehabilitacionAStarNode;
use App\Services\Support\AStarPriorityQueue;
use Illuminate\Support\Facades\DB;

class RehabilitacionAStarService
{
    private array $fases;
    private array $transiciones;
    private array $gimnastaInfo;
    private Lesion $lesion;
    private float $factorRiesgo;

    public function __construct()
    {
        $this->factorRiesgo = 1.0;
    }

    public function planificar(int $lesionId, int $medicoId, string $fechaInicio): array
    {
        $this->lesion = Lesion::with(['gimnasta'])->findOrFail($lesionId);

        if ($this->lesion->Estado === 'Recuperada') {
            throw new \InvalidArgumentException("La lesión ya está recuperada");
        }

        $this->gimnastaInfo = $this->cargarHistorialGimnasta((int) $this->lesion->ID_Gimnasta);

        $this->cargarFases();
        $this->transiciones = $this->cargarTransiciones();

        $resultado = $this->buscarRutaOptima();

        if (!$resultado) {
            return [
                'success' => false,
                'mensaje' => 'No se encontró una ruta de rehabilitación válida',
            ];
        }

        $secuenciaFormateada = $this->formatearSecuencia($resultado);

        $planData = [
            'success' => true,
            'lesion' => [
                'id' => $this->lesion->ID_Lesion,
                'tipo' => $this->lesion->Tipo,
                'gravedad' => $this->lesion->Gravedad,
                'parte_cuerpo' => $this->lesion->ParteCuerpo,
            ],
            'gimnasta' => [
                'id' => $this->gimnastaInfo['id'],
                'nombre' => $this->gimnastaInfo['nombre'],
                'nivel' => $this->gimnastaInfo['nivel'],
                'lesiones_previas' => $this->gimnastaInfo['lesiones_previas'],
            ],
            'plan' => $secuenciaFormateada,
        ];

        if ($medicoId && $fechaInicio) {
            $planGuardado = $this->guardarPlan($resultado, $medicoId, $fechaInicio);
            $planData['plan_guardado'] = $planGuardado;
        }

        return $planData;
    }

    private function cargarHistorialGimnasta(int $gimnastaId): array
    {
        $gimnasta = Gimnasta::find($gimnastaId);

        $lesionesPrevias = Lesion::where('ID_Gimnasta', $gimnastaId)
            ->where('ID_Lesion', '!=', $this->lesion->ID_Lesion)
            ->count();

        $planesPrevios = PlanRehabilitacion::where('ID_Gimnasta', $gimnastaId)->count();

        $evaluaciones = EvaluacionFisiologica::where('ID_Gimnasta', $gimnastaId)
            ->orderBy('FechaEvaluacion', 'desc')
            ->first();

        $this->factorRiesgo = 1.0 + ($lesionesPrevias * 0.15) + ($planesPrevios * 0.05);

        return [
            'id' => $gimnastaId,
            'nombre' => $gimnasta->Nombre . ' ' . $gimnasta->Apellido,
            'nivel' => $gimnasta->Nivel,
            'lesiones_previas' => $lesionesPrevias,
            'planes_previos' => $planesPrevios,
            'evaluacion_fisiologica' => $evaluaciones,
        ];
    }

    private function cargarFases(): void
    {
        $this->fases = FaseRehabilitacion::where('activo', true)
            ->orderBy('Orden')
            ->get()
            ->mapWithKeys(function ($f) {
                return [(string) $f->ID_Fase => [
                    'id' => (string) $f->ID_Fase,
                    'nombre' => $f->Nombre,
                    'descripcion' => $f->Descripcion,
                    'orden' => $f->Orden,
                    'duracion_min' => $f->Duracion_Minima_Dias,
                    'duracion_max' => $f->Duracion_Maxima_Dias,
                    'riesgo_recaida' => (float) $f->Riesgo_Recaída,
                    'tipos_validos' => $f->Tipos_Lesion_Validos ?? [],
                    'gravedades_validas' => $f->Gravedades_Validas ?? [],
                    'es_inicial' => $f->Es_Inicial,
                    'es_final' => $f->Es_Final,
                ]];
            })
            ->toArray();
    }

    private function cargarTransiciones(): array
    {
        $transiciones = [];

        foreach ($this->fases as $id => $fase) {
            $faseModel = FaseRehabilitacion::find((int) $id);
            $siguientes = $faseModel->Fases_Siguientes ? json_decode($faseModel->Fases_Siguientes, true) : [];

            $transiciones[$id] = array_map('strval', $siguientes);

            if (empty($transiciones[$id]) && !$fase['es_final']) {
                $ordenActual = $fase['orden'];
                $candidatos = array_filter($this->fases, function ($f) use ($ordenActual, $id) {
                    return $f['orden'] > $ordenActual && $f['id'] !== $id;
                });

                if (!empty($candidatos)) {
                    usort($candidatos, function ($a, $b) {
                        return $a['orden'] <=> $b['orden'];
                    });
                    $transiciones[$id] = [$candidatos[0]['id']];
                }
            }
        }

        return $transiciones;
    }

    private function heurista(int $profundidadActual, int $totalFases): float
    {
        $fasesRestantes = $totalFases - $profundidadActual;

        if ($fasesRestantes <= 0) {
            return 0;
        }

        $fasesOrdenadas = array_values($this->fases);
        usort($fasesOrdenadas, function ($a, $b) {
            return $a['duracion_min'] <=> $b['duracion_min'];
        });

        $diasMinimos = 0;
        for ($i = 0; $i < $fasesRestantes && $i < count($fasesOrdenadas); $i++) {
            $diasMinimos += $fasesOrdenadas[$i]['duracion_min'];
        }

        return $diasMinimos * 0.8;
    }

    private function calcularCosto(string $faseId, int $profundidad): float
    {
        $fase = $this->fases[$faseId];

        if (!$this->faseEsValida($fase)) {
            return INF;
        }

        $diasEstimados = (int) (($fase['duracion_min'] + $fase['duracion_max']) / 2);

        $diasEstimados = (int) ($diasEstimados * $this->factorRiesgo);

        $gravedadFactor = match ($this->lesion->Gravedad) {
            'Leve' => 1.0,
            'Moderada' => 1.3,
            'Grave' => 1.6,
            default => 1.0,
        };

        $diasEstimados = (int) ($diasEstimados * $gravedadFactor);

        $riesgo = $fase['riesgo_recaida'] * $this->factorRiesgo;

        $riesgo *= (1 + $profundidad * 0.1);

        return $diasEstimados + ($riesgo * 100);
    }

    private function faseEsValida(array $fase): bool
    {
        $tiposValidos = $fase['tipos_validos'];
        if (!empty($tiposValidos) && !in_array($this->lesion->Tipo, $tiposValidos)) {
            return false;
        }

        $gravedadesValidas = $fase['gravedades_validas'];
        if (!empty($gravedadesValidas) && !in_array($this->lesion->Gravedad, $gravedadesValidas)) {
            return false;
        }

        return true;
    }

    private function buscarRutaOptima(): ?RehabilitacionAStarNode
    {
        $cola = new AStarPriorityQueue();
        $visitados = [];

        $fasesIniciales = array_filter($this->fases, function ($f) {
            return $f['es_inicial'];
        });

        if (empty($fasesIniciales)) {
            $faseMenorOrden = array_values($this->fases);
            usort($faseMenorOrden, function ($a, $b) {
                return $a['orden'] <=> $b['orden'];
            });
            $fasesIniciales = [$faseMenorOrden[0]];
        }

        $totalFases = count($this->fases);

        foreach ($fasesIniciales as $fase) {
            $costo = $this->calcularCosto($fase['id'], 0);

            if ($costo === INF) {
                continue;
            }

            $diasAcumulados = (int) (($fase['duracion_min'] + $fase['duracion_max']) / 2);
            $riesgoAcumulado = $fase['riesgo_recaida'] * $this->factorRiesgo;

            $h = $this->heurista(1, $totalFases);

            $nodo = new RehabilitacionAStarNode(
                $fase['id'],
                [$fase['id']],
                $costo,
                $h,
                1,
                $diasAcumulados,
                $riesgoAcumulado
            );

            $cola->insert($nodo, $nodo->f);
        }

        $mejorSolucion = null;
        $mejorCosto = INF;

        $iteraciones = 0;
        $maxIteraciones = 10000;

        while (!$cola->isEmpty() && $iteraciones < $maxIteraciones) {
            $iteraciones++;
            $actual = $cola->extract();

            if ($actual->f >= $mejorCosto) {
                continue;
            }

            $ultimaFase = $this->fases[$actual->faseId];

            if ($ultimaFase['es_final']) {
                if ($actual->g < $mejorCosto) {
                    $mejorCosto = $actual->g;
                    $mejorSolucion = $actual;
                }
                continue;
            }

            $claveEstado = $actual->estadoKey();
            if (isset($visitados[$claveEstado]) && $visitados[$claveEstado] <= $actual->g) {
                continue;
            }
            $visitados[$claveEstado] = $actual->g;

            $sucesores = $this->transiciones[$actual->faseId] ?? [];

            foreach ($sucesores as $idSucesor) {
                if (!isset($this->fases[$idSucesor])) {
                    continue;
                }

                if (in_array($idSucesor, $actual->secuencia)) {
                    continue;
                }

                $costoFase = $this->calcularCosto($idSucesor, $actual->profundidad);

                if ($costoFase === INF) {
                    continue;
                }

                $nuevoG = $actual->g + $costoFase;
                $nuevaProfundidad = $actual->profundidad + 1;
                $nuevoH = $this->heurista($nuevaProfundidad, $totalFases);

                $faseSucesora = $this->fases[$idSucesor];
                $diasFase = (int) (($faseSucesora['duracion_min'] + $faseSucesora['duracion_max']) / 2);
                $diasFase = (int) ($diasFase * $this->factorRiesgo);

                $nuevoDiasAcumulados = $actual->diasAcumulados + $diasFase;
                $nuevoRiesgoAcumulado = $actual->riesgoRecaidaAcumulado
                    + ($faseSucesora['riesgo_recaida'] * $this->factorRiesgo);

                $nodoSucesor = new RehabilitacionAStarNode(
                    $idSucesor,
                    array_merge($actual->secuencia, [$idSucesor]),
                    $nuevoG,
                    $nuevoH,
                    $nuevaProfundidad,
                    $nuevoDiasAcumulados,
                    $nuevoRiesgoAcumulado
                );

                $cola->insert($nodoSucesor, $nodoSucesor->f);
            }
        }

        return $mejorSolucion;
    }

    private function formatearSecuencia(RehabilitacionAStarNode $nodo): array
    {
        $secuenciaDetalle = [];
        $diasAcumulados = 0;
        $fechaActual = now();

        foreach ($nodo->secuencia as $index => $id) {
            $fase = $this->fases[$id];
            $diasFase = (int) (($fase['duracion_min'] + $fase['duracion_max']) / 2);
            $diasFase = (int) ($diasFase * $this->factorRiesgo);

            $gravedadFactor = match ($this->lesion->Gravedad) {
                'Leve' => 1.0,
                'Moderada' => 1.3,
                'Grave' => 1.6,
                default => 1.0,
            };
            $diasFase = (int) ($diasFase * $gravedadFactor);

            $fechaInicioFase = $fechaActual->copy()->addDays($diasAcumulados);
            $fechaFinFase = $fechaInicioFase->copy()->addDays($diasFase);

            $diasAcumulados += $diasFase;

            $secuenciaDetalle[] = [
                'orden' => $index + 1,
                'fase_id' => $id,
                'nombre' => $fase['nombre'],
                'descripcion' => $fase['descripcion'],
                'duracion_dias' => $diasFase,
                'fecha_inicio_estimada' => $fechaInicioFase->format('Y-m-d'),
                'fecha_fin_estimada' => $fechaFinFase->format('Y-m-d'),
                'riesgo_recaida' => round($fase['riesgo_recaida'] * $this->factorRiesgo, 4),
                'es_final' => $fase['es_final'],
            ];
        }

        return [
            'total_fases' => count($secuenciaDetalle),
            'duracion_total_dias' => $diasAcumulados,
            'fecha_inicio' => now()->format('Y-m-d'),
            'fecha_fin_estimada' => now()->addDays($diasAcumulados)->format('Y-m-d'),
            'riesgo_recaida_total' => round($nodo->riesgoRecaidaAcumulado, 4),
            'factor_riesgo_aplicado' => round($this->factorRiesgo, 2),
            'secuencia' => $secuenciaDetalle,
        ];
    }

    private function guardarPlan(RehabilitacionAStarNode $nodo, int $medicoId, string $fechaInicio): array
    {
        return DB::transaction(function () use ($nodo, $medicoId, $fechaInicio) {
            $plan = PlanRehabilitacion::create([
                'ID_Lesion' => $this->lesion->ID_Lesion,
                'ID_Gimnasta' => $this->lesion->ID_Gimnasta,
                'ID_Medico' => $medicoId,
                'Fecha_Inicio' => $fechaInicio,
                'Fecha_Estimada_Fin' => now()->addDays($nodo->diasAcumulados)->format('Y-m-d'),
                'Duracion_Estimada_Dias' => $nodo->diasAcumulados,
                'Riesgo_Recaída_Total' => $nodo->riesgoRecaidaAcumulado,
                'Secuencia_Fases' => $nodo->secuencia,
                'Estado' => 'En Curso',
            ]);

            foreach ($nodo->secuencia as $index => $idFase) {
                $fase = $this->fases[$idFase];
                $diasFase = (int) (($fase['duracion_min'] + $fase['duracion_max']) / 2);
                $diasFase = (int) ($diasFase * $this->factorRiesgo);

                $fechaInicioFase = now()->addDays($index * $diasFase);
                $fechaFinFase = $fechaInicioFase->copy()->addDays($diasFase);

                PlanFaseSeguimiento::create([
                    'ID_Plan' => $plan->ID_Plan,
                    'ID_Fase' => (int) $idFase,
                    'Orden' => $index + 1,
                    'Fecha_Inicio' => $fechaInicioFase->format('Y-m-d'),
                    'Fecha_Fin' => $fechaFinFase->format('Y-m-d'),
                    'Estado' => $index === 0 ? 'En Curso' : 'Pendiente',
                    'Riesgo_Recaída_Fase' => $fase['riesgo_recaida'] * $this->factorRiesgo,
                ]);
            }

            $this->lesion->update(['Estado' => 'En Rehabilitación']);

            return [
                'plan_id' => $plan->ID_Plan,
                'fases_registradas' => count($nodo->secuencia),
                'fecha_inicio' => $fechaInicio,
                'fecha_fin_estimada' => $plan->Fecha_Estimada_Fin->format('Y-m-d'),
            ];
        });
    }
}
