<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanRehabilitacion extends Model
{
    protected $table = 'planes_rehabilitacion';
    protected $primaryKey = 'ID_Plan';
    public $timestamps = true;

    protected $fillable = [
        'ID_Lesion',
        'ID_Gimnasta',
        'ID_Medico',
        'Fecha_Inicio',
        'Fecha_Estimada_Fin',
        'Duracion_Estimada_Dias',
        'Riesgo_Recaída_Total',
        'Secuencia_Fases',
        'Estado',
        'Observaciones',
        'activo',
    ];

    protected $casts = [
        'Fecha_Inicio' => 'date',
        'Fecha_Estimada_Fin' => 'date',
        'Duracion_Estimada_Dias' => 'integer',
        'Riesgo_Recaída_Total' => 'float',
        'Secuencia_Fases' => 'array',
        'activo' => 'boolean',
    ];

    public function lesion()
    {
        return $this->belongsTo(Lesion::class, 'ID_Lesion', 'ID_Lesion');
    }

    public function gimnasta()
    {
        return $this->belongsTo(Usuario::class, 'ID_Gimnasta', 'ID_Usuario');
    }

    public function medico()
    {
        return $this->belongsTo(Usuario::class, 'ID_Medico', 'ID_Usuario');
    }

    public function seguimientos()
    {
        return $this->hasMany(PlanFaseSeguimiento::class, 'ID_Plan', 'ID_Plan');
    }
}
