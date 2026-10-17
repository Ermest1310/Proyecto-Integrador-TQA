<?php

namespace App\Services\Support;

class AStarNode
{
    public string $elementoId;
    public array $secuencia;
    public float $g;
    public float $h;
    public float $f;
    public int $profundidad;
    public float $valorDificultadAcumulado;
    public float $penalizacionAcumulada;

    public function __construct(
        string $elementoId,
        array $secuencia,
        float $g,
        float $h,
        int $profundidad,
        float $valorDificultadAcumulado,
        float $penalizacionAcumulada
    ) {
        $this->elementoId = $elementoId;
        $this->secuencia = $secuencia;
        $this->g = $g;
        $this->h = $h;
        $this->f = $g + $h;
        $this->profundidad = $profundidad;
        $this->valorDificultadAcumulado = $valorDificultadAcumulado;
        $this->penalizacionAcumulada = $penalizacionAcumulada;
    }

    public function estadoKey(): string
    {
        return implode('|', $this->secuencia);
    }
}
