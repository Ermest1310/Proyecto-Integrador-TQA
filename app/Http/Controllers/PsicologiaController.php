<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PsicologiaController extends Controller
{
    /**
     * Algoritmo máximo de Psicología Deportiva aplicada a
     * Gimnasia Artística Masculina de Alto Rendimiento.
     * Respuesta para el chatbot vía /api.
     */
    public function procesar(Request $request)
    {
        $mensaje = strtolower($request->input('mensaje'));
        $usuario = Auth::user();

        //------------------------------------------------------------
        // 1. DETECCIÓN DE TEMA PSICOLÓGICO PRINCIPAL
        //------------------------------------------------------------

        $temas = [
            'ansiedad'          => ['ansiedad', 'nervioso', 'miedo', 'temor', 'preocupado'],
            'motivacion'        => ['motivado', 'ganas', 'desmotivado', 'energía', 'fuerza'],
            'autoconfianza'     => ['confianza', 'dudar', 'seguro', 'fallar', 'autoestima'],
            'lesion'            => ['lesión', 'dolor', 'recuperación', 'miedo a lesionarme'],
            'concentracion'     => ['concentración', 'focus', 'distraído', 'mental'],
            'competencia'       => ['competencia', 'clasificar', 'torneo', 'preparación'],
            'estres'            => ['estres', 'estresado', 'agobiado', 'cansado'],
            'fatiga'            => ['fatiga', 'agotamiento', 'quemado', 'burnout'],
        ];

        $tema_detectado = $this->detectarTema($mensaje, $temas);

        //------------------------------------------------------------
        // 2. ANALÍTICA EMOCIONAL (Escala 1–100)
        //------------------------------------------------------------

        $emocion = $this->analizarEmocion($mensaje);

        //------------------------------------------------------------
        // 3. ANÁLISIS DE ANSIEDAD PRECOMPETITIVA (Modelo CSAI-2)
        //------------------------------------------------------------

        $ansiedad = $this->calcularAnsiedad($mensaje);

        //------------------------------------------------------------
        // 4. ANÁLISIS DE BURNOUT (Modelo de Smith 1986)
        //------------------------------------------------------------

        $burnout = $this->calcularBurnout($mensaje);

        //------------------------------------------------------------
        // 5. SELECCIÓN DEL MÓDULO DE INTERVENCIÓN
        //------------------------------------------------------------

        $intervencion = $this->seleccionarIntervencion(
            $tema_detectado,
            $emocion,
            $ansiedad,
            $burnout
        );

        //------------------------------------------------------------
        // 6. GENERACIÓN DE EJERCICIO PSICOLÓGICO PERSONALIZADO
        //------------------------------------------------------------

        $ejercicio = $this->generarEjercicio($tema_detectado);

        //------------------------------------------------------------
        // 7. OUTPUT FINAL
        //------------------------------------------------------------

        return response()->json([
            'usuario' => $usuario->NombreUsuario ?? 'Gimnasta',
            'tema_detectado' => $tema_detectado,
            'estado_emocional' => $emocion,
            'ansiedad_precompetitiva' => $ansiedad,
            'riesgo_burnout' => $burnout,
            'intervencion' => $intervencion,
            'ejercicio_recomendado' => $ejercicio
        ]);
    }



    // ----------------------------------------------------------------------
    // FUNCIÓN 1: DETECTAR TEMA PSICOLÓGICO
    // ----------------------------------------------------------------------
    private function detectarTema($mensaje, $temas)
    {
        foreach ($temas as $tema => $palabras) {
            foreach ($palabras as $p) {
                if (str_contains($mensaje, $p)) {
                    return $tema;
                }
            }
        }
        return 'general';
    }



    // ----------------------------------------------------------------------
    // FUNCIÓN 2: ANALÍTICA EMOCIONAL (1–100)
    // ----------------------------------------------------------------------
    private function analizarEmocion($mensaje)
    {
        $puntuacion = 50;

        $positivas = ['bien', 'motivado', 'listo', 'fuerte', 'progreso'];
        $negativas = ['mal', 'triste', 'miedo', 'estres', 'cansado', 'ansiedad'];

        foreach ($positivas as $p) {
            if (str_contains($mensaje, $p)) $puntuacion += 5;
        }

        foreach ($negativas as $p) {
            if (str_contains($mensaje, $p)) $puntuacion -= 7;
        }

        return max(1, min(100, $puntuacion));
    }



    // ----------------------------------------------------------------------
    // FUNCIÓN 3: Ansiedad Precompetitiva (CSAI-2 Simplificado)
    // ----------------------------------------------------------------------
    private function calcularAnsiedad($mensaje)
    {
        $cognitiva = 10;
        $somatica  = 10;

        $cognitivos = ['miedo', 'fallar', 'preocupado', 'duda', 'presión'];
        $somaticos  = ['nervioso', 'temblor', 'sudor', 'latidos', 'respirar'];

        foreach ($cognitivos as $c) {
            if (str_contains($mensaje, $c)) $cognitiva += 15;
        }

        foreach ($somaticos as $s) {
            if (str_contains($mensaje, $s)) $somatica += 15;
        }

        return [
            'cognitiva' => min(100, $cognitiva),
            'somatica'  => min(100, $somatica)
        ];
    }



    // ----------------------------------------------------------------------
    // FUNCIÓN 4: Burnout (Smith Model)
    // ----------------------------------------------------------------------
    private function calcularBurnout($mensaje)
    {
        $riesgo = 10;
        $palabras = ['agotado', 'cansado', 'fatiga', 'quemado', 'ya no puedo'];

        foreach ($palabras as $p) {
            if (str_contains($mensaje, $p)) $riesgo += 20;
        }

        return min(100, $riesgo);
    }



    // ----------------------------------------------------------------------
    // FUNCIÓN 5: Intervención psicológica avanzada
    // ----------------------------------------------------------------------
    private function seleccionarIntervencion($tema, $emocion, $ansiedad, $burnout)
    {
        switch ($tema) {
            case 'ansiedad':
                return 'Realiza respiración 4-7-8, visualización del aparato y diálogo interno positivo.';
            case 'motivacion':
                return 'Reconstruye tu propósito deportivo y usa anclajes mentales de victoria.';
            case 'autoconfianza':
                return 'Aplicar técnica de “Evidencia de Éxitos”: recordar 5 logros recientes.';
            case 'lesion':
                return 'Reestructuración cognitiva + exposición progresiva al movimiento.';
            case 'concentracion':
                return 'Aplicar técnica “Círculo de Control” + rutinas de entrada al aparato.';
            case 'competencia':
                return 'Simulación mental de rutina + control respiratorio + música base.';
            case 'estres':
                return 'Técnica de descarga emocional + respiración diafragmática.';
            case 'fatiga':
                return 'Pausa mental + reducción de carga emocional + recuperación guiada.';
        }

        if ($burnout >= 60) {
            return 'Riesgo alto de burnout: reducir cargas, hablar con entrenador y tomar descanso activo.';
        }

        if ($ansiedad['cognitiva'] >= 60) {
            return 'Ansiedad cognitiva alta: usar diálogo interno racional + listas de control.';
        }

        return 'Mantén tu rutina psicológica habitual y escucha a tu cuerpo.';
    }



    // ----------------------------------------------------------------------
    // FUNCIÓN 6: Rutinas mentales por aparato
    // ----------------------------------------------------------------------
    private function generarEjercicio($tema)
    {
        $ejercicios = [
            'ansiedad' =>
                "EJERCICIO: Técnica 4-7-8 + visualización de la rutina perfecta en tu aparato principal.",
            'motivacion' =>
                "EJERCICIO: Escribe 3 razones por las que eres gimnasta y 3 metas semanales.",
            'autoconfianza' =>
                "EJERCICIO: Repetición de logros + construcción de mantra competitivo.",
            'lesion' =>
                "EJERCICIO: Visualización kinestésica + respiración + autoafirmaciones de recuperación.",
            'concentracion' =>
                "EJERCICIO: Técnica del túnel atencional + 15 segundos de respiración cuadrada.",
            'estres' =>
                "EJERCICIO: Desahogo guiado + respiración profunda + grounding 5-4-3-2-1.",
            'fatiga' =>
                "EJERCICIO: Escaneo corporal + micro pausas + recuperación activa.",
            'competencia' =>
                "EJERCICIO: Visualización competitiva + simulación de entrada al aparato."
        ];

        return $ejercicios[$tema] ?? "EJERCICIO GENERAL: Respiración + enfoque mental + autoevaluación del día.";
    }
}
