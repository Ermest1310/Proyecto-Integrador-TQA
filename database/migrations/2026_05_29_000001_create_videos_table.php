<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->integer('ID_Video', true);
            $table->integer('ID_Usuario');
            $table->string('NombreArchivo');
            $table->string('NombreOriginal')->nullable();
            $table->string('Tipo')->nullable();
            $table->bigInteger('Tamano')->nullable();
            $table->text('Descripcion')->nullable();
            $table->timestamps();

            $table->foreign('ID_Usuario')->references('ID_Usuario')->on('usuarios')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('videos');
    }
};
