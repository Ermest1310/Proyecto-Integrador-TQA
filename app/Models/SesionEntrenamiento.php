<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class SesionEntrenamiento extends Model
{
    use HasFactory;

    protected $table = 'sesiones_entrenamiento';
    protected $primaryKey = 'ID_Sesion';
    public $timestamps = true;

    protected $fillable = [
        'ID_Gimnasta',
        'ID_Entrenador',
        'ID_Aparato',
        'Fecha',
        'Hora_Inicio',
        'Hora_Fin',
        'Duracion_Minutos',
        'Tipo_Asignacion',
        'Estado',
        'Observaciones_Entrenador',
        'Notas_Gimnasta',
        'Asistio_Gimnasta',
        'Hora_Llegada_Gimnasta',
        'Hora_Salida_Gimnasta',
        'Calificacion_Sesion',
        'Comentarios_Finales',
        'activo'
    ];

    protected $casts = [
        'Fecha' => 'date',
        'Hora_Inicio' => 'datetime:H:i',
        'Hora_Fin' => 'datetime:H:i',
        'Hora_Llegada_Gimnasta' => 'datetime',
        'Hora_Salida_Gimnasta' => 'datetime',
        'Asistio_Gimnasta' => 'boolean',
        'activo' => 'boolean',
        'Duracion_Minutos' => 'integer',
        'Calificacion_Sesion' => 'integer'
    ];

    // Relaciones
    public function gimnasta()
    {
        return $this->belongsTo(Usuario::class, 'ID_Gimnasta', 'ID_Usuario');
    }

    public function entrenador()
    {
        return $this->belongsTo(Usuario::class, 'ID_Entrenador', 'ID_Usuario');
    }

    public function aparato()
    {
        return $this->belongsTo(Aparato::class, 'ID_Aparato', 'ID_Aparato');
    }

    // Scopes
    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }

    public function scopeProgramadas($query)
    {
        return $query->where('Estado', 'Programada');
    }

    public function scopeHoy($query)
    {
        return $query->whereDate('Fecha', today());
    }

    public function scopeEntrenador($query, $entrenadorId)
    {
        return $query->where('ID_Entrenador', $entrenadorId);
    }

    // Métodos auxiliares
    public static function asignarEntrenadorAutomatico($fecha, $horaInicio, $horaFin, $gimnastaId = null, $excludeSesionId = null)
    {
        // Obtener entrenadores activos
        $entrenadores = Usuario::whereHas('rol', function($q) {
            $q->where('NombreRol', 'Entrenador');
        })->where('activo', true)->get();

        if ($entrenadores->isEmpty()) {
            return null;
        }

        // Contar sesiones por entrenador en el último mes para balancear carga
        $entrenadorCarga = [];
        foreach ($entrenadores as $entrenador) {
            $sesiones = self::where('ID_Entrenador', $entrenador->ID_Usuario)
                ->where('Fecha', '>=', now()->subMonth())
                ->count();
            
            // Verificar disponibilidad en el horario solicitado
            $query = self::where('ID_Entrenador', $entrenador->ID_Usuario)
                ->where('Fecha', $fecha)
                ->where('Estado', '!=', 'Cancelada')
                ->where(function($q) use ($horaInicio, $horaFin) {
                    $q->where(function($q1) use ($horaInicio, $horaFin) {
                        // Conflicto: nueva sesión inicia durante una existente
                        $q1->where('Hora_Inicio', '<=', $horaInicio)
                           ->where('Hora_Fin', '>', $horaInicio);
                    })->orWhere(function($q2) use ($horaInicio, $horaFin) {
                        // Conflicto: nueva sesión termina durante una existente
                        $q2->where('Hora_Inicio', '<', $horaFin)
                           ->where('Hora_Fin', '>=', $horaFin);
                    })->orWhere(function($q3) use ($horaInicio, $horaFin) {
                        // Conflicto: nueva sesión engloba una existente
                        $q3->where('Hora_Inicio', '>=', $horaInicio)
                           ->where('Hora_Fin', '<=', $horaFin);
                    });
                });
                
            if ($excludeSesionId) {
                $query->where('ID_Sesion', '!=', $excludeSesionId);
            }
            
            $ocupado = $query->exists();

            if (!$ocupado) {
                $entrenadorCarga[$entrenador->ID_Usuario] = $sesiones;
            }
        }

        // Si no hay entrenadores disponibles
        if (empty($entrenadorCarga)) {
            return null;
        }

        // Retornar el entrenador con menos carga
        return array_search(min($entrenadorCarga), $entrenadorCarga);
    }

    public static function entrenadoresDisponibles($fecha, $horaInicio, $horaFin)
    {
        $entrenadores = Usuario::whereHas('rol', function($q) {
            $q->where('NombreRol', 'Entrenador');
        })->where('activo', true)->get();

        $disponibles = [];
        foreach ($entrenadores as $entrenador) {
            $ocupado = self::where('ID_Entrenador', $entrenador->ID_Usuario)
                ->where('Fecha', $fecha)
                ->where('Estado', '!=', 'Cancelada')
                ->where(function($q) use ($horaInicio, $horaFin) {
                    $q->where(function($q1) use ($horaInicio, $horaFin) {
                        // Conflicto: nueva sesión inicia durante una existente
                        $q1->where('Hora_Inicio', '<=', $horaInicio)
                           ->where('Hora_Fin', '>', $horaInicio);
                    })->orWhere(function($q2) use ($horaInicio, $horaFin) {
                        // Conflicto: nueva sesión termina durante una existente
                        $q2->where('Hora_Inicio', '<', $horaFin)
                           ->where('Hora_Fin', '>=', $horaFin);
                    })->orWhere(function($q3) use ($horaInicio, $horaFin) {
                        // Conflicto: nueva sesión engloba una existente
                        $q3->where('Hora_Inicio', '>=', $horaInicio)
                           ->where('Hora_Fin', '<=', $horaFin);
                    });
                })
                ->exists();

            if (!$ocupado) {
                $disponibles[] = $entrenador;
            }
        }

        return collect($disponibles);
    }

    // Calcular duración automáticamente
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($sesion) {
            if ($sesion->Hora_Inicio && $sesion->Hora_Fin) {
                $inicio = Carbon::parse($sesion->Hora_Inicio);
                $fin = Carbon::parse($sesion->Hora_Fin);
                $sesion->Duracion_Minutos = $fin->diffInMinutes($inicio);
            }
        });
    }
}