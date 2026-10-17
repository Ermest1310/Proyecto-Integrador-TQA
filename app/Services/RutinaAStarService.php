<?php

namespace App\Services;

use App\Models\Aparato;
use App\Models\Gimnasta;
use App\Models\ElementoRutina;
use App\Models\CalificacionElemento;
use App\Models\Lesion;
use App\Services\Support\AStarNode;
use App\Services\Support\AStarPriorityQueue;

class RutinaAStarService
{
    private int $maxElementos;
    private array $elementos;
    private array $transiciones;
    private array $tasasExito;
    private array $penalizacionesLesion;
    private array $cacheMejoresValores;

    public function __construct(int $maxElementos = 10)
    {
        $this->maxElementos = $maxElementos;
        $this->cacheMejoresValores = [];
    }

    public function planificar(int $gimnastaId, int $aparatoId): array
    {
        $gimnasta = Gimnasta::with(['evaluacionesFisiologicas', 'lesiones' => function ($q) {
            $q->where('Estado', 'Lesionado');
        }])->find($gimnastaId);

        if (!$gimnasta) {
            throw new \InvalidArgumentException("Gimnasta no encontrado");
        }

        $aparato = Aparato::find($aparatoId);
        if (!$aparato) {
            throw new \InvalidArgumentException("Aparato no encontrado");
        }

        $this->cargarElementos($aparatoId);
        $this->cargarTransiciones($aparatoId);
        $this->cargarTasasExito($gimnastaId, $aparatoId);
        $this->cargarPenalizacionesLesion($gimnasta);

        $this->precalcularMejoresValores();

        $resultado = $this->buscarRutinaOptima();

        return $this->formatearResultado($resultado, $gimnasta, $aparato);
    }

    private function cargarElementos(int $aparatoId): void
    {
        $this->elementos = ElementoRutina::where('ID_Aparato', $aparatoId)
            ->where('activo', true)
            ->get()
            ->mapWithKeys(function ($e) {
                return [(string) $e->ID_Elemento => [
                    'id' => (string) $e->ID_Elemento,
                    'nombre' => $e->Nombre,
                    'dificultad' => (float) $e->ValorDificultad,
                    'grupo' => $e->Grupo ?? 'A',
                    'minEnlace' => (int) ($e->MinEnlace ?? 0),
                ]];
            })
            ->toArray();
    }

    private function cargarTransiciones(int $aparatoId): void
    {
        $this->transiciones = [];
        $elementos = ElementoRutina::where('ID_Aparato', $aparatoId)->get();

        foreach ($elementos as $origen) {
            $idsPermitidos = $origen->IDsConexion ? json_decode($origen->IDsConexion, true) : [];
            $this->transiciones[(string) $origen->ID_Elemento] = array_map('strval', $idsPermitidos);
        }
    }

    private function cargarTasasExito(int $gimnastaId, int $aparatoId): void
    {
        $calificaciones = CalificacionElemento::where('ID_Gimnasta', $gimnastaId)
            ->whereHas('elementoRutina', function ($q) use ($aparatoId) {
                $q->where('ID_Aparato', $aparatoId);
            })
            ->get();

        $this->tasasExito = $calificaciones->mapWithKeys(function ($c) {
            return [(string) $c->ID_Elemento => (float) $c->TasaExito];
        })->toArray();
    }

    private function cargarPenalizacionesLesion(Gimnasta $gimnasta): void
    {
        $this->penalizacionesLesion = [];

        foreach ($gimnasta->lesiones as $lesion) {
            $partesAfectadas = json_decode($lesion->PartesAfectadas, true) ?? [];

            foreach ($this->elementos as $id => $elemento) {
                $penalesPorGrupo = [
                    'A' => 0.02,
                    'B' => 0.05,
                    'C' => 0.08,
                    'D' => 0.12,
                    'E' => 0.15,
                    'F' => 0.18,
                ];

                $penalizacionBase = $penalesPorGrupo[$elemento['grupo']] ?? 0.05;

                if (in_array($partesAfectadas, ['core', 'tronco']) && in_array($elemento['grupo'], ['D', 'E', 'F'])) {
                    $penalizacionBase *= 2.5;
                }

                $this->penalizacionesLesion[$id] = ($this->penalizacionesLesion[$id] ?? 0) + $penalizacionBase;
            }
        }
    }

    private function precalcularMejoresValores(): void
    {
        $valores = array_column($this->elementos, 'dificultad');
        rsort($valores);

        for ($i = 0; $i <= $this->maxElementos; $i++) {
            $this->cacheMejoresValores[$i] = array_sum(array_slice($valores, 0, $i));
        }
    }

    private function heurista(int $profundidadActual): float
    {
        $restantes = $this->maxElementos - $profundidadActual;
        if ($restantes <= 0) {
            return 0;
        }
        return $this->cacheMejoresValores[$restantes];
    }

    private function obtenerSucesores(AStarNode $nodo): array
    {
        $ultimoElemento = end($nodo->secuencia);
        $idsValidos = $this->transiciones[$ultimoElemento] ?? [];

        $sucesores = [];
        foreach ($idsValidos as $id) {
            if (!isset($this->elementos[$id])) {
                continue;
            }
            if (in_array($id, $nodo->secuencia)) {
                continue;
            }
            $sucesores[] = $id;
        }

        return $sucesores;
    }

