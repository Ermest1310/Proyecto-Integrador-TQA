<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SesionEntrenamiento;
use App\Models\Usuario;

class SesionEntrenamientoSeeder extends Seeder
{
    public function run()
    {
        $gimnastas = Usuario::whereHas('rol', function($q) {
            $q->where('NombreRol', 'Gimnasta');
        })->get();
        
        $entrenadores = Usuario::whereHas('rol', function($q) {
            $q->where('NombreRol', 'Entrenador');
        })->get();

        if ($gimnastas->isEmpty() || $entrenadores->isEmpty()) {
            return;
        }

        $sesiones = [
            ['ID_Gimnasta' => $gimnastas->first()->ID_Usuario, 'ID_Entrenador' => $entrenadores->first()->ID_Usuario, 'Fecha' => now()->subDays(20)->format('Y-m-d'), 'Hora_Inicio' => '14:00:00', 'Hora_Fin' => '16:00:00', 'Duracion_Minutos' => 120, 'Estado' => 'Completada', 'Asistio_Gimnasta' => true, 'Observaciones_Entrenador' => 'Excelente rendimiento en barras asimétricas'],
            ['ID_Gimnasta' => $gimnastas->first()->ID_Usuario, 'ID_Entrenador' => $entrenadores->first()->ID_Usuario, 'Fecha' => now()->subDays(18)->format('Y-m-d'), 'Hora_Inicio' => '16:00:00', 'Hora_Fin' => '18:00:00', 'Duracion_Minutos' => 120, 'Estado' => 'Completada', 'Asistio_Gimnasta' => true, 'Observaciones_Entrenador' => 'Mejoró técnica en salto'],
            ['ID_Gimnasta' => $gimnastas->skip(1)->first()->ID_Usuario ?? $gimnastas->first()->ID_Usuario, 'ID_Entrenador' => $entrenadores->first()->ID_Usuario, 'Fecha' => now()->subDays(15)->format('Y-m-d'), 'Hora_Inicio' => '10:00:00', 'Hora_Fin' => '12:00:00', 'Duracion_Minutos' => 120, 'Estado' => 'Cancelada', 'Asistio_Gimnasta' => false, 'Observaciones_Entrenador' => 'Ausencia justificada por enfermedad'],
            ['ID_Gimnasta' => $gimnastas->first()->ID_Usuario, 'ID_Entrenador' => $entrenadores->first()->ID_Usuario, 'Fecha' => now()->subDays(13)->format('Y-m-d'), 'Hora_Inicio' => '09:00:00', 'Hora_Fin' => '11:00:00', 'Duracion_Minutos' => 120, 'Estado' => 'Completada', 'Asistio_Gimnasta' => true, 'Observaciones_Entrenador' => 'Trabajo en flexibilidad'],
            ['ID_Gimnasta' => $gimnastas->skip(1)->first()->ID_Usuario ?? $gimnastas->first()->ID_Usuario, 'ID_Entrenador' => $entrenadores->first()->ID_Usuario, 'Fecha' => now()->subDays(12)->format('Y-m-d'), 'Hora_Inicio' => '15:00:00', 'Hora_Fin' => '17:00:00', 'Duracion_Minutos' => 120, 'Estado' => 'Completada', 'Asistio_Gimnasta' => true, 'Observaciones_Entrenador' => 'Rutina de suelo perfecta'],
            ['ID_Gimnasta' => $gimnastas->first()->ID_Usuario, 'ID_Entrenador' => $entrenadores->first()->ID_Usuario, 'Fecha' => now()->subDays(10)->format('Y-m-d'), 'Hora_Inicio' => '08:00:00', 'Hora_Fin' => '10:00:00', 'Duracion_Minutos' => 120, 'Estado' => 'Completada', 'Asistio_Gimnasta' => true, 'Observaciones_Entrenador' => 'Entrenamiento de fuerza'],
            ['ID_Gimnasta' => $gimnastas->skip(1)->first()->ID_Usuario ?? $gimnastas->first()->ID_Usuario, 'ID_Entrenador' => $entrenadores->first()->ID_Usuario, 'Fecha' => now()->subDays(9)->format('Y-m-d'), 'Hora_Inicio' => '17:00:00', 'Hora_Fin' => '19:00:00', 'Duracion_Minutos' => 120, 'Estado' => 'Completada', 'Asistio_Gimnasta' => false, 'Observaciones_Entrenador' => 'No asistió'],
            ['ID_Gimnasta' => $gimnastas->first()->ID_Usuario, 'ID_Entrenador' => $entrenadores->first()->ID_Usuario, 'Fecha' => now()->subDays(8)->format('Y-m-d'), 'Hora_Inicio' => '14:30:00', 'Hora_Fin' => '16:30:00', 'Duracion_Minutos' => 120, 'Estado' => 'Completada', 'Asistio_Gimnasta' => true, 'Observaciones_Entrenador' => 'Práctica de viga'],
            ['ID_Gimnasta' => $gimnastas->skip(1)->first()->ID_Usuario ?? $gimnastas->first()->ID_Usuario, 'ID_Entrenador' => $entrenadores->first()->ID_Usuario, 'Fecha' => now()->subDays(7)->format('Y-m-d'), 'Hora_Inicio' => '11:00:00', 'Hora_Fin' => '13:00:00', 'Duracion_Minutos' => 120, 'Estado' => 'Completada', 'Asistio_Gimnasta' => true, 'Observaciones_Entrenador' => 'Elementos nuevos en paralelas'],
            ['ID_Gimnasta' => $gimnastas->first()->ID_Usuario, 'ID_Entrenador' => $entrenadores->first()->ID_Usuario, 'Fecha' => now()->subDays(6)->format('Y-m-d'), 'Hora_Inicio' => '16:00:00', 'Hora_Fin' => '18:00:00', 'Duracion_Minutos' => 120, 'Estado' => 'Completada', 'Asistio_Gimnasta' => true, 'Observaciones_Entrenador' => 'Preparación para competencia'],
            ['ID_Gimnasta' => $gimnastas->skip(1)->first()->ID_Usuario ?? $gimnastas->first()->ID_Usuario, 'ID_Entrenador' => $entrenadores->first()->ID_Usuario, 'Fecha' => now()->subDays(5)->format('Y-m-d'), 'Hora_Inicio' => '09:30:00', 'Hora_Fin' => '11:30:00', 'Duracion_Minutos' => 120, 'Estado' => 'Completada', 'Asistio_Gimnasta' => true, 'Observaciones_Entrenador' => 'Calentamiento y estiramientos'],
            ['ID_Gimnasta' => $gimnastas->first()->ID_Usuario, 'ID_Entrenador' => $entrenadores->first()->ID_Usuario, 'Fecha' => now()->subDays(4)->format('Y-m-d'), 'Hora_Inicio' => '15:30:00', 'Hora_Fin' => '17:30:00', 'Duracion_Minutos' => 120, 'Estado' => 'Completada', 'Asistio_Gimnasta' => true, 'Observaciones_Entrenador' => 'Trabajo técnico avanzado'],
            ['ID_Gimnasta' => $gimnastas->skip(1)->first()->ID_Usuario ?? $gimnastas->first()->ID_Usuario, 'ID_Entrenador' => $entrenadores->first()->ID_Usuario, 'Fecha' => now()->subDays(3)->format('Y-m-d'), 'Hora_Inicio' => '10:30:00', 'Hora_Fin' => '12:30:00', 'Duracion_Minutos' => 120, 'Estado' => 'Completada', 'Asistio_Gimnasta' => true, 'Observaciones_Entrenador' => 'Rutinas completas'],
            ['ID_Gimnasta' => $gimnastas->first()->ID_Usuario, 'ID_Entrenador' => $entrenadores->first()->ID_Usuario, 'Fecha' => now()->subDays(2)->format('Y-m-d'), 'Hora_Inicio' => '14:00:00', 'Hora_Fin' => '16:00:00', 'Duracion_Minutos' => 120, 'Estado' => 'Completada', 'Asistio_Gimnasta' => true, 'Observaciones_Entrenador' => 'Corrección de errores'],
            ['ID_Gimnasta' => $gimnastas->skip(1)->first()->ID_Usuario ?? $gimnastas->first()->ID_Usuario, 'ID_Entrenador' => $entrenadores->first()->ID_Usuario, 'Fecha' => now()->subDays(1)->format('Y-m-d'), 'Hora_Inicio' => '17:00:00', 'Hora_Fin' => '19:00:00', 'Duracion_Minutos' => 120, 'Estado' => 'Completada', 'Asistio_Gimnasta' => true, 'Observaciones_Entrenador' => 'Sesión de recuperación'],
            ['ID_Gimnasta' => $gimnastas->first()->ID_Usuario, 'ID_Entrenador' => $entrenadores->first()->ID_Usuario, 'Fecha' => now()->format('Y-m-d'), 'Hora_Inicio' => '10:00:00', 'Hora_Fin' => '12:00:00', 'Duracion_Minutos' => 120, 'Estado' => 'Programada', 'Asistio_Gimnasta' => false, 'Observaciones_Entrenador' => null],
            ['ID_Gimnasta' => $gimnastas->skip(1)->first()->ID_Usuario ?? $gimnastas->first()->ID_Usuario, 'ID_Entrenador' => $entrenadores->first()->ID_Usuario, 'Fecha' => now()->addDays(1)->format('Y-m-d'), 'Hora_Inicio' => '15:00:00', 'Hora_Fin' => '17:00:00', 'Duracion_Minutos' => 120, 'Estado' => 'Programada', 'Asistio_Gimnasta' => false, 'Observaciones_Entrenador' => null],
            ['ID_Gimnasta' => $gimnastas->first()->ID_Usuario, 'ID_Entrenador' => $entrenadores->first()->ID_Usuario, 'Fecha' => now()->addDays(2)->format('Y-m-d'), 'Hora_Inicio' => '09:00:00', 'Hora_Fin' => '11:00:00', 'Duracion_Minutos' => 120, 'Estado' => 'Programada', 'Asistio_Gimnasta' => false, 'Observaciones_Entrenador' => null]
        ];

        foreach ($sesiones as $sesion) {
            SesionEntrenamiento::create($sesion);
        }
    }
}