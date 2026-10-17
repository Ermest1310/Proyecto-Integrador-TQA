<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolicitudNivel extends Model
{
    use HasFactory;

    protected $table = 'solicitudes_nivel';
    protected $primaryKey = 'ID_Solicitud';

    protected $fillable = [
        'ID_Gimnasta',
        'nivel_actual',
        'nivel_solicitado',
        'tipo',
        'ID_Entrenador',
        'estado',
        'comentarios_gimnasta',
        'comentarios_entrenador',
        'comentarios_admin',
        'fecha_solicitud',
        'fecha_respuesta',
        'aprobado_por'
    ];

    protected $casts = [
        'fecha_solicitud' => 'datetime',
        'fecha_respuesta' => 'datetime',
    ];

    const NIVELES = [
        'Junior' => 1,
        'Senior' => 2
    ];

    public function gimnasta()
    {
        return $this->belongsTo(Usuario::class, 'ID_Gimnasta', 'ID_Usuario');
    }

    public function entrenador()
    {
        return $this->belongsTo(Usuario::class, 'ID_Entrenador', 'ID_Usuario');
    }

    public function aprobador()
    {
        return $this->belongsTo(Usuario::class, 'aprobado_por', 'ID_Usuario');
    }

    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeAprobadas($query)
    {
        return $query->where('estado', 'aprobada');
    }

    public function scopeRechazadas($query)
    {
        return $query->where('estado', 'rechazada');
    }

    public static function siguienteNivel($nivelActual)
    {
        $niveles = array_keys(self::NIVELES);
        $index = array_search($nivelActual, $niveles);
        
        if ($index !== false && $index < count($niveles) - 1) {
            return $niveles[$index + 1];
        }
        
        return null;
    }

    public static function puedeSubirNivel($nivelActual, $nivelSolicitado)
    {
        $nivelActualNum = self::NIVELES[$nivelActual] ?? 0;
        $nivelSolicitadoNum = self::NIVELES[$nivelSolicitado] ?? 0;
        
        return $nivelSolicitadoNum === $nivelActualNum + 1;
    }
}
