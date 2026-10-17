<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('descuentos_juez', function (Blueprint $table) {
            $table->unsignedInteger('ID_Aparato')->nullable()->after('ID_Elemento');
            $table->json('angulos_json')->nullable()->after('angulo_detectado');
            $table->decimal('confianza_ia', 5, 2)->nullable()->after('descuento_ia_propuesto');
            $table->boolean('fue_corregido')->default(false)->after('justificacion');

            $table->foreign('ID_Aparato')->references('ID_Aparato')->on('aparatos')->nullOnDelete();
        });

        DB::statement('ALTER TABLE descuentos_juez MODIFY ID_EvaluacionJuez INT NULL');
    }

    public function down(): void
    {
        Schema::table('descuentos_juez', function (Blueprint $table) {
            $table->dropForeign(['ID_Aparato']);
            $table->dropColumn(['ID_Aparato', 'angulos_json', 'confianza_ia', 'fue_corregido']);
        });
    }
};
