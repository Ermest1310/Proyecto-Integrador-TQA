<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AparatoRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'Nombre' => [
                'required',
                'string',
                'min:3',
                'max:50',
                'unique:aparatos,Nombre' . ($this->route('aparato') ? ',' . $this->route('aparato') . ',ID_Aparato' : ''),
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/'
            ],
            'Descripcion' => ['nullable', 'string', 'max:200'],
            'EstadoMantenimiento' => ['nullable', 'string', 'in:Operativo,Mantenimiento'],
            'UltimoMantenimiento' => ['nullable', 'date', 'before_or_equal:today'],
            'ProximoMantenimiento' => ['nullable', 'date', 'after_or_equal:today']
        ];
    }

    public function messages()
    {
        return [
            'Nombre.min' => 'El nombre debe tener al menos 3 caracteres.',
            'Nombre.regex' => 'El nombre solo puede contener letras y espacios.',
            'Descripcion.max' => 'La descripción no puede exceder 200 caracteres.'
        ];
    }
}