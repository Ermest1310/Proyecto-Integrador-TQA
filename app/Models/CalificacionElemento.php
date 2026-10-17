<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CalificacionElemento extends Model
{
    protected $table = 'calificacion_elementos';
    protected $primaryKey = 'ID_Calificacion';
    public $timestamps = true;

    protected $fillable = [
        'ID_Gimnasta',
        'ID_Elemento',
        'TasaExito',
        'IntentosExitosos',
        'IntentosFallidos',
        'FechaUltimaEvaluacion',
        'Observaciones',
    ];

    protected $casts = [
        'ID_Gimnasta' => 'integer',
        'ID_Elemento' => 'integer',
        'TasaExito' => 'float',
        'IntentosExitosos' => 'integer',
        'IntentosFallidos' => 'integer',
        'FechaUltimaEvaluacion' => 'date',
    ];

    public function gimnasta()
    {
        return $this->belongsTo(Usuario::class, 'ID_Gimnasta', 'ID_Usuario');
    }

    public function elementoRutina()
    {
        return $this->belongsTo(ElementoRutina::class, 'ID_Elemento', 'ID_Elemento');
    }

    public function actualizarTasaExito(int $exitoso): void
    {
        if ($exitoso) {
            $this->IntentosExitosos++;
        } else {
            $this->IntentosFallidos++;
        }

        $total = $this->IntentosExitosos + $this->IntentosFallidos;
        $this->TasaExito = $total > 0 ? round($this->IntentosExitosos / $total, 4) : 0.7500;
        $this->FechaUltimaEvaluacion = now();
        $this->save();
    }

    public static function getTasaExitoPromedio(int $gimnastaId): float
    {
        $promedio = self::where('ID_Gimnasta', $gimnastaId)->avg('TasaExito');
        return $promedio !== null ? round((float) $promedio, 4) : 0.7500;
    }

    public static function getHistorialGimnasta(int $gimnastaId)
    {
        return self::with('elementoRutina')
            ->where('ID_Gimnasta', $gimnastaId)
            ->orderByDesc('FechaUltimaEvaluacion')
            ->get();
    }
}
