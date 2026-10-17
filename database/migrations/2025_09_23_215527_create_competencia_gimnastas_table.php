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
        Schema::create('competencia_gimnastas', function (Blueprint $table) {
            $table->foreignId('ID_Competencia')->constrained('competencias', 'ID_Competencia')->cascadeOnDelete();

            $table->foreignId('ID_Gimnasta')->constrained('usuarios', 'ID_Usuario')->cascadeOnDelete();

            $table->primary(['ID_Competencia', 'ID_Gimnasta']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competencia_gimnastas');
    }
};
