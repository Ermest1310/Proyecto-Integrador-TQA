<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EntrenamientoRequest extends FormRequest
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
                    $fail('Solo se pueden crear entrenamientos para gimnastas.');
                }
                if ($usuario->tieneLesionActiva()) {
                    $fail('El gimnasta tiene una lesión activa y no puede entrenar.');
                }
            }],
            'ID_Aparato' => ['required', 'exists:aparatos,ID_Aparato', function($attribute, $value, $fail) {
                $aparato = \App\Models\Aparato::find($value);
                if (!$aparato || !$aparato->activo) {
                    $fail('El aparato seleccionado no está disponible.');
                }
            }],
            'Fecha' => ['required', 'date', 'before_or_equal:today', 'after:' . now()->subMonths(6)->format('Y-m-d')],
            'Duracion' => ['required', 'integer', 'min:15', 'max:300'],
            'Observaciones' => ['nullable', 'string', 'max:500', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9\s\.\,\-\(\)]+$/']
        ];
    }

    public function messages()
    {
        return [
            'Duracion.min' => 'Duración mínima de entrenamiento: 15 minutos.',
            'Duracion.max' => 'Duración máxima de entrenamiento: 5 horas.',
            'Fecha.after' => 'No se pueden registrar entrenamientos de más de 6 meses atrás.',
            'Fecha.before_or_equal' => 'No se pueden registrar entrenamientos futuros.',
            'Observaciones.regex' => 'Las observaciones contienen caracteres no válidos.'
        ];
    }
}