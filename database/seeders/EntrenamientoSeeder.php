<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Entrenamiento;
use App\Models\Usuario;
use App\Models\Aparato;
use Carbon\Carbon;

class EntrenamientoSeeder extends Seeder
{
    public function run(): void
    {
        $gimnastas = Usuario::whereHas('rol', function($q) {
            $q->where('NombreRol', 'Gimnasta');
        })->where('activo', true)->get();
        $aparatos = Aparato::all();
        
        $observaciones = [
            'Excelente técnica en saltos',
            'Mejorar flexibilidad en piernas',
            'Trabajar más en equilibrio',
            'Buena coordinación general',
            'Necesita fortalecer core',
            'Progreso notable en fuerza',
            'Concentrarse en la postura',
            'Muy buena sesión de entrenamiento',
            'Trabajar expresión corporal',
            'Mejorar sincronización musical'
        ];

        // Generar entrenamientos de las últimas 4 semanas
        for ($semana = 0; $semana < 4; $semana++) {
            foreach ($gimnastas as $gimnasta) {
                // 3-4 entrenamientos por semana por gimnasta
                $entrenamientosPorSemana = rand(3, 4);
                
                for ($i = 0; $i < $entrenamientosPorSemana; $i++) {
                    $fecha = Carbon::now()->subWeeks($semana)->subDays(rand(0, 6));
                    
                    // Duración según nivel
                    $duracion = match($gimnasta->Nivel) {
                        'Elite' => rand(180, 240), // 3-4 horas
                        'Avanzado' => rand(120, 180), // 2-3 horas
                        'Intermedio' => rand(90, 120), // 1.5-2 horas
                        'Principiante' => rand(60, 90), // 1-1.5 horas
                        default => rand(90, 120)
                    };

                    Entrenamiento::create([
                        'ID_Gimnasta' => $gimnasta->ID_Usuario,
                        'ID_Aparato' => $aparatos->random()->ID_Aparato,
                        'Fecha' => $fecha->format('Y-m-d'),
                        'Duracion' => $duracion,
                        'Observaciones' => $observaciones[array_rand($observaciones)],
                        'activo' => true
                    ]);
                }
            }
        }
    }
}