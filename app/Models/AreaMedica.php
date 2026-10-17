<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AreaMedica extends Model
{
    use HasFactory;

    protected $table = 'area_medica';
    protected $primaryKey = 'ID_AreaMedica';
    protected $fillable = [
        'Nombre',
        'Especialista',
        'TipoConsulta',
        'Descripcion',
    ];
}
