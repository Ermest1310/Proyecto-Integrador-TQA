<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plan_fase_seguimiento', function (Blueprint $table) {
            $table->id('ID_Seguimiento');
            $table->unsignedBigInteger('ID_Plan');
            $table->unsignedBigInteger('ID_Fase');
            $table->integer('Orden');
            $table->date('Fecha_Inicio')->nullable();
            $table->date('Fecha_Fin')->nullable();
            $table->enum('Estado', ['Pendiente', 'En Curso', 'Completada', 'Omitida'])->default('Pendiente');
            $table->decimal('Riesgo_Recaída_Fase', 5, 4)->default(0);
            $table->text('Observaciones')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->foreign('ID_Plan')->references('ID_Plan')->on('planes_rehabilitacion')->onDelete('cascade');
            $table->foreign('ID_Fase')->references('ID_Fase')->on('fases_rehabilitacion');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plan_fase_seguimiento');
    }
};
