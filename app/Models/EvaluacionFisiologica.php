<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluacionFisiologica extends Model
{
    use HasFactory;

    protected $table = 'evaluaciones_fisiologicas';
    protected $primaryKey = 'ID_Evaluacion';

    protected $fillable = [
        'ID_Gimnasta', 'FechaEvaluacion', 'FCReposo', 'FCEsfuerzo',
        'PresionSistolica', 'PresionDiastolica', 'SaturacionOxigeno', 'Observaciones'
    ];

    public function gimnasta()
    {
        return $this->belongsTo(Gimnasta::class, 'ID_Gimnasta', 'ID_Gimnasta');
    }
}
