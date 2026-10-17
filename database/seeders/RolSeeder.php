<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rol;

class RolSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'Administrador',
            'Gimnasta', 
            'Entrenador',
            'Médico',
            'Fisioterapeuta',
            'Juez'
        ];

        foreach ($roles as $rol) {
            Rol::create([
                'NombreRol' => $rol,
                'activo' => true
            ]);
        }
    }
}