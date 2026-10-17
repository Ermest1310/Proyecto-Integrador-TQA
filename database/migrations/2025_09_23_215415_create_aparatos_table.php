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
        Schema::create('aparatos', function (Blueprint $table) {
            $table->id('ID_Aparato');
            $table->string('Nombre', 50);
            $table->text('Descripcion')->nullable();
            $table->date('UltimoMantenimiento')->nullable();
            $table->date('ProximoMantenimiento')->nullable();
            $table->enum('EstadoMantenimiento', ['Operativo', 'Mantenimiento', 'Fuera de Servicio'])->default('Operativo');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aparatos');
    }
};
