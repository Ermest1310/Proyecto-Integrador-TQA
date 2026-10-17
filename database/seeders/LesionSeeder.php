<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Lesion;
use App\Models\Usuario;

class LesionSeeder extends Seeder
{
    public function run(): void
    {
        
        $gimnastas = Usuario::whereHas('rol', function($q) {
            $q->where('NombreRol', 'Gimnasta');
        })->where('activo', true)->get();
        
        // Obtener médicos disponibles
        $medicos = Usuario::whereHas('rol', function($q) {
            $q->whereIn('NombreRol', ['Médico', 'Fisioterapeuta']);
        })->where('activo', true)->get();
        
        $lesionesData = [
            ['Esguince', 'Tobillo', 'Ligamentos laterales', 'Leve', 'Reposo y fisioterapia', 14],
            ['Tendinitis', 'Muñeca', 'Tendón flexor', 'Moderada', 'Antiinflamatorios y ejercicios', 21],
            ['Contractura', 'Espalda', 'Músculo trapecio', 'Leve', 'Masajes y estiramientos', 7],
            ['Desgarro', 'Muslo', 'Isquiotibiales', 'Grave', 'Reposo absoluto y rehabilitación', 45],
            ['Luxación', 'Hombro', 'Articulación glenohumeral', 'Grave', 'Inmovilización y cirugía', 90],
            ['Sobrecarga', 'Espalda', 'Zona lumbar', 'Moderada', 'Fisioterapia y fortalecimiento', 30],
            ['Tendinitis', 'Pie', 'Tendón plantar', 'Leve', 'Reposo relativo y ejercicios', 14],
            ['Tendinitis', 'Rodilla', 'Tendón rotuliano', 'Moderada', 'Terapia física especializada', 28],
            ['Esguince', 'Rodilla', 'Ligamento lateral', 'Grave', 'Inmovilización y rehabilitación', 60],
            ['Contractura', 'Cuello', 'Músculo cervical', 'Leve', 'Relajantes y fisioterapia', 10]
        ];
        
        $estados = ['Lesionado', 'En Rehabilitación', 'Recuperada'];

        // Solo algunos gimnastas tienen lesiones (realista)
        $cantidadGimnastas = $gimnastas->count();
        $maxLesionados = min($cantidadGimnastas, 4); // Máximo 4 o todos si hay menos
        $gimnastasConLesiones = $gimnastas->random(rand(2, $maxLesionados));
        
        // Verificar que hay datos para crear lesiones
        if ($gimnastas->isEmpty() || $medicos->isEmpty()) {
            $this->command->info('No hay gimnastas o médicos disponibles para crear lesiones.');
            return;
        }
        
        foreach ($gimnastasConLesiones as $gimnasta) {
            // Algunos pueden tener más de una lesión
            $numLesiones = rand(1, 2);
            
            for ($i = 0; $i < $numLesiones; $i++) {
                $lesionData = $lesionesData[array_rand($lesionesData)];
                $fechaLesion = now()->subDays(rand(1, 180)); // Últimos 6 meses
                $fechaDiagnostico = $fechaLesion->addDays(rand(0, 3)); // Diagnóstico 0-3 días después
                $estado = $estados[array_rand($estados)];
                
                Lesion::create([
                    'ID_Gimnasta' => $gimnasta->ID_Usuario,
                    'ID_Medico' => $medicos->isNotEmpty() ? $medicos->random()->ID_Usuario : null,
                    'Tipo' => $lesionData[0],
                    'ParteCuerpo' => $lesionData[1],
                    'ParteEspecifica' => $lesionData[2],
                    'FechaLesion' => $fechaLesion,
                    'FechaDiagnostico' => $fechaDiagnostico,
                    'Estado' => $estado,
                    'FechaRecuperacion' => $estado === 'Recuperada' ? $fechaLesion->addDays($lesionData[5] + rand(0, 7)) : null,
                    'Gravedad' => $lesionData[3],
                    'Tratamiento' => $lesionData[4],
                    'TiempoRecuperacion' => $lesionData[5],
                    'Observaciones' => 'Lesión típica en gimnasia artística. Seguimiento regular requerido.',
                    'activo' => true
                ]);
            }
        }
    }
}