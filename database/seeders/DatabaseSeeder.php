<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolSeeder::class,
            UsuarioSeeder::class,
            AparatoSeeder::class,
            CompetenciaSeeder::class,
            EntrenamientoSeeder::class,
            LesionSeeder::class,
            CompetenciaGimnastaSeeder::class,
            PostulacionSeeder::class,
            SesionEntrenamientoSeeder::class,
            SolicitudNivelSeeder::class,
        ]);
    }
}
