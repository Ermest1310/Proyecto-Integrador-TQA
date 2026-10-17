<?php

namespace App\Services;

use App\Models\DescuentosJuez;
use App\Models\DeduccionEjecucion;
use Illuminate\Support\Facades\DB;

class PrediccionDeduccionAIService
{
    private const K_VECINOS = 7;
    private const PESO_DISTANCIA = 2.0;
    private const CONFIANZA_MINIMA = 0.25;
    private const DISTANCIA_SATURACION = 90.0;

    private const ANGULOS_CLAVE = DescuentosJuez::ANGULOS_CLAVE;

    private const FIG_REF = [
        'codo_izq' => 180.0,
        'codo_der' => 180.0,
        'rodilla_izq' => 180.0,
        'rodilla_der' => 180.0,
        'cadera_izq' => 180.0,
        'cadera_der' => 180.0,
        'hombro_izq' => 180.0,
        'hombro_der' => 180.0,
        'tobillo_izq' => 90.0,
        'tobillo_der' => 90.0,
        'tronco_izq' => 180.0,
        'tronco_der' => 180.0,
    ];

    private const PESOS_ARTICULACION = [
        'codo_izq' => 0.8,
        'codo_der' => 0.8,
        'rodilla_izq' => 1.2,
        'rodilla_der' => 1.2,
        'cadera_izq' => 1.5,
        'cadera_der' => 1.5,
        'hombro_izq' => 1.0,
        'hombro_der' => 1.0,
        'tobillo_izq' => 0.7,
        'tobillo_der' => 0.7,
        'tronco_izq' => 1.6,
        'tronco_der' => 1.6,
    ];

    private array $umbralesCalibrados;

    public function __construct()
    {
        $this->umbralesCalibrados = $this->calibrarUmbrales();
    }

