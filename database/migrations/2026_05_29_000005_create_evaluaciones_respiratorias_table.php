<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluaciones_respiratorias', function (Blueprint $table) {
            $table->integer('ID_Evaluacion', true);
            $table->integer('ID_Gimnasta');
            $table->date('FechaEvaluacion');
            $table->decimal('CapacidadPulmonar', 5, 2)->nullable();
            $table->decimal('SaturacionOxigeno', 4, 2)->nullable();
            $table->string('DiagnosticoRespiratorio')->nullable();
            $table->text('Observaciones')->nullable();
            $table->timestamps();

            $table->foreign('ID_Gimnasta')->references('ID_Gimnasta')->on('gimnastas')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluaciones_respiratorias');
    }
};
