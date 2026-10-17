<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompetenciaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'NombreCompetencia' => ['required', 'string', 'min:5', 'max:100', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9\s\-\.]+$/'],
            'Fecha' => ['required', 'date', 'after:today', 'before:' . now()->addYears(2)->format('Y-m-d')],
            'Ubicacion' => ['required', 'string', 'min:3', 'max:150', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9\s\-\,\.]+$/'],
            'Descripcion' => ['nullable', 'string', 'max:500']
        ];
    }

    public function messages()
    {
        return [
            'NombreCompetencia.min' => 'El nombre debe tener al menos 5 caracteres.',
            'NombreCompetencia.regex' => 'El nombre solo puede contener letras, números, espacios, guiones y puntos.',
            'Fecha.after' => 'La competencia debe ser en el futuro.',
            'Fecha.before' => 'No se pueden programar competencias con más de 2 años de anticipación.',
            'Ubicacion.min' => 'La ubicación debe tener al menos 3 caracteres.',
            'Ubicacion.regex' => 'La ubicación contiene caracteres no válidos.',
            'Descripcion.max' => 'La descripción no puede exceder 500 caracteres.'
        ];
    }
}