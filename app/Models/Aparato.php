<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aparato extends Model
{
    use HasFactory;

    protected $table = 'aparatos';
    protected $primaryKey = 'ID_Aparato';

    protected $fillable = [
        'Nombre',
        'Descripcion',
        'UltimoMantenimiento',
        'ProximoMantenimiento',
        'EstadoMantenimiento',
        'activo'
    ];

    protected $casts = [
        'UltimoMantenimiento' => 'date',
        'ProximoMantenimiento' => 'date',
        'activo' => 'boolean'
    ];

    public function necesitaMantenimiento()
    {
        return $this->ProximoMantenimiento && $this->ProximoMantenimiento <= now()->addDays(7);
    }

    public function estaOperativo()
    {
        return $this->EstadoMantenimiento === 'Operativo' && $this->activo;
    }

    public function getEnMantenimientoAttribute()
    {
        return $this->EstadoMantenimiento === 'Mantenimiento';
    }
}
