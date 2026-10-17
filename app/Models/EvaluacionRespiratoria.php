<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluacionRespiratoria extends Model
{
    use HasFactory;

    protected $table = 'evaluaciones_respiratorias';
    protected $primaryKey = 'ID_Evaluacion';

    protected $fillable = [
        'ID_Gimnasta', 'FechaEvaluacion', 'CapacidadPulmonar',
        'SaturacionOxigeno', 'DiagnosticoRespiratorio', 'Observaciones'
    ];

    public function gimnasta()
    {
        return $this->belongsTo(Gimnasta::class, 'ID_Gimnasta', 'ID_Gimnasta');
    }
}
