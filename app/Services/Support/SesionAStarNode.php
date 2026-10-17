<?php

namespace App\Services\Support;

class SesionAStarNode
{
    public array $asignaciones;
    public float $g;
    public float $h;
    public float $f;
    public int $profundidad;
    public float $makespanParcial;
    public array $cargaEntrenadores;
    public array $usoAparatos;

    public function __construct(
        array $asignaciones,
        float $g,
        float $h,
        int $profundidad,
        float $makespanParcial,
        array $cargaEntrenadores,
        array $usoAparatos
    ) {
        $this->asignaciones = $asignaciones;
        $this->g = $g;
        $this->h = $h;
        $this->f = $g + $h;
        $this->profundidad = $profundidad;
        $this->makespanParcial = $makespanParcial;
        $this->cargaEntrenadores = $cargaEntrenadores;
        $this->usoAparatos = $usoAparatos;
    }

    public function estadoKey(): string
    {
        $partes = [];
        foreach ($this->asignaciones as $a) {
            $partes[] = $a['gimnasta_id'] . ':' . $a['bloque_id'] . ':' . $a['aparato_id'] . ':' . $a['entrenador_id'];
        }
        sort($partes);
        return implode('|', $partes);
    }
}
