<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('planes_rehabilitacion', function (Blueprint $table) {
            $table->id('ID_Plan');
            $table->integer('ID_Lesion');
            $table->integer('ID_Gimnasta');
            $table->unsignedBigInteger('ID_Medico')->nullable();
            $table->date('Fecha_Inicio');
            $table->date('Fecha_Estimada_Fin')->nullable();
            $table->integer('Duracion_Estimada_Dias')->default(0);
            $table->decimal('Riesgo_Recaída_Total', 5, 4)->default(0);
            $table->json('Secuencia_Fases')->nullable();
            $table->enum('Estado', ['Pendiente', 'En Curso', 'Completado', 'Cancelado'])->default('Pendiente');
            $table->text('Observaciones')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->foreign('ID_Lesion')->references('ID_Lesion')->on('lesiones')->onDelete('cascade');
            $table->foreign('ID_Gimnasta')->references('ID_Usuario')->on('usuarios')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('planes_rehabilitacion');
    }
};