    private function calcularCosto(string $elementoId, int $profundidad): float
    {
        $elemento = $this->elementos[$elementoId];
        $dificultad = $elemento['dificultad'];

        $penalizacionEjecucion = 0.0;
        $tasaExito = $this->tasasExito[$elementoId] ?? 0.75;
        $penalizacionEjecucion += (1 - $tasaExito) * 0.3;

        $penalizacionEjecucion += ($this->penalizacionesLesion[$elementoId] ?? 0);

        $factorFatiga = 1 + ($profundidad * 0.02);
        $penalizacionEjecucion *= $factorFatiga;

        return $dificultad - $penalizacionEjecucion;
    }

    private function buscarRutinaOptima(): ?AStarNode
    {
        $cola = new AStarPriorityQueue();
        $visitados = [];

        foreach ($this->elementos as $id => $elemento) {
            if (($elemento['minEnlace'] ?? 0) > 0) {
                continue;
            }

            $g = $this->calcularCosto($id, 0);
            $secuencia = [$id];
            $h = $this->heurista(1);

            $nodo = new AStarNode($id, $secuencia, $g, $h, 1, $g, 0);
            $cola->insert($nodo, $nodo->f);
        }

        $mejorSolucion = null;
        $mejorPuntaje = -INF;

        $iteraciones = 0;
        $maxIteraciones = 50000;

        while (!$cola->isEmpty() && $iteraciones < $maxIteraciones) {
            $iteraciones++;
            $actual = $cola->extract();

            if ($actual->f < $mejorPuntaje) {
                continue;
            }

            $puntajeActual = $actual->valorDificultadAcumulado - $actual->penalizacionAcumulada;

            if ($puntajeActual > $mejorPuntaje) {
                $mejorPuntaje = $puntajeActual;
                $mejorSolucion = $actual;
            }

            if ($actual->profundidad >= $this->maxElementos) {
                continue;
            }

            $claveEstado = $actual->estadoKey();
            if (isset($visitados[$claveEstado]) && $visitados[$claveEstado] >= $actual->g) {
                continue;
            }
            $visitados[$claveEstado] = $actual->g;

            $sucesores = $this->obtenerSucesores($actual);

            foreach ($sucesores as $idSucesor) {
                $costoArista = $this->calcularCosto($idSucesor, $actual->profundidad);
                $nuevoG = $actual->g + $costoArista;
                $nuevaSecuencia = array_merge($actual->secuencia, [$idSucesor]);
                $nuevaProfundidad = $actual->profundidad + 1;
                $nuevoH = $this->heurista($nuevaProfundidad);

                $penalizacionAcumulada = $actual->penalizacionAcumulada
                    + max(0, -$costoArista);

                $nodoSucesor = new AStarNode(
                    $idSucesor,
                    $nuevaSecuencia,
                    $nuevoG,
                    $nuevoH,
                    $nuevaProfundidad,
                    $actual->valorDificultadAcumulado + $this->elementos[$idSucesor]['dificultad'],
                    $penalizacionAcumulada
                );

                $cola->insert($nodoSucesor, $nodoSucesor->f);
            }
        }

        return $mejorSolucion;
    }

    private function formatearResultado(?AStarNode $nodo, Gimnasta $gimnasta, Aparato $aparato): array
    {
        if (!$nodo) {
            return [
                'success' => false,
                'mensaje' => 'No se encontró una rutina válida',
            ];
        }

        $elementosDetalle = [];
        $puntajeTotal = 0;

        foreach ($nodo->secuencia as $index => $id) {
            $elemento = $this->elementos[$id];
            $costo = $this->calcularCosto($id, $index);
            $puntajeTotal += $costo;

            $elementosDetalle[] = [
                'orden' => $index + 1,
                'elemento_id' => $id,
                'nombre' => $elemento['nombre'],
                'grupo' => $elemento['grupo'],
                'valor_dificultad' => $elemento['dificultad'],
                'tasa_exito_historica' => $this->tasasExito[$id] ?? null,
                'penalizacion_lesion' => $this->penalizacionesLesion[$id] ?? 0,
                'puntaje_contribucion' => round($costo, 3),
            ];
        }

        return [
            'success' => true,
            'gimnasta' => [
                'id' => $gimnasta->ID_Gimnasta,
                'nombre' => $gimnasta->Nombre . ' ' . $gimnasta->Apellido,
                'nivel' => $gimnasta->Nivel,
            ],
            'aparato' => [
                'id' => $aparato->ID_Aparato,
                'nombre' => $aparato->Nombre,
            ],
            'rutina_optima' => [
                'secuencia' => $elementosDetalle,
                'total_elementos' => count($elementosDetalle),
                'valor_dificultad_total' => round($nodo->valorDificultadAcumulado, 3),
                'penalizacion_total' => round($nodo->penalizacionAcumulada, 3),
                'puntaje_esperado' => round($nodo->valorDificultadAcumulado - $nodo->penalizacionAcumulada, 3),
            ],
        ];
    }
}