    public function predecir(array $angulos, ?int $aparatoId = null, ?string $tipoErrorSugerido = null): array
    {
        try {
            $vector = $this->normalizarEntrada($angulos);

            if ($vector === null) {
                return $this->respuestaFallbackHeuristico($angulos, $aparatoId, 'Vector de ángulos incompleto o inválido');
            }

            $dataset = $this->cargarDataset($aparatoId);

            if (count($dataset) < 3) {
                return $this->respuestaFallbackHeuristico($angulos, $aparatoId, 'Dataset insuficiente: aplicando reglas FIG');
            }

            $vecinos = $this->buscarVecinos($vector, $dataset, $tipoErrorSugerido);

            if (empty($vecinos)) {
                return $this->respuestaFallbackHeuristico($angulos, $aparatoId, 'Sin vecinos dentro del umbral calibrado');
            }

            return $this->agregarVecinos($vecinos, $vector, $aparatoId);
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'mensaje' => 'Error en predicción: ' . $e->getMessage(),
                'deduccion_estimada' => null,
                'confianza' => 0,
                'fuente' => 'error',
            ];
        }
    }

    public function registrarFeedback(
        array $angulos,
        float $descuentoJuez,
        string $tipoError,
        ?int $evaluacionId,
        ?int $aparatoId,
        ?float $descuentoIaPropuesto = null
    ): array {
        try {
            $vector = $this->normalizarEntrada($angulos);

            if ($vector === null) {
                return ['success' => false, 'mensaje' => 'Vector de ángulos inválido para feedback'];
            }

            $corregido = $descuentoIaPropuesto !== null
                && abs($descuentoIaPropuesto - $descuentoJuez) > 0.001;

            $anguloPrincipal = $this->anguloMayorDesviacion($vector);

            $registro = DescuentosJuez::create([
                'ID_EvaluacionJuez' => $evaluacionId,
                'ID_Aparato' => $aparatoId,
                'tipo_error' => $tipoError,
                'descripcion_error' => 'Feedback de aprendizaje continuo del Juez IA',
                'descuento_ia_propuesto' => $descuentoIaPropuesto,
                'confianza_ia' => null,
                'descuento_final' => $descuentoJuez,
                'angulo_detectado' => $anguloPrincipal,
                'angulos_json' => $vector,
                'justificacion' => $corregido
                    ? 'Corrección del juez humano sobre predicción de la IA (' . $descuentoIaPropuesto . ' → ' . $descuentoJuez . ')'
                    : 'Confirmación del juez sobre predicción de la IA',
                'fue_corregido' => $corregido,
            ]);

            $this->umbralesCalibrados = $this->calibrarUmbrales();

            return [
                'success' => true,
                'feedback_id' => $registro->ID_Descuento,
                'fue_corregido' => $corregido,
                'dataset_actual' => DescuentosJuez::contarDatasetEntrenamiento(),
                'mensaje' => $corregido
                    ? 'Corrección registrada: el modelo se ha ajustado con este nuevo patrón'
                    : 'Confirmación registrada: refuerzo positivo para el modelo',
            ];
        } catch (\Throwable $e) {
            return ['success' => false, 'mensaje' => 'Error al registrar feedback: ' . $e->getMessage()];
        }
    }

    public function calibrarUmbrales(): array
    {
        $umbrales = [];
        foreach (['0.10', '0.30', '0.50'] as $valor) {
            $umbrales[$valor] = ['min' => 0.0, 'max' => 180.0, 'n' => 0];
        }

        try {
            $filas = DB::table('descuentos_juez')
                ->whereNotNull('angulo_detectado')
                ->where('angulo_detectado', '>', 0)
                ->get(['descuento_final', 'angulo_detectado']);

            $porValor = [];
            foreach ($filas as $f) {
                $clave = number_format((float) $f->descuento_final, 2);
                if (!isset($porValor[$clave])) {
                    $porValor[$clave] = [];
                }
                $porValor[$clave][] = (float) $f->angulo_detectado;
            }

            foreach ($porValor as $clave => $angulos) {
                if (count($angulos) >= 2) {
                    sort($angulos);
                    $n = count($angulos);
                    $min = $angulos[(int) floor($n * 0.10)];
                    $max = $angulos[(int) ceil($n * 0.90) - 1];
                    $umbrales[$clave] = ['min' => round($min, 2), 'max' => round($max, 2), 'n' => $n];
                }
            }

            $pendientes = DB::table('descuentos_juez')
                ->whereNull('angulos_json')
                ->whereNotNull('angulo_detectado')
                ->where('angulo_detectado', '>', 0)
                ->get(['descuento_final', 'angulo_detectado']);

            foreach ($pendientes as $f) {
                $clave = number_format((float) $f->descuento_final, 2);
                if (!isset($umbrales[$clave]['n'])) {
                    $umbrales[$clave] = ['min' => 0.0, 'max' => 180.0, 'n' => 0];
                }
            }
        } catch (\Throwable $e) {
            foreach (['0.10', '0.30', '0.50'] as $valor) {
                $umbrales[$valor] = ['min' => 0.0, 'max' => 180.0, 'n' => 0, 'error' => $e->getMessage()];
            }
        }

        return $umbrales;
    }

    public function obtenerUmbrales(): array
    {
        return $this->umbralesCalibrados;
    }

    public function obtenerEstadisticasModelo(): array
    {
        $total = DescuentosJuez::count();
        $conAngulos = DescuentosJuez::conAngulos()->count();
        $corregidos = DescuentosJuez::corregidos()->count();

        $porAparato = DescuentosJuez::conAngulos()
            ->selectRaw('ID_Aparato, COUNT(*) as total')
            ->groupBy('ID_Aparato')
            ->pluck('total', 'ID_Aparato');

        $porTipo = DescuentosJuez::conAngulos()
            ->selectRaw('tipo_error, COUNT(*) as total')
            ->groupBy('tipo_error')
            ->pluck('total', 'tipo_error');

        return [
            'total_deducciones' => $total,
            'dataset_entrenamiento' => $conAngulos,
            'correcciones_juez' => $corregidos,
            'precision_aproximada' => $total > 0
                ? round((($total - $corregidos) / $total) * 100, 1)
                : null,
            'muestra_minima_knn' => self::K_VECINOS,
            'por_aparato' => $porAparato,
            'por_tipo_error' => $porTipo,
            'umbrales_calibrados' => $this->umbralesCalibrados,
        ];
    }

    private function normalizarEntrada(array $angulos): ?array
    {
        $vector = [];
        foreach (self::ANGULOS_CLAVE as $clave) {
            $valor = $angulos[$clave] ?? $angulos[str_replace('_', ' ', $clave)] ?? null;
            if ($valor === null || $valor === '' || !is_numeric($valor)) {
                return null;
            }
            $f = (float) $valor;
            if ($f < 0 || $f > 360) {
                return null;
            }
            $vector[$clave] = $f;
        }
        return $vector;
    }

    private function cargarDataset(?int $aparatoId = null): array
    {
        $query = DescuentosJuez::conAngulos()->whereNotNull('descuento_final');

        if ($aparatoId !== null) {
            $mismoAparato = (clone $query)->porAparato($aparatoId)->get();
            if ($mismoAparato->count() >= self::K_VECINOS) {
                return $mismoAparato->all();
            }
        }

        return $query->get()->all();
    }

    private function buscarVecinos(array $vector, array $dataset, ?string $tipoErrorSugerido): array
    {
        $distancias = [];

        foreach ($dataset as $muestra) {
            $mVector = $muestra->vectorAngulosNormalizado();
            $distancia = $this->distanciaEuclidianaPonderada($vector, $mVector);

            $esMismoTipo = $tipoErrorSugerido !== null && $muestra->tipo_error === $tipoErrorSugerido;
            $bonus = $esMismoTipo ? 0.75 : 1.0;

            $distancias[] = [
                'muestra' => $muestra,
                'distancia' => $distancia,
                'peso' => 1.0 / (pow($distancia + 0.001, self::PESO_DISTANCIA) * $bonus),
            ];
        }

        usort($distancias, fn($a, $b) => $a['distancia'] <=> $b['distancia']);

        $umbralCalibrado = $this->umbralDistanciaMaxima($tipoErrorSugerido);
        $filtrados = array_filter($distancias, fn($d) => $d['distancia'] <= $umbralCalibrado);

        if (empty($filtrados)) {
            $filtrados = array_slice($distancias, 0, 3);
        }

        return array_slice(array_values($filtrados), 0, self::K_VECINOS);
    }

    private function distanciaEuclidianaPonderada(array $a, array $b): float
    {
        $suma = 0.0;
        $n = 0;

        foreach (self::ANGULOS_CLAVE as $clave) {
            if (!isset($a[$clave]) || !isset($b[$clave]) || $b[$clave] === null) {
                continue;
            }
            $peso = self::PESOS_ARTICULACION[$clave] ?? 1.0;
            $diff = $a[$clave] - $b[$clave];
            $suma += $peso * $diff * $diff;
            $n++;
        }

        if ($n === 0) {
            return self::DISTANCIA_SATURACION;
        }

        return round(sqrt($suma / $n), 4);
    }

    private function umbralDistanciaMaxima(?string $tipoError): float
    {
        $base = 45.0;

        if ($tipoError !== null) {
            $conteo = DescuentosJuez::conAngulos()->porTipoError($tipoError)->count();
            if ($conteo >= self::K_VECINOS) {
                $base = 60.0;
            }
        }

        $total = DescuentosJuez::conAngulos()->count();
        if ($total >= 50) {
            $base += 10.0;
        } elseif ($total >= 20) {
            $base += 5.0;
        }

        return min($base, self::DISTANCIA_SATURACION);
    }

    private function agregarVecinos(array $vecinos, array $vector, ?int $aparatoId): array
    {
        $pesoTotal = array_sum(array_column($vecinos, 'peso'));
        $acumulado = [];

        foreach ($vecinos as $vecino) {
            $valor = number_format((float) $vecino['muestra']->descuento_final, 2);
            $acumulado[$valor] = ($acumulado[$valor] ?? 0) + $vecino['peso'];
        }

        arsort($acumulado);
        $mejorValor = (float) array_key_first($acumulado);
        $pesoMejor = $acumulado[array_key_first($acumulado)];

        $confianzaAcuerdo = $pesoMejor / max($pesoTotal, 0.0001);

        $distanciaMinima = $vecinos[0]['distancia'];
        $confianzaCercania = max(0, 1 - ($distanciaMinima / self::DISTANCIA_SATURACION));

        $confianza = round(min(0.99, ($confianzaAcuerdo * 0.65 + $confianzaCercania * 0.35)) * 100, 1);

        if ($confianza < self::CONFIANZA_MINIMA * 100) {
            $heuristico = $this->respuestaFallbackHeuristico(
                $vector,
                $aparatoId,
                'Confianza kNN (' . $confianza . '%) por debajo del mínimo, aplicando reglas FIG'
            );
            $heuristico['vecinos_consultados'] = count($vecinos);
            $heuristico['confianza_knn'] = $confianza;
            return $heuristico;
        }

        $desglose = [];
        foreach ($acumulado as $valor => $peso) {
            $desglose[] = [
                'deduccion' => (float) $valor,
                'peso' => round($peso / max($pesoTotal, 0.0001), 4),
            ];
        }

        $masCercano = $vecinos[0]['muestra'];

        return [
            'success' => true,
            'deduccion_estimada' => $mejorValor,
            'confianza' => $confianza,
            'fuente' => 'knn_historico',
            'mensaje' => 'Predicción basada en ' . count($vecinos) . ' muestras etiquetadas por jueces',
            'vecinos_consultados' => count($vecinos),
            'distancia_minima' => $distanciaMinima,
            'desglose_vecinos' => $desglose,
            'tipo_error_sugerido' => $masCercano->tipo_error,
            'elemento_referencia' => $masCercano->nombre_elemento,
            'muestra_mas_cercana' => [
                'id' => $masCercano->ID_Descuento,
                'descuento' => (float) $masCercano->descuento_final,
                'tipo_error' => $masCercano->tipo_error,
                'angulo_detectado' => $masCercano->angulo_detectado !== null ? (float) $masCercano->angulo_detectado : null,
            ],
        ];
    }

    private function respuestaFallbackHeuristico(array $angulos, ?int $aparatoId, string $motivo): array
    {
        $desviaciones = [];
        foreach (self::ANGULOS_CLAVE as $clave) {
            $valor = $angulos[$clave] ?? null;
            if ($valor === null || !is_numeric($valor)) {
                continue;
            }
            $ref = self::FIG_REF[$clave] ?? 180.0;
            $desviaciones[$clave] = abs((float) $valor - $ref);
        }

        if (empty($desviaciones)) {
            return [
                'success' => false,
                'deduccion_estimada' => null,
                'confianza' => 0,
                'fuente' => 'sin_datos',
                'mensaje' => $motivo . ' y sin desviaciones calculables',
            ];
        }

        arsort($desviaciones);
        $peorClave = array_key_first($desviaciones);
        $peorDesviacion = $desviaciones[$peorClave];

        $deduccion = 0.0;
        if ($peorDesviacion > 5) {
            $deduccion = min(0.5, round($peorDesviacion / 20 * 0.1 * 100) / 100);
            $deduccion = max(0.10, $deduccion);
        }

        $reglasAplicables = $this->reglasFigAparato($aparatoId, $deduccion);

        $confianza = $deduccion > 0
            ? round(min(65, 35 + ($peorDesviacion / 4)), 1)
            : 70.0;

        return [
            'success' => true,
            'deduccion_estimada' => $deduccion,
            'confianza' => $confianza,
            'fuente' => 'reglas_fig_heuristico',
            'mensaje' => $motivo,
            'articulo_critico' => $peorClave,
            'desviacion_grados' => round($peorDesviacion, 2),
            'regla_fig' => $reglasAplicables,
        ];
    }

    private function reglasFigAparato(?int $aparatoId, float $deduccion): ?array
    {
        try {
            $query = DeduccionEjecucion::where('Valor_Deduccion', $deduccion);
            if ($aparatoId !== null) {
                $query->where(function ($q) use ($aparatoId) {
                    $q->where('ID_Aparato', $aparatoId)->orWhereNull('ID_Aparato');
                });
            }
            $regla = $query->first();
            if (!$regla) {
                return null;
            }
            return [
                'id' => $regla->ID_Deduccion,
                'tipo' => $regla->Tipo,
                'valor' => (float) $regla->Valor_Deduccion,
                'criterios' => $regla->Criterios,
            ];
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function anguloMayorDesviacion(array $vector): float
    {
        $peor = 0.0;
        $peorRef = 180.0;
        foreach ($vector as $clave => $valor) {
            $ref = self::FIG_REF[$clave] ?? 180.0;
            $dev = abs($valor - $ref);
            if ($dev > $peor) {
                $peor = $dev;
                $peorRef = $ref;
            }
        }
        return round($peorRef + ($peor > 0 ? -($peor / 2) : 0), 2);
    }
}
