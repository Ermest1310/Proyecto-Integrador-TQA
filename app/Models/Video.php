<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected $table = 'videos';
    protected $primaryKey = 'ID_Video';

    protected $fillable = [
        'ID_Usuario', 'NombreArchivo', 'NombreOriginal', 'Tipo', 'Tamano', 'Descripcion'
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'ID_Usuario', 'ID_Usuario');
    }

    public function poseResultados()
    {
        return $this->hasMany(PoseResultado::class, 'ID_Video', 'ID_Video');
    }
}
