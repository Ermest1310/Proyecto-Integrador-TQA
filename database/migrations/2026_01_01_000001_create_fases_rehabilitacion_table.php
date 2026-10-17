<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fases_rehabilitacion', function (Blueprint $table) {
            $table->id('ID_Fase');
            $table->string('Nombre', 100);
            $table->text('Descripcion')->nullable();
            $table->integer('Orden')->default(0);
            $table->integer('Duracion_Minima_Dias')->default(7);
            $table->integer('Duracion_Maxima_Dias')->default(21);
            $table->decimal('Riesgo_Recaída', 5, 4)->default(0.05);
            $table->json('Tipos_Lesion_Validos')->nullable();
            $table->json('Gravedades_Validas')->nullable();
            $table->json('Fases_Siguientes')->nullable();
            $table->boolean('Es_Inicial')->default(false);
            $table->boolean('Es_Final')->default(false);
            $table->boolean('activo')->default(true);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fases_rehabilitacion');
    }
};
