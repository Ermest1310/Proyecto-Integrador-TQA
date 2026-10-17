<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('usuarios', 'CedulaIdentidad')) {
            Schema::table('usuarios', function (Blueprint $table) {
                $table->string('CedulaIdentidad', 20)->nullable()->after('Email');
            });
        }
        
        // Asignar cédulas temporales a usuarios existentes que no tengan
        $usuarios = \App\Models\Usuario::whereNull('CedulaIdentidad')->orWhere('CedulaIdentidad', '')->get();
        foreach ($usuarios as $usuario) {
            $usuario->update(['CedulaIdentidad' => 'TEMP-' . $usuario->ID_Usuario]);
        }
        
        // No crear índice único para permitir cédulas duplicadas
    }

    public function down()
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropColumn('CedulaIdentidad');
        });
    }
};