<?php

namespace App\Http\Controllers;

use App\Services\PrediccionDeduccionAIService;
use Illuminate\Http\Request;

class PrediccionJuezController extends Controller
{
    private PrediccionDeduccionAIService $servicio;

    public function __construct(PrediccionDeduccionAIService $servicio)
    {
        $this->servicio = $servicio;
    }

    public function predecirDeduccion(Request $request)
    {
        $request->validate([
            'angulos' => 'required|array',
            'angulos.*' => 'nullable|numeric|min:0|max:360',
            'ID_Aparato' => 'nullable|integer|exists:aparatos,ID_Aparato',
            'tipo_error' => 'nullable|string|in:caida,paso,perdida_equilibrio,separacion_linea,falta_tecnica,otro',
        ]);

        try {
            $angulos = $request->input('angulos');

            if (!is_array($angulos) || count($angulos) < 12) {
                return response()->json([
                    'success' => false,
                    'mensaje' => 'Se requieren los 12 ángulos clave para la predicción',
                    'recibidos' => is_array($angulos) ? count($angulos) : 0,
                ], 422);
            }

            $resultado = $this->servicio->predecir(
                $angulos,
                $request->input('ID_Aparato'),
                $request->input('tipo_error')
            );

            return response()->json($resultado);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'mensaje' => 'Error interno de predicción: ' . $e->getMessage(),
                'deduccion_estimada' => null,
                'confianza' => 0,
                'fuente' => 'error',
            ], 500);
        }
    }

    public function registrarFeedback(Request $request)
    {
        $request->validate([
            'angulos' => 'required|array',
            'angulos.*' => 'nullable|numeric|min:0|max:360',
            'descuento_juez' => 'required|numeric|min:0|max:10',
            'tipo_error' => 'required|string|in:caida,paso,perdida_equilibrio,separacion_linea,falta_tecnica,otro',
            'ID_EvaluacionJuez' => 'nullable|integer|exists:evaluaciones_juez,ID_EvaluacionJuez',
            'ID_Aparato' => 'nullable|integer|exists:aparatos,ID_Aparato',
            'descuento_ia_propuesto' => 'nullable|numeric|min:0|max:10',
            'nombre_elemento' => 'nullable|string|max:100',
            'descripcion_error' => 'nullable|string|max:500',
            'justificacion' => 'nullable|string|max:500',
            'angulo_detectado' => 'nullable|numeric',
            'ID_Elemento' => 'nullable|integer',
        ]);

        try {
            $angulos = $request->input('angulos');

            if (!is_array($angulos) || count($angulos) < 12) {
                return response()->json([
                    'success' => false,
                    'mensaje' => 'Se requieren los 12 ángulos clave para registrar el feedback',
                ], 422);
            }

            $resultado = $this->servicio->registrarFeedback(
                $angulos,
                (float) $request->input('descuento_juez'),
                $request->input('tipo_error'),
                $request->input('ID_EvaluacionJuez'),
                $request->input('ID_Aparato'),
                $request->input('descuento_ia_propuesto') !== null
                    ? (float) $request->input('descuento_ia_propuesto')
                    : null
            );

            if ($resultado['success'] && $request->input('ID_EvaluacionJuez')) {
                $this->persistirDeduccionEnEvaluacion($request);
            }

            return response()->json($resultado, $resultado['success'] ? 200 : 422);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'mensaje' => 'Error al registrar feedback: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function estadisticasModelo()
    {
        try {
            return response()->json([
                'success' => true,
                'estadisticas' => $this->servicio->obtenerEstadisticasModelo(),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'mensaje' => 'Error al obtener estadísticas: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function persistirDeduccionEnEvaluacion(Request $request): void
    {
        $existe = \App\Models\DescuentosJuez::where('ID_EvaluacionJuez', $request->input('ID_EvaluacionJuez'))
            ->where('tipo_error', $request->input('tipo_error'))
            ->where('created_at', '>', now()->subSeconds(5))
            ->exists();

        if ($existe) {
            return;
        }

        \App\Models\DescuentosJuez::create([
            'ID_EvaluacionJuez' => $request->input('ID_EvaluacionJuez'),
            'ID_Elemento' => $request->input('ID_Elemento'),
            'ID_Aparato' => $request->input('ID_Aparato'),
            'nombre_elemento' => $request->input('nombre_elemento'),
            'tipo_error' => $request->input('tipo_error'),
            'descripcion_error' => $request->input('descripcion_error') ?: 'Deducción registrada con feedback de aprendizaje',
            'descuento_ia_propuesto' => $request->input('descuento_ia_propuesto'),
            'descuento_final' => $request->input('descuento_juez'),
            'angulo_detectado' => $request->input('angulo_detectado'),
            'angulos_json' => $request->input('angulos'),
            'justificacion' => $request->input('justificacion'),
            'fue_corregido' => $request->input('descuento_ia_propuesto') !== null
                && abs((float) $request->input('descuento_ia_propuesto') - (float) $request->input('descuento_juez')) > 0.001,
        ]);
    }
}
