<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluacionPsicologica extends Model
{
    use HasFactory;

    protected $table = 'evaluaciones_psicologicas';
    protected $primaryKey = 'ID_Evaluacion';

    protected $fillable = [
        'ID_Gimnasta', 'FechaEvaluacion', 'NivelAnsiedad', 'NivelEstres',
        'NivelMotivacion', 'CalidadSueno', 'Concentracion',
        'Observaciones', 'Recomendaciones'
    ];

    public function gimnasta()
    {
        return $this->belongsTo(Gimnasta::class, 'ID_Gimnasta', 'ID_Gimnasta');
    }
}
