<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SolicitudNivel;
use App\Models\Usuario;
use App\Models\Rol;

class SolicitudNivelSeeder extends Seeder
{
    public function run(): void
    {
        $rolGimnasta = Rol::where('NombreRol', 'Gimnasta')->first();
        $rolEntrenador = Rol::where('NombreRol', 'Entrenador')->first();
        $rolAdmin = Rol::where('NombreRol', 'Administrador')->first();

        if (!$rolGimnasta || !$rolEntrenador || !$rolAdmin) {
            $this->command->error('Roles no encontrados. Ejecuta RolSeeder primero.');
            return;
        }

        $gimnastas = Usuario::where('ID_Rol', $rolGimnasta->ID_Rol)->get();
        $entrenadores = Usuario::where('ID_Rol', $rolEntrenador->ID_Rol)->get();
        $admin = Usuario::where('ID_Rol', $rolAdmin->ID_Rol)->first();

        if ($gimnastas->isEmpty() || $entrenadores->isEmpty() || !$admin) {
            $this->command->error('No hay usuarios suficientes. Ejecuta UsuarioSeeder primero.');
            return;
        }

        // Solicitud pendiente de evaluación (Principiante -> Intermedio)
        SolicitudNivel::create([
            'ID_Gimnasta' => $gimnastas->where('Nivel', 'Principiante')->first()->ID_Usuario,
            'nivel_actual' => 'Principiante',
            'nivel_solicitado' => 'Intermedio',
            'tipo' => 'evaluacion',
            'comentarios_gimnasta' => 'He estado entrenando consistentemente durante 6 meses y siento que estoy listo para el siguiente nivel.',
            'estado' => 'pendiente',
            'fecha_solicitud' => now()->subDays(2)
        ]);

        // Recomendación pendiente de entrenador (Intermedio -> Avanzado)
        SolicitudNivel::create([
            'ID_Gimnasta' => $gimnastas->where('Nivel', 'Intermedio')->first()->ID_Usuario,
            'nivel_actual' => 'Intermedio',
            'nivel_solicitado' => 'Avanzado',
            'tipo' => 'recomendacion',
            'ID_Entrenador' => $entrenadores->first()->ID_Usuario,
            'comentarios_entrenador' => 'Este gimnasta ha demostrado excelente progreso y dominio de las técnicas intermedias. Recomiendo su promoción.',
            'estado' => 'pendiente',
            'fecha_solicitud' => now()->subDays(1)
        ]);

        // Solicitud aprobada (Principiante -> Intermedio)
        $principiantes = $gimnastas->where('Nivel', 'Principiante');
        if ($principiantes->count() > 1) {
            SolicitudNivel::create([
                'ID_Gimnasta' => $principiantes->skip(1)->first()->ID_Usuario,
                'nivel_actual' => 'Principiante',
                'nivel_solicitado' => 'Intermedio',
                'tipo' => 'evaluacion',
                'comentarios_gimnasta' => 'Creo que he mejorado mucho y estoy listo para más desafíos.',
                'comentarios_admin' => 'Aprobado. Ha demostrado buen desempeño en las evaluaciones.',
                'estado' => 'aprobada',
                'fecha_solicitud' => now()->subDays(10),
                'fecha_respuesta' => now()->subDays(8),
                'aprobado_por' => $admin->ID_Usuario
            ]);
        }

        // Recomendación aprobada (Intermedio -> Avanzado)
        $intermedios = $gimnastas->where('Nivel', 'Intermedio');
        if ($intermedios->count() > 1) {
            SolicitudNivel::create([
                'ID_Gimnasta' => $intermedios->skip(1)->first()->ID_Usuario,
                'nivel_actual' => 'Intermedio',
                'nivel_solicitado' => 'Avanzado',
                'tipo' => 'recomendacion',
                'ID_Entrenador' => $entrenadores->skip(1)->first()->ID_Usuario,
                'comentarios_entrenador' => 'Gimnasta destacado con excelente técnica y dedicación. Merece la promoción.',
                'comentarios_admin' => 'Aprobado por recomendación del entrenador.',
                'estado' => 'aprobada',
                'fecha_solicitud' => now()->subDays(15),
                'fecha_respuesta' => now()->subDays(13),
                'aprobado_por' => $admin->ID_Usuario
            ]);
        }

        // Solicitud rechazada (Principiante -> Intermedio)
        if ($principiantes->count() > 2) {
            SolicitudNivel::create([
                'ID_Gimnasta' => $principiantes->skip(2)->first()->ID_Usuario,
                'nivel_actual' => 'Principiante',
                'nivel_solicitado' => 'Intermedio',
                'tipo' => 'evaluacion',
                'comentarios_gimnasta' => 'Quiero subir de nivel.',
                'comentarios_admin' => 'Rechazado. Necesitas más tiempo de práctica y mejorar la técnica en los elementos básicos antes de avanzar.',
                'estado' => 'rechazada',
                'fecha_solicitud' => now()->subDays(7),
                'fecha_respuesta' => now()->subDays(5),
                'aprobado_por' => $admin->ID_Usuario
            ]);
        }

        // Solicitud pendiente (Avanzado -> Elite)
        $avanzados = $gimnastas->where('Nivel', 'Avanzado');
        if ($avanzados->isNotEmpty()) {
            SolicitudNivel::create([
                'ID_Gimnasta' => $avanzados->first()->ID_Usuario,
                'nivel_actual' => 'Avanzado',
                'nivel_solicitado' => 'Elite',
                'tipo' => 'recomendacion',
                'ID_Entrenador' => $entrenadores->first()->ID_Usuario,
                'comentarios_entrenador' => 'Gimnasta excepcional con potencial para competencias de alto nivel. Recomiendo promoción a Elite.',
                'estado' => 'pendiente',
                'fecha_solicitud' => now()
            ]);
        }

    }
}
