<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluacionesJuez extends Model
{
    use HasFactory;

    protected $table = 'evaluaciones_juez';
    protected $primaryKey = 'ID_EvaluacionJuez';
    public $timestamps = false;

    protected $fillable = [
        'ID_Gimnasta',
        'ID_Competencia',
        'ID_Aparato',
        'ID_Usuario',
        'video_ruta',
        'nota_final',
        'nota_dificultad',
        'nota_ejecucion',
        'observaciones',
        'estado',
        'fecha_evaluacion',
    ];

    protected $casts = [
        'nota_final' => 'decimal:2',
        'nota_dificultad' => 'decimal:2',
        'nota_ejecucion' => 'decimal:2',
        'fecha_evaluacion' => 'datetime',
    ];

    public function gimnasta()
    {
        return $this->belongsTo(Usuario::class, 'ID_Gimnasta', 'ID_Usuario');
    }

    public function competencia()
    {
        return $this->belongsTo(Competencia::class, 'ID_Competencia', 'ID_Competencia');
    }

    public function aparato()
    {
        return $this->belongsTo(Aparato::class, 'ID_Aparato', 'ID_Aparato');
    }

    public function juez()
    {
        return $this->belongsTo(Usuario::class, 'ID_Usuario', 'ID_Usuario');
    }

    public function descuentos()
    {
        return $this->hasMany(DescuentosJuez::class, 'ID_EvaluacionJuez', 'ID_EvaluacionJuez');
    }

    public function videos()
    {
        return $this->hasMany(VideosEvaluacion::class, 'ID_EvaluacionJuez', 'ID_EvaluacionJuez');
    }
}
