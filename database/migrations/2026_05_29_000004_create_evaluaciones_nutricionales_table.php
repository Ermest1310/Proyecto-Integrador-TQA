<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluaciones_nutricionales', function (Blueprint $table) {
            $table->integer('ID_Evaluacion', true);
            $table->integer('ID_Gimnasta');
            $table->date('FechaEvaluacion');
            $table->decimal('Peso', 5, 2)->nullable();
            $table->decimal('Altura', 5, 2)->nullable();
            $table->decimal('IMC', 4, 2)->nullable();
            $table->decimal('PorcentajeGrasa', 4, 2)->nullable();
            $table->decimal('MasaMuscular', 4, 2)->nullable();
            $table->decimal('NivelHidratacion', 4, 2)->nullable();
            $table->string('SeguimientoDieta')->nullable();
            $table->text('Recomendaciones')->nullable();
            $table->text('Observaciones')->nullable();
            $table->timestamps();

            $table->foreign('ID_Gimnasta')->references('ID_Gimnasta')->on('gimnastas')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluaciones_nutricionales');
    }
};
