<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('area_medica', function (Blueprint $table) {
            $table->id('ID_AreaMedica');
            $table->string('Nombre', 100);
            $table->string('Especialista', 100);
            $table->string('TipoConsulta', 100)->nullable();
            $table->text('Descripcion')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('area_medica');
    }
};
