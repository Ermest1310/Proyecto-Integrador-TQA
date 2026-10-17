<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class SesionEntrenamientoRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'ID_Gimnasta' => ['required', 'exists:usuarios,ID_Usuario', function($attribute, $value, $fail) {
                $usuario = \App\Models\Usuario::with('rol')->find($value);
                if (!$usuario || $usuario->rol->NombreRol !== 'Gimnasta') {
                    $fail('Solo se pueden programar sesiones para gimnastas.');
                }
                if ($usuario->tieneLesionActiva()) {
                    $fail('El gimnasta tiene una lesión activa y no puede entrenar.');
                }
            }],
            'ID_Entrenador' => ['nullable', 'exists:usuarios,ID_Usuario', function($attribute, $value, $fail) {
                if ($value) {
                    $usuario = \App\Models\Usuario::with('rol')->find($value);
                    if (!$usuario || $usuario->rol->NombreRol !== 'Entrenador') {
                        $fail('Solo se pueden asignar entrenadores válidos.');
                    }
                }
            }],
            'ID_Aparato' => ['nullable', 'exists:aparatos,ID_Aparato', function($attribute, $value, $fail) {
                if ($value) {
                    $aparato = \App\Models\Aparato::find($value);
                    if (!$aparato || !$aparato->activo || !$aparato->estaOperativo()) {
                        $fail('El aparato seleccionado no está disponible o está en mantenimiento.');
                    }
                    
                    // Validar que no tenga mantenimiento en la fecha de la sesión
                    if ($aparato->ProximoMantenimiento && $this->Fecha) {
                        $fechaSesion = \Carbon\Carbon::parse($this->Fecha);
                        $fechaMantenimiento = \Carbon\Carbon::parse($aparato->ProximoMantenimiento);
                        
                        if ($fechaSesion->equalTo($fechaMantenimiento)) {
                            $fail('El aparato tiene mantenimiento programado para esa fecha (' . $fechaMantenimiento->format('d/m/Y') . ').');
                        }
                    }
                }
            }],
            'Fecha' => ['required', 'date', 'after_or_equal:today', 'before:' . now()->addMonths(3)->format('Y-m-d')],
            'Hora_Inicio' => ['required', 'date_format:H:i', function($attribute, $value, $fail) {
                $hora = Carbon::createFromFormat('H:i', $value);
                if ($hora->hour < 6 || $hora->hour > 22) {
                    $fail('Las sesiones solo pueden programarse entre 06:00 y 22:00.');
                }
            }],
            'Hora_Fin' => ['required', 'date_format:H:i', 'after:Hora_Inicio', function($attribute, $value, $fail) {
                if ($this->Hora_Inicio) {
                    $inicio = Carbon::createFromFormat('H:i', $this->Hora_Inicio);
                    $fin = Carbon::createFromFormat('H:i', $value);
                    $duracion = $fin->diffInMinutes($inicio);
                    
                    if ($duracion < 30) {
                        $fail('La sesión debe durar mínimo 30 minutos.');
                    }
                    if ($duracion > 180) {
                        $fail('La sesión no puede durar más de 3 horas.');
                    }
                }
                
                // Validar disponibilidad del gimnasta
                $conflictoGimnasta = \App\Models\SesionEntrenamiento::where('ID_Gimnasta', $this->ID_Gimnasta)
                    ->where('Fecha', $this->Fecha)
                    ->where('Estado', '!=', 'Cancelada')
                    ->where(function($q) {
                        $q->where(function($q1) {
                            $q1->where('Hora_Inicio', '<=', $this->Hora_Inicio)
                               ->where('Hora_Fin', '>', $this->Hora_Inicio);
                        })->orWhere(function($q2) {
                            $q2->where('Hora_Inicio', '<', $this->Hora_Fin)
                               ->where('Hora_Fin', '>=', $this->Hora_Fin);
                        })->orWhere(function($q3) {
                            $q3->where('Hora_Inicio', '>=', $this->Hora_Inicio)
                               ->where('Hora_Fin', '<=', $this->Hora_Fin);
                        });
                    });
                    
                if ($this->route('id')) {
                    $conflictoGimnasta->where('ID_Sesion', '!=', $this->route('id'));
                }
                
                if ($conflictoGimnasta->exists()) {
                    $fail('El gimnasta ya tiene una sesión programada en ese horario.');
                }
                
                // Validar disponibilidad del entrenador si se especifica
                if ($this->ID_Entrenador) {
                    $conflictoEntrenador = \App\Models\SesionEntrenamiento::where('ID_Entrenador', $this->ID_Entrenador)
                        ->where('Fecha', $this->Fecha)
                        ->where('Estado', '!=', 'Cancelada')
                        ->where(function($q) {
                            $q->where(function($q1) {
                                $q1->where('Hora_Inicio', '<=', $this->Hora_Inicio)
                                   ->where('Hora_Fin', '>', $this->Hora_Inicio);
                            })->orWhere(function($q2) {
                                $q2->where('Hora_Inicio', '<', $this->Hora_Fin)
                                   ->where('Hora_Fin', '>=', $this->Hora_Fin);
                            })->orWhere(function($q3) {
                                $q3->where('Hora_Inicio', '>=', $this->Hora_Inicio)
                                   ->where('Hora_Fin', '<=', $this->Hora_Fin);
                            });
                        });
                        
                    if ($this->route('id')) {
                        $conflictoEntrenador->where('ID_Sesion', '!=', $this->route('id'));
                    }
                    
                    if ($conflictoEntrenador->exists()) {
                        $fail('El entrenador seleccionado ya tiene una sesión programada en ese horario.');
                    }
                }
            }]
        ];
    }

    public function messages()
    {
        return [
            'Fecha.after_or_equal' => 'No se pueden programar sesiones en el pasado.',
            'Fecha.before' => 'No se pueden programar sesiones con más de 3 meses de anticipación.',
            'Hora_Inicio.date_format' => 'Formato de hora inválido (HH:MM).',
            'Hora_Fin.after' => 'La hora de fin debe ser posterior a la hora de inicio.',
        ];
    }
}