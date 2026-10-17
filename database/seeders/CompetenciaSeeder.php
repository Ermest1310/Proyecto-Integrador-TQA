<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Competencia;

class CompetenciaSeeder extends Seeder
{
    public function run(): void
    {
        $competencias = [
            [
                'NombreCompetencia' => 'Copa Nacional de Gimnasia 2024',
                'Fecha' => '2024-03-15',
                'Ubicacion' => 'Palacio de los Deportes, Ciudad de México',
                'Descripcion' => 'Competencia nacional clasificatoria para campeonatos internacionales'
            ],
            [
                'NombreCompetencia' => 'Torneo Estatal Juvenil',
                'Fecha' => '2024-04-20',
                'Ubicacion' => 'Gimnasio Municipal, Guadalajara',
                'Descripcion' => 'Competencia para gimnastas juveniles del estado'
            ],
            [
                'NombreCompetencia' => 'Festival Internacional de Gimnasia',
                'Fecha' => '2024-05-10',
                'Ubicacion' => 'Centro de Convenciones, Monterrey',
                'Descripcion' => 'Festival con participación de gimnastas internacionales'
            ],
            [
                'NombreCompetencia' => 'Campeonato Regional Norte',
                'Fecha' => '2024-06-08',
                'Ubicacion' => 'Polideportivo Norte, Tijuana',
                'Descripcion' => 'Competencia regional para la zona norte del país'
            ],
            [
                'NombreCompetencia' => 'Copa Centroamericana',
                'Fecha' => '2024-07-22',
                'Ubicacion' => 'Arena Deportiva, San José, Costa Rica',
                'Descripcion' => 'Competencia internacional centroamericana'
            ],
            [
                'NombreCompetencia' => 'Torneo de Verano Elite',
                'Fecha' => '2024-08-12',
                'Ubicacion' => 'Complejo Olímpico, Puebla',
                'Descripcion' => 'Competencia de alto nivel para gimnastas elite'
            ]
        ];

        foreach ($competencias as $comp) {
            Competencia::create([
                'NombreCompetencia' => $comp['NombreCompetencia'],
                'Fecha' => $comp['Fecha'],
                'Ubicacion' => $comp['Ubicacion'],
                'Descripcion' => $comp['Descripcion'],
                'activo' => true
            ]);
        }
    }
}