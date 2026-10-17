<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Postulacion;

class PostulacionSeeder extends Seeder
{
    public function run()
    {
        $postulaciones = [
            ['Email' => 'maria.gonzalez@email.com', 'Nombre' => 'María', 'Apellido' => 'González', 'FechaNacimiento' => '2010-05-15', 'Nacionalidad' => 'Venezuela', 'CedulaIdentidad' => 'V-12345678', 'Peso' => 45.5, 'Altura' => 1.55, 'IMC' => 18.9, 'Edad' => 14, 'Nivel' => 'Principiante', 'estado' => 'pendiente'],
            ['Email' => 'carlos.rodriguez@email.com', 'Nombre' => 'Carlos', 'Apellido' => 'Rodríguez', 'FechaNacimiento' => '2008-08-22', 'Nacionalidad' => 'Colombia', 'CedulaIdentidad' => '87654321', 'Peso' => 52.0, 'Altura' => 1.62, 'IMC' => 19.8, 'Edad' => 16, 'Nivel' => 'Intermedio', 'estado' => 'aprobada', 'fecha_revision' => now()->subDays(2)],
            ['Email' => 'ana.martinez@email.com', 'Nombre' => 'Ana', 'Apellido' => 'Martínez', 'FechaNacimiento' => '2012-03-10', 'Nacionalidad' => 'España', 'CedulaIdentidad' => '12345678A', 'Peso' => 38.0, 'Altura' => 1.45, 'IMC' => 18.1, 'Edad' => 12, 'Nivel' => 'Principiante', 'estado' => 'rechazada', 'comentarios' => 'Edad muy temprana, recomendamos esperar 1 año más', 'fecha_revision' => now()->subDays(1)],
            ['Email' => 'lucia.fernandez@email.com', 'Nombre' => 'Lucía', 'Apellido' => 'Fernández', 'FechaNacimiento' => '2009-12-08', 'Nacionalidad' => 'México', 'CedulaIdentidad' => 'FERL091208MDFRNC05', 'Peso' => 48.0, 'Altura' => 1.58, 'IMC' => 19.2, 'Edad' => 15, 'Nivel' => 'Intermedio', 'estado' => 'pendiente'],
            ['Email' => 'diego.santos@email.com', 'Nombre' => 'Diego', 'Apellido' => 'Santos', 'FechaNacimiento' => '2011-07-20', 'Nacionalidad' => 'Brasil', 'CedulaIdentidad' => '12345678901', 'Peso' => 42.0, 'Altura' => 1.50, 'IMC' => 18.7, 'Edad' => 13, 'Nivel' => 'Principiante', 'estado' => 'aprobada', 'fecha_revision' => now()->subDays(5)],
            ['Email' => 'sofia.lopez@email.com', 'Nombre' => 'Sofía', 'Apellido' => 'López', 'FechaNacimiento' => '2007-04-12', 'Nacionalidad' => 'Argentina', 'CedulaIdentidad' => '45678912', 'Peso' => 55.0, 'Altura' => 1.65, 'IMC' => 20.2, 'Edad' => 17, 'Nivel' => 'Avanzado', 'estado' => 'pendiente'],
            ['Email' => 'mateo.vargas@email.com', 'Nombre' => 'Mateo', 'Apellido' => 'Vargas', 'FechaNacimiento' => '2013-01-30', 'Nacionalidad' => 'Chile', 'CedulaIdentidad' => '12345678-9', 'Peso' => 35.0, 'Altura' => 1.40, 'IMC' => 17.9, 'Edad' => 11, 'Nivel' => 'Principiante', 'estado' => 'rechazada', 'comentarios' => 'IMC bajo, requiere evaluación nutricional', 'fecha_revision' => now()->subDays(3)],
            ['Email' => 'valentina.cruz@email.com', 'Nombre' => 'Valentina', 'Apellido' => 'Cruz', 'FechaNacimiento' => '2006-09-15', 'Nacionalidad' => 'Perú', 'CedulaIdentidad' => '98765432', 'Peso' => 58.0, 'Altura' => 1.68, 'IMC' => 20.5, 'Edad' => 18, 'Nivel' => 'Elite', 'estado' => 'aprobada', 'fecha_revision' => now()->subDays(4)]
        ];

        foreach ($postulaciones as $postulacion) {
            $recomendaciones = Postulacion::generarRecomendaciones([
                'edad' => $postulacion['Edad'],
                'imc' => $postulacion['IMC'],
                'altura' => $postulacion['Altura'],
                'peso' => $postulacion['Peso']
            ]);
            
            $postulacion['recomendaciones_sistema'] = $recomendaciones;
            Postulacion::create($postulacion);
        }
    }
}