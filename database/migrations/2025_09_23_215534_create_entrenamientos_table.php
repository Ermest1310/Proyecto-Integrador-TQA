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
        Schema::create('entrenamientos', function (Blueprint $table) {
            $table->id('ID_Entrenamiento');
            $table->foreignId('ID_Gimnasta')->constrained('usuarios', 'ID_Usuario')->cascadeOnDelete();

            $table->foreignId('ID_Aparato')->constrained('aparatos', 'ID_Aparato')->cascadeOnDelete();

            $table->date('Fecha');
            $table->integer('Duracion');
            $table->string('Observaciones', 50)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entrenamientos');
    }
};
