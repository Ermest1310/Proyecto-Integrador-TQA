<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluacionNutricional extends Model
{
    use HasFactory;

    protected $table = 'evaluaciones_nutricionales';
    protected $primaryKey = 'ID_Evaluacion';

    protected $fillable = [
        'ID_Gimnasta', 'FechaEvaluacion', 'Peso', 'Altura', 'IMC',
        'PorcentajeGrasa', 'MasaMuscular', 'NivelHidratacion',
        'SeguimientoDieta', 'Recomendaciones', 'Observaciones'
    ];

    public function gimnasta()
    {
        return $this->belongsTo(Gimnasta::class, 'ID_Gimnasta', 'ID_Gimnasta');
    }
}
