<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Competencia extends Model
{
    use HasFactory;

    protected $table = 'competencias';
    protected $primaryKey = 'ID_Competencia';

    protected $fillable = [
        'NombreCompetencia',
        'Fecha',
        'Ubicacion',
        'Descripcion',
        'activo'
    ];

    protected $casts = [
        'Fecha' => 'date',
        'activo' => 'boolean'
    ];
}
