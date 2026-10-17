<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanFaseSeguimiento extends Model
{
    protected $table = 'plan_fase_seguimiento';
    protected $primaryKey = 'ID_Seguimiento';
    public $timestamps = true;

    protected $fillable = [
        'ID_Plan',
        'ID_Fase',
        'Orden',
        'Fecha_Inicio',
        'Fecha_Fin',
        'Estado',
        'Riesgo_Recaída_Fase',
        'Observaciones',
        'activo',
    ];

    protected $casts = [
        'Orden' => 'integer',
        'Fecha_Inicio' => 'date',
        'Fecha_Fin' => 'date',
        'Riesgo_Recaída_Fase' => 'float',
        'activo' => 'boolean',
    ];

    public function plan()
    {
        return $this->belongsTo(PlanRehabilitacion::class, 'ID_Plan', 'ID_Plan');
    }

    public function fase()
    {
        return $this->belongsTo(FaseRehabilitacion::class, 'ID_Fase', 'ID_Fase');
    }
}
