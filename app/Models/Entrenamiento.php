<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entrenamiento extends Model
{
    use HasFactory;

    protected $table = 'entrenamientos';
    protected $primaryKey = 'ID_Entrenamiento';

    protected $fillable = [
        'ID_Gimnasta',
        'ID_Aparato',
        'Fecha',
        'Duracion',
        'Observaciones',
        'activo'
    ];

    protected $casts = [
        'Fecha' => 'date',
        'activo' => 'boolean'
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'ID_Gimnasta', 'ID_Usuario');
    }

    // Alias para compatibilidad
    public function gimnasta()
    {
        return $this->usuario();
    }

    public function aparato()
    {
        return $this->belongsTo(Aparato::class, 'ID_Aparato', 'ID_Aparato');
    }
}
