<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Aparato;

class AparatoSeeder extends Seeder
{
    public function run(): void
    {
        $aparatos = [
            ['Suelo', 'Ejercicios de suelo con música, tumbling y acrobacia'],
            ['Salto', 'Mesa de salto para ejercicios de impulso y vuelo'],
            ['Barras Asimétricas', 'Dos barras de diferente altura para rutinas de fuerza'],
            ['Viga de Equilibrio', 'Viga estrecha para ejercicios de equilibrio y precisión'],
            ['Anillas', 'Anillas suspendidas para ejercicios de fuerza masculina'],
            ['Caballo con Arcos', 'Aparato masculino para ejercicios circulares'],
            ['Barras Paralelas', 'Barras paralelas para rutinas de fuerza masculina'],
            ['Barra Fija', 'Barra horizontal para ejercicios de balanceo y giros'],
            ['Cuerda', 'Ejercicios rítmicos con cuerda'],
            ['Aro', 'Ejercicios rítmicos con aro'],
            ['Pelota', 'Ejercicios rítmicos con pelota'],
            ['Mazas', 'Ejercicios rítmicos con mazas'],
            ['Cinta', 'Ejercicios rítmicos con cinta']
        ];

        foreach ($aparatos as $index => $aparato) {
            // Algunos aparatos tienen mantenimiento programado
            $ultimoMantenimiento = rand(0, 1) ? now()->subDays(rand(30, 180)) : null;
            $proximoMantenimiento = $ultimoMantenimiento ? $ultimoMantenimiento->addDays(rand(90, 365)) : now()->addDays(rand(30, 180));
            
            // Estados de mantenimiento variados
            $estados = ['Operativo', 'Operativo', 'Operativo', 'Mantenimiento', 'Operativo'];
            $estadoMantenimiento = $estados[array_rand($estados)];
            
            Aparato::create([
                'Nombre' => $aparato[0],
                'Descripcion' => $aparato[1],
                'UltimoMantenimiento' => $ultimoMantenimiento,
                'ProximoMantenimiento' => $proximoMantenimiento,
                'EstadoMantenimiento' => $estadoMantenimiento,
                'activo' => true
            ]);
        }
    }
}