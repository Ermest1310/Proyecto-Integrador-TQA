<?php

namespace App\Services\Support;

class RehabilitacionAStarNode
{
    public string $faseId;
    public array $secuencia;
    public float $g;
    public float $h;
    public float $f;
    public int $profundidad;
    public int $diasAcumulados;
    public float $riesgoRecaidaAcumulado;

    public function __construct(
        string $faseId,
        array $secuencia,
        float $g,
        float $h,
        int $profundidad,
        int $diasAcumulados,
        float $riesgoRecaidaAcumulado
    ) {
        $this->faseId = $faseId;
        $this->secuencia = $secuencia;
        $this->g = $g;
        $this->h = $h;
        $this->f = $g + $h;
        $this->profundidad = $profundidad;
        $this->diasAcumulados = $diasAcumulados;
        $this->riesgoRecaidaAcumulado = $riesgoRecaidaAcumulado;
    }

    public function estadoKey(): string
    {
        return implode('|', $this->secuencia);
    }
}
