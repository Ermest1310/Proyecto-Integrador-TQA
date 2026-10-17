<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CompetenciaGimnasta;
use App\Models\Usuario;
use App\Models\Competencia;

class CompetenciaGimnastaSeeder extends Seeder
{
    public function run(): void
    {
        $gimnastas = Usuario::whereHas('rol', function($q) {
            $q->where('NombreRol', 'Gimnasta');
        })->where('activo', true)->get();
        $competencias = Competencia::all();
        
        foreach ($competencias as $competencia) {
            // Seleccionar gimnastas según el tipo de competencia
            $participantes = match(true) {
                str_contains($competencia->NombreCompetencia, 'Elite') => 
                    $gimnastas->where('Nivel', 'Elite'),
                str_contains($competencia->NombreCompetencia, 'Nacional') => 
                    $gimnastas->whereIn('Nivel', ['Elite', 'Avanzado']),
                str_contains($competencia->NombreCompetencia, 'Juvenil') => 
                    $gimnastas->whereIn('Nivel', ['Intermedio', 'Principiante']),
                default => $gimnastas->whereIn('Nivel', ['Elite', 'Avanzado', 'Intermedio'])
            };
            
            // Inscribir entre 3-8 gimnastas por competencia
            $numParticipantes = min(rand(3, 8), $participantes->count());
            $gimnastaSeleccionadas = $participantes->random($numParticipantes);
            
            foreach ($gimnastaSeleccionadas as $gimnasta) {
                CompetenciaGimnasta::create([
                    'ID_Competencia' => $competencia->ID_Competencia,
                    'ID_Gimnasta' => $gimnasta->ID_Usuario
                ]);
            }
        }
    }
}