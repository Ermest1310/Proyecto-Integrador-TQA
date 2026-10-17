<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sesiones_entrenamiento', function (Blueprint $table) {
            $table->id('ID_Sesion');
            $table->foreignId('ID_Gimnasta')->constrained('usuarios', 'ID_Usuario')->cascadeOnDelete();
            $table->foreignId('ID_Entrenador')->constrained('usuarios', 'ID_Usuario')->cascadeOnDelete();
            $table->foreignId('ID_Aparato')->nullable()->constrained('aparatos', 'ID_Aparato')->cascadeOnDelete();
            
            // Programación de la sesión
            $table->date('Fecha');
            $table->time('Hora_Inicio');
            $table->time('Hora_Fin');
            $table->integer('Duracion_Minutos'); // Calculado automáticamente
            
            // Tipo de asignación
            $table->enum('Tipo_Asignacion', ['Automatica', 'Manual'])->default('Automatica');
            
            // Estado de la sesión
            $table->enum('Estado', ['Programada', 'En_Curso', 'Completada', 'Cancelada'])->default('Programada');
            
            // Observaciones y notas
            $table->text('Observaciones_Entrenador')->nullable();
            $table->text('Notas_Gimnasta')->nullable();
            
            // Control de asistencia
            $table->boolean('Asistio_Gimnasta')->default(false);
            $table->timestamp('Hora_Llegada_Gimnasta')->nullable();
            $table->timestamp('Hora_Salida_Gimnasta')->nullable();
            
            // Evaluación de la sesión
            $table->integer('Calificacion_Sesion')->nullable(); // 1-5 estrellas
            $table->text('Comentarios_Finales')->nullable();
            
            $table->boolean('activo')->default(true);
            $table->timestamps();
            
            // Índices para optimizar consultas
            $table->index(['Fecha', 'Hora_Inicio']);
            $table->index(['ID_Entrenador', 'Fecha']);
            $table->index(['Estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sesiones_entrenamiento');
    }
};