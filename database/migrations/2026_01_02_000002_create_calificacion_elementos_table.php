<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calificacion_elementos', function (Blueprint $table) {
            $table->id('ID_Calificacion');
            $table->integer('ID_Gimnasta');
            $table->unsignedBigInteger('ID_Elemento');
            $table->decimal('TasaExito', 5, 4)->default(0.7500);
            $table->integer('IntentosExitosos')->default(0);
            $table->integer('IntentosFallidos')->default(0);
            $table->date('FechaUltimaEvaluacion')->nullable();
            $table->text('Observaciones')->nullable();
            $table->timestamps();

            $table->foreign('ID_Gimnasta')->references('ID_Usuario')->on('usuarios')->onDelete('cascade');
            $table->foreign('ID_Elemento')->references('ID_Elemento')->on('elemento_rutinas')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calificacion_elementos');
    }
};
