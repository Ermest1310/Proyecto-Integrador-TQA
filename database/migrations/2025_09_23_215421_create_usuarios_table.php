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
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id('ID_Usuario');
            $table->string('NombreUsuario', 50);
            $table->string('Contraseña', 255);
            $table->foreignId('ID_Rol')->constrained('roles', 'ID_Rol')->cascadeOnDelete();
            $table->string('Email', 50)->unique();
            $table->string('FotoPerfil')->nullable();
            
            // Campos unificados para todos los tipos de usuario
            $table->string('Nombre', 20)->nullable();
            $table->string('Apellido', 20)->nullable();
            $table->date('FechaNacimiento')->nullable();
            $table->string('Nacionalidad', 20)->nullable();
            $table->decimal('Peso', 5, 2)->nullable();
            $table->decimal('Altura', 5, 2)->nullable();
            $table->string('Nivel', 15)->nullable();
            $table->string('Contacto', 30)->nullable();
            
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
