<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario;
use App\Models\Rol;

class UsuarioSeeder extends Seeder
{
    public function run(): void
    {
        $roles = Rol::all()->keyBy('NombreRol');

        // Administrador
        $admin = Usuario::create([
            'NombreUsuario' => 'admin@gym.com',
            'Email' => 'admin@gym.com',
            'CedulaIdentidad' => '12345678',
            'Contraseña' => bcrypt('12345678'),
            'Nombre' => 'Rodrigo',
            'Apellido' => 'Borgia',
            'Nacionalidad' => 'Venezuela',
            'ID_Rol' => $roles['Administrador']->ID_Rol,
            'activo' => true
        ]);

        // Entrenadores
        $entrenadores = [
            ['entrenador1@gym.com', '87654321', 'Miguel', 'Rodriguez', 'Colombia', '+57-300-1234567'],
            ['entrenador2@gym.com', '11223344', 'Laura', 'Garcia', 'Perú', '+51-999-2345678'],
            ['entrenador3@gym.com', '55667788', 'Carlos', 'Lopez', 'Chile', '+56-9-3456789']
        ];

        $entrenadorObjs = [];
        foreach ($entrenadores as $ent) {
            $entrenadorObjs[] = Usuario::create([
                'NombreUsuario' => $ent[0],
                'Email' => $ent[0],
                'CedulaIdentidad' => $ent[1],
                'Contraseña' => bcrypt($ent[1]),
                'Nombre' => $ent[2],
                'Apellido' => $ent[3],
                'Nacionalidad' => $ent[4],
                'Contacto' => $ent[5],
                'ID_Rol' => $roles['Entrenador']->ID_Rol,
                'activo' => true
            ]);
        }

        // Personal médico
        $medicos = [
            ['medico1@gym.com', '22334455', 'Elena', 'Martinez', 'México', '+52-555-4567890', 'Médico'],
            ['medico2@gym.com', '33445566', 'Ricardo', 'Santos', 'Argentina', '+54-11-5678901', 'Médico'],
            ['fisio1@gym.com', '44556677', 'Ana', 'Morales', 'Bolivia', '+591-7-6789012', 'Fisioterapeuta'],
            ['fisio2@gym.com', '66778899', 'Pedro', 'Silva', 'Ecuador', '+593-9-7890123', 'Fisioterapeuta']
        ];

        foreach ($medicos as $med) {
            Usuario::create([
                'NombreUsuario' => $med[0],
                'Email' => $med[0],
                'CedulaIdentidad' => $med[1],
                'Contraseña' => bcrypt($med[1]),
                'Nombre' => $med[2],
                'Apellido' => $med[3],
                'Nacionalidad' => $med[4],
                'Contacto' => $med[5],
                'ID_Rol' => $roles[$med[6]]->ID_Rol,
                'activo' => true
            ]);
        }

        // Gimnastas
        $gimnastas = [
            ['gimnasta1@gym.com', '11111111', 'Sofia', 'Ramirez', '2001-03-12', 'Bolivia', 48.5, 1.58, 'Principiante'],
            ['gimnasta2@gym.com', '22222222', 'Valeria', 'Mendez', '1999-11-08', 'Colombia', 51.2, 1.62, 'Intermedio'],
            ['gimnasta3@gym.com', '33333333', 'Camila', 'Herrera', '2002-07-22', 'Chile', 53.8, 1.65, 'Principiante'],
            ['gimnasta4@gym.com', '44444444', 'Isabella', 'Cruz', '2000-09-15', 'Perú', 49.7, 1.60, 'Intermedio'],
            ['gimnasta5@gym.com', '55555555', 'Natalia', 'Silva', '2001-12-03', 'Brasil', 52.3, 1.63, 'Principiante'],
            ['gimnasta6@gym.com', '66666666', 'Martina', 'Lopez', '2003-05-18', 'Argentina', 50.0, 1.60, 'Avanzado'],
            ['gimnasta7@gym.com', '77777777', 'Daniela', 'Torres', '2002-11-25', 'Uruguay', 49.5, 1.59, 'Elite']
        ];

        foreach ($gimnastas as $gim) {
            Usuario::create([
                'NombreUsuario' => $gim[0],
                'Email' => $gim[0],
                'CedulaIdentidad' => $gim[1],
                'Contraseña' => bcrypt($gim[1]),
                'Nombre' => $gim[2],
                'Apellido' => $gim[3],
                'FechaNacimiento' => $gim[4],
                'Nacionalidad' => $gim[5],
                'Peso' => $gim[6],
                'Altura' => $gim[7],
                'Nivel' => $gim[8],
                'ID_Rol' => $roles['Gimnasta']->ID_Rol,
                'activo' => true
            ]);
        }
    }
}