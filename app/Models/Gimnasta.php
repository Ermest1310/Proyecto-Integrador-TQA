<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gimnasta extends Model
{
    use HasFactory;

    protected $table = 'gimnastas';
    protected $primaryKey = 'ID_Gimnasta';

    protected $fillable = [
        'Nombre', 'Apellido', 'FechaNacimiento', 'Nacionalidad',
        'Peso', 'Altura', 'Nivel', 'ID_Usuario', 'ID_Entrenador',
        'ID_Rol', 'activo'
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'ID_Usuario', 'ID_Usuario');
    }

    public function aptitudesMedicas()
    {
        return $this->hasMany(AptitudMedica::class, 'ID_Gimnasta', 'ID_Gimnasta');
    }

    public function evaluacionesNutricionales()
    {
        return $this->hasMany(EvaluacionNutricional::class, 'ID_Gimnasta', 'ID_Gimnasta');
    }

    public function evaluacionesRespiratorias()
    {
        return $this->hasMany(EvaluacionRespiratoria::class, 'ID_Gimnasta', 'ID_Gimnasta');
    }

    public function evaluacionesPsicologicas()
    {
        return $this->hasMany(EvaluacionPsicologica::class, 'ID_Gimnasta', 'ID_Gimnasta');
    }

    public function evaluacionesFisiologicas()
    {
        return $this->hasMany(EvaluacionFisiologica::class, 'ID_Gimnasta', 'ID_Gimnasta');
    }
}
