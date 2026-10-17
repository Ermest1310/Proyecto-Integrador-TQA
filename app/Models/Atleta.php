<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Atleta extends Model
{
    use HasFactory;
    
    // 💡 Asegúrate de que el nombre de la tabla sea correcto.
    protected $table = 'atletas'; 
    
    // 💡 Define los campos que se pueden asignar masivamente
    protected $fillable = [
        'Nombre', 
        'lesion_actual', 
        'progreso_rehab',
        // ... otros campos del gimnasta
    ];

    // Si tu tabla no usa timestamps (created_at, updated_at):
    // public $timestamps = false; 

    // Opcional: Relación con el usuario si Atleta es una extensión de la tabla de usuarios
    /*
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id'); 
    }
    */
}