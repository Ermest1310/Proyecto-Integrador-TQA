<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ElementoRutina extends Model
{
    protected $table = 'elemento_rutinas';
    protected $primaryKey = 'ID_Elemento';
    public $timestamps = false;

    protected $fillable = [
        'ID_Aparato',
        'Nombre',
        'Grupo',
        'ValorDificultad',
        'MinEnlace',
        'IDsConexion',
        'Descripcion',
        'activo',
    ];

    protected $casts = [
        'ID_Aparato' => 'integer',
        'ValorDificultad' => 'float',
        'MinEnlace' => 'integer',
        'IDsConexion' => 'array',
        'activo' => 'boolean',
    ];

    public function aparato()
    {
        return $this->belongsTo(Aparato::class, 'ID_Aparato', 'ID_Aparato');
    }

    public function calificaciones()
    {
        return $this->hasMany(CalificacionElemento::class, 'ID_Elemento', 'ID_Elemento');
    }

    public function elementosConectados()
    {
        $ids = $this->IDsConexion ?? [];
        return self::whereIn('ID_Elemento', $ids)->get();
    }

    public function puedeConectarCon(ElementoRutina $otro): bool
    {
        $ids = $this->IDsConexion ?? [];
        return in_array($otro->ID_Elemento, $ids);
    }

    public static function getPorAparato(int $aparatoId)
    {
        return self::where('ID_Aparato', $aparatoId)
            ->where('activo', true)
            ->orderBy('Grupo')
            ->orderBy('Nombre')
            ->get();
    }

    public static function getGrupos()
    {
        return ['A', 'B', 'C', 'D', 'E', 'F'];
    }
}
