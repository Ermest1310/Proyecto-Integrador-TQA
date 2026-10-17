<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FaseRehabilitacion extends Model
{
    protected $table = 'fases_rehabilitacion';
    protected $primaryKey = 'ID_Fase';
    public $timestamps = false;

    protected $fillable = [
        'Nombre',
        'Descripcion',
        'Orden',
        'Duracion_Minima_Dias',
        'Duracion_Maxima_Dias',
        'Riesgo_Recaída',
        'Tipos_Lesion_Validos',
        'Gravedades_Validas',
        'Fases_Siguientes',
        'Es_Inicial',
        'Es_Final',
        'activo',
    ];

    protected $casts = [
        'Orden' => 'integer',
        'Duracion_Minima_Dias' => 'integer',
        'Duracion_Maxima_Dias' => 'integer',
        'Riesgo_Recaída' => 'float',
        'Tipos_Lesion_Validos' => 'array',
        'Gravedades_Validas' => 'array',
        'Fases_Siguientes' => 'array',
        'Es_Inicial' => 'boolean',
        'Es_Final' => 'boolean',
        'activo' => 'boolean',
    ];

    public function planesSeguimiento()
    {
        return $this->hasMany(PlanFaseSeguimiento::class, 'ID_Fase', 'ID_Fase');
    }

    public static function getFasesIniciales()
    {
        return self::where('Es_Inicial', true)
            ->where('activo', true)
            ->orderBy('Orden')
            ->get();
    }

    public static function getFaseFinal()
    {
        return self::where('Es_Final', true)
            ->where('activo', true)
            ->first();
    }
}
