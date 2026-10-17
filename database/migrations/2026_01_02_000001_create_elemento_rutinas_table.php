<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('elemento_rutinas', function (Blueprint $table) {
            $table->id('ID_Elemento');
            $table->integer('ID_Aparato');
            $table->string('Nombre', 100);
            $table->string('Grupo', 1)->default('A');
            $table->decimal('ValorDificultad', 3, 1)->default(0.1);
            $table->integer('MinEnlace')->default(0);
            $table->json('IDsConexion')->nullable();
            $table->text('Descripcion')->nullable();
            $table->boolean('activo')->default(true);

            $table->foreign('ID_Aparato')->references('ID_Aparato')->on('aparatos')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('elemento_rutinas');
    }
};
