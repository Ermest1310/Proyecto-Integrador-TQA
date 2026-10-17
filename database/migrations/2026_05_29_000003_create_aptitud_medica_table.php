<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aptitud_medica', function (Blueprint $table) {
            $table->integer('ID_Aptitud', true);
            $table->integer('ID_Gimnasta');
            $table->date('FechaCertificacion');
            $table->boolean('AptoParaEntrenar')->default(true);
            $table->text('RestriccionesMedicas')->nullable();
            $table->text('Diagnostico')->nullable();
            $table->string('MedicoResponsable')->nullable();
            $table->text('Observaciones')->nullable();
            $table->timestamps();

            $table->foreign('ID_Gimnasta')->references('ID_Gimnasta')->on('gimnastas')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aptitud_medica');
    }
};
