<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('postulaciones', function (Blueprint $table) {
            $table->id('ID_Postulacion');
            $table->string('Email', 50)->unique();
            $table->string('Nombre', 20);
            $table->string('Apellido', 20);
            $table->date('FechaNacimiento');
            $table->string('Nacionalidad', 30);
            $table->string('CedulaIdentidad', 20);
            $table->decimal('Peso', 5, 2);
            $table->decimal('Altura', 4, 2);
            $table->decimal('IMC', 4, 2);
            $table->integer('Edad');
            $table->enum('Nivel', ['Principiante', 'Intermedio', 'Avanzado', 'Elite']);
            $table->enum('estado', ['pendiente', 'aprobada', 'rechazada'])->default('pendiente');
            $table->text('recomendaciones_sistema')->nullable();
            $table->text('comentarios')->nullable();
            $table->timestamp('fecha_postulacion')->useCurrent();
            $table->timestamp('fecha_revision')->nullable();
            $table->unsignedBigInteger('revisado_por')->nullable();
            $table->timestamps();
            
            $table->foreign('revisado_por')->references('ID_Usuario')->on('usuarios');
            $table->index(['Nacionalidad', 'CedulaIdentidad']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('postulaciones');
    }
};