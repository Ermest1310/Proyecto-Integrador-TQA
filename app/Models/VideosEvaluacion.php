<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideosEvaluacion extends Model
{
    use HasFactory;

    protected $table = 'videos_evaluacion';
    protected $primaryKey = 'ID_Video';
    public $timestamps = false;

    protected $fillable = [
        'ID_EvaluacionJuez',
        'nombre_original',
        'nombre_archivo',
        'ruta_archivo',
        'tamano_bytes',
        'duracion_segundos',
        'analizado_mediapipe',
        'resultado_analisis_json',
        'fecha_subido',
    ];

    protected $casts = [
        'analizado_mediapipe' => 'boolean',
        'resultado_analisis_json' => 'array',
        'fecha_subido' => 'datetime',
    ];

    public function evaluacion()
    {
        return $this->belongsTo(EvaluacionesJuez::class, 'ID_EvaluacionJuez', 'ID_EvaluacionJuez');
    }
}
