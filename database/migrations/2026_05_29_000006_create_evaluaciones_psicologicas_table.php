<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluaciones_psicologicas', function (Blueprint $table) {
            $table->integer('ID_Evaluacion', true);
            $table->integer('ID_Gimnasta');
            $table->date('FechaEvaluacion');
            $table->integer('NivelAnsiedad')->nullable();
            $table->integer('NivelEstres')->nullable();
            $table->integer('NivelMotivacion')->nullable();
            $table->integer('CalidadSueno')->nullable();
            $table->integer('Concentracion')->nullable();
            $table->text('Observaciones')->nullable();
            $table->text('Recomendaciones')->nullable();
            $table->timestamps();

            $table->foreign('ID_Gimnasta')->references('ID_Gimnasta')->on('gimnastas')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluaciones_psicologicas');
    }
};
