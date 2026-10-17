<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RolRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rolesPermitidos = [
            'Administrador', 'Gimnasta', 'Entrenador', 'Médico', 'Fisioterapeuta',
            'Nutricionista', 'Psicólogo', 'Coordinador', 'Asistente'
        ];

        return [
            'NombreRol' => [
                'required',
                'string',
                'min:3',
                'max:20',
                'unique:roles,NombreRol' . ($this->route('id') ? ',' . $this->route('id') . ',ID_Rol' : ''),
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
                function ($attribute, $value, $fail) use ($rolesPermitidos) {
                    if (!in_array($value, $rolesPermitidos)) {
                        $fail('Debe ser un rol válido del sistema de gimnasia.');
                    }
                },
                function ($attribute, $value, $fail) {
                    $reservados = ['root', 'system', 'admin', 'superuser', 'guest'];
                    if (in_array(strtolower($value), $reservados)) {
                        $fail('Este nombre de rol está reservado.');
                    }
                }
            ]
        ];
    }

    public function messages()
    {
        return [
            'NombreRol.min' => 'El nombre debe tener al menos 3 caracteres.',
            'NombreRol.max' => 'El nombre no puede exceder 20 caracteres.',
            'NombreRol.unique' => 'Ya existe un rol con este nombre.',
            'NombreRol.regex' => 'El nombre solo puede contener letras y espacios.'
        ];
    }
}