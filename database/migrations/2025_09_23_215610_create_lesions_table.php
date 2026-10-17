<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lesiones', function (Blueprint $table) {
            $table->id('ID_Lesion');
            $table->foreignId('ID_Gimnasta')->constrained('usuarios', 'ID_Usuario')->cascadeOnDelete();
            $table->unsignedBigInteger('ID_Medico')->nullable();
            
            $table->string('Tipo', 30);
            $table->string('ParteCuerpo', 30);
            $table->string('ParteEspecifica', 50)->nullable();
            $table->date('FechaLesion');
            $table->date('FechaDiagnostico')->nullable();
            $table->date('FechaRecuperacion')->nullable();
            $table->enum('Estado', ['Lesionado', 'En Rehabilitación', 'Recuperada'])->default('Lesionado');
            $table->enum('Gravedad', ['Leve', 'Moderada', 'Grave'])->default('Leve');
            $table->text('Tratamiento')->nullable();
            $table->integer('TiempoRecuperacion')->nullable()->comment('Días estimados');
            $table->text('Observaciones')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
            
            $table->foreign('ID_Medico')->references('ID_Usuario')->on('usuarios')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lesiones');
    }
};
