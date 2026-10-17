<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitudes_nivel', function (Blueprint $table) {
            $table->id('ID_Solicitud');
            $table->foreignId('ID_Gimnasta')->constrained('usuarios', 'ID_Usuario')->cascadeOnDelete();
            $table->string('nivel_actual', 15);
            $table->string('nivel_solicitado', 15);
            $table->enum('tipo', ['evaluacion', 'recomendacion'])->default('evaluacion');
            $table->foreignId('ID_Entrenador')->nullable()->constrained('usuarios', 'ID_Usuario')->nullOnDelete();
            $table->enum('estado', ['pendiente', 'aprobada', 'rechazada'])->default('pendiente');
            $table->text('comentarios_gimnasta')->nullable();
            $table->text('comentarios_entrenador')->nullable();
            $table->text('comentarios_admin')->nullable();
            $table->timestamp('fecha_solicitud')->useCurrent();
            $table->timestamp('fecha_respuesta')->nullable();
            $table->foreignId('aprobado_por')->nullable()->constrained('usuarios', 'ID_Usuario')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitudes_nivel');
    }
};
