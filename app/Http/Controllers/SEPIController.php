<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

class SEPIController extends Controller
{
    public function index()
    {
        // Simulación de datos del gimnasta (normalmente vendrían de la BD)
        $gimnasta = [
            'id' => 1,
            'nombre' => 'Boris Arias',
        ];

        // Escoring psicológico (0-10)
        $metricas = [
            'bienestar' => 80,                  // %
            'resiliencia' => 7,                 // 0-10
            'ansiedad' => 3,                     // 0-10
            'ansiedad_porcentaje' => 30,         // %
            'prediccion' => 75                   // %
        ];

        // Reglas expertas simples para generar recomendaciones
        $recomendaciones = [];
        if ($metricas['ansiedad'] >= 7) {
            $recomendaciones[] = [
                'prioridad' => 'Alta',
                'texto' => 'Practicar técnicas de respiración y meditación pre-competitiva.'
            ];
        } elseif ($metricas['ansiedad'] >= 4) {
            $recomendaciones[] = [
                'prioridad' => 'Media',
                'texto' => 'Realizar visualización de rutinas y ejercicios de relajación.'
            ];
        } else {
            $recomendaciones[] = [
                'prioridad' => 'Baja',
                'texto' => 'Mantener la rutina habitual de entrenamiento mental.'
            ];
        }

        if ($metricas['resiliencia'] < 5) {
            $recomendaciones[] = [
                'prioridad' => 'Media',
                'texto' => 'Trabajar en autoconfianza mediante refuerzos positivos y coaching.'
            ];
        }

        // Especialistas
        $especialistas = [
            (object)[
                'Nombre' => 'Dr. Juan Pérez',
                'certificacion' => 'Psicólogo Deportivo Certificado',
                'Descripcion' => 'Especialista en alto rendimiento y manejo de ansiedad.',
                'horario' => 'Lunes a Viernes 9:00-17:00',
                'foto' => null
            ],
            (object)[
                'Nombre' => 'Dra. Ana Gómez',
                'certificacion' => 'Psicóloga Deportiva',
                'Descripcion' => 'Especialista en visualización de rutinas y concentración.',
                'horario' => 'Lunes a Viernes 10:00-16:00',
                'foto' => null
            ],
        ];

        // Tratamientos / Intervenciones
        $tratamientos = [
            (object)[
                'nombre' => 'Meditación Pre-Competitiva',
                'duracion' => '30 min',
                'especialista' => 'Dr. Juan Pérez',
                'modalidad' => 'Presencial'
            ],
            (object)[
                'nombre' => 'Visualización de Rutinas',
                'duracion' => '20 min',
                'especialista' => 'Dra. Ana Gómez',
                'modalidad' => 'Virtual'
            ],
        ];

        // Historial de sesiones
        $historial = [
            (object)[
                'fecha' => Carbon::now()->subDays(7),
                'especialista' => 'Dr. Juan Pérez',
                'tipo_consulta' => 'Meditación',
                'progreso' => 80,
                'notas' => 'El gimnasta mostró buena concentración.'
            ],
            (object)[
                'fecha' => Carbon::now()->subDays(3),
                'especialista' => 'Dra. Ana Gómez',
                'tipo_consulta' => 'Visualización de Rutinas',
                'progreso' => 70,
                'notas' => 'Se recomienda reforzar autoconfianza.'
            ],
        ];

        // Datos para Chart.js
        $fechas = [];
        $valoresBienestar = [];
        foreach ($historial as $sesion) {
            $fechas[] = Carbon::parse($sesion->fecha)->format('d/m');
            $valoresBienestar[] = $sesion->progreso ?? 0;
        }

        return view('psicologia.sepi', compact(
            'gimnasta',
            'metricas',
            'recomendaciones',
            'especialistas',
            'tratamientos',
            'historial',
            'fechas',
            'valoresBienestar'
        ));
    }
}
