<?php

namespace App\Http\Controllers; // Nota: Asegúrate de que este archivo esté en App\Models, si lo copias entero cambia esto a namespace App\Models;

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    use HasFactory;

    protected $table = 'usuarios';
    protected $primaryKey = 'ID_Usuario';
    
    // CORRECCIÓN CLAVE: Tu SQL físico no tiene columnas 'created_at' ni 'updated_at' en esta tabla.
    // Al ponerlo en false evitamos errores SQL de inserción.
    public $timestamps = false; 

    protected $fillable = [
        'NombreUsuario',
        'Contrasena', // CORRECCIÓN: Cambiado de Contraseña a Contrasena (idéntico al SQL de Laragon)
        'ID_Rol',
        'Email',
        'CedulaIdentidad',
        'FotoPerfil',
        'Nombre',
        'Apellido',
        'FechaNacimiento',
        'Nacionalidad',
        'Peso',
        'Altura',
        'Nivel',
        'Contacto',
        'ID_Entrenador',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'FechaNacimiento' => 'date',
        'Peso' => 'decimal:2',
        'Altura' => 'decimal:2',
    ];

    protected $hidden = [
        'Contrasena', // CORRECCIÓN: Ocultamos el campo correcto sin caracteres especiales
    ];

    /**
     * Reemplazo del Authnatible de Laravel para que Eloquent sepa dónde buscar el password
     */
    public function getAuthPassword()
    {
        return $this->Contrasena;
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function getRouteKeyName()
    {
        return 'ID_Usuario';
    }

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'ID_Rol', 'ID_Rol');
    }

    // Scopes por tipo de usuario
    public function scopeGimnastas($query)
    {
        return $query->whereHas('rol', function($q) {
            $q->where('NombreRol', 'Gimnasta');
        });
    }

    public function scopeEntrenadores($query)
    {
        return $query->whereHas('rol', function($q) {
            $q->where('NombreRol', 'Entrenador');
        });
    }

    public function scopeMedicos($query)
    {
        return $query->whereHas('rol', function($q) {
            $q->whereIn('NombreRol', ['Médico', 'Fisioterapeuta']);
        });
    }

    public function entrenador()
    {
        return $this->belongsTo(Usuario::class, 'ID_Entrenador', 'ID_Usuario');
    }

    public function lesiones()
    {
        return $this->hasMany(Lesion::class, 'ID_Gimnasta', 'ID_Usuario');
    }

    public function tieneLesionActiva()
    {
        return $this->lesiones()
            ->where('activo', true)
            ->where('Estado', 'Lesionado')
            ->exists();
    }

    public function sesionesComoGimnasta()
    {
        return $this->hasMany(\App\Models\SesionEntrenamiento::class, 'ID_Gimnasta', 'ID_Usuario');
    }

    public function solicitudesNivel()
    {
        return $this->hasMany(\App\Models\SolicitudNivel::class, 'ID_Gimnasta', 'ID_Usuario');
    }

    public function recomendacionesNivel()
    {
        return $this->hasMany(\App\Models\SolicitudNivel::class, 'ID_Entrenador', 'ID_Usuario');
    }
}