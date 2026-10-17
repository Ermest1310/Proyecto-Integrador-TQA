<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pose_resultados', function (Blueprint $table) {
            $table->integer('ID_Resultado', true);
            $table->integer('ID_Video');
            $table->integer('FrameNum');
            $table->json('Landmarks');
            $table->json('AngulosCalculados')->nullable();
            $table->timestamps();

            $table->foreign('ID_Video')->references('ID_Video')->on('videos')->onDelete('cascade');
            $table->index(['ID_Video', 'FrameNum']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pose_resultados');
    }
};
