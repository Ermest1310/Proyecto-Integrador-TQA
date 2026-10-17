<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Rol;

class UsuarioRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'Email' => 'required|email|max:50|unique:usuarios,Email' . ($this->route('id') ? ',' . $this->route('id') . ',ID_Usuario' : ''),
            'CedulaIdentidad' => 'required|string|min:6|max:20|unique:usuarios,CedulaIdentidad' . ($this->route('id') ? ',' . $this->route('id') . ',ID_Usuario' : ''),
            'Nacionalidad' => 'required|string|min:2|max:30',
            'ID_Rol' => 'required|exists:roles,ID_Rol',
            'Nombre' => ['required', 'string', 'min:2', 'max:20', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/', new \App\Rules\SafeString(false, 20)],
            'Apellido' => ['required', 'string', 'min:2', 'max:20', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/', new \App\Rules\SafeString(false, 20)],
        ];

        $rol = Rol::find($this->ID_Rol);
        if ($rol && $rol->NombreRol === 'Gimnasta') {
            $rules = array_merge($rules, [
                'FechaNacimiento' => ['required', 'date', 'before:today', 'after:' . now()->subYears(35)->format('Y-m-d'), 'before:' . now()->subYears(8)->format('Y-m-d')],
                'Peso' => ['required', 'numeric', 'min:30', 'max:120', 'regex:/^\d{2,3}(\.\d{1,2})?$/'],
                'Altura' => ['required', 'numeric', 'min:1.20', 'max:2.00', 'regex:/^[12]\.[0-9]{2}$/'],
                'Nivel' => 'required|in:Principiante,Intermedio,Avanzado,Elite',

            ]);
        } elseif ($rol && in_array($rol->NombreRol, ['Entrenador', 'Médico', 'Fisioterapeuta'])) {
            $rules['Contacto'] = ['required', 'string', 'max:30', 'regex:/^[\+]?[0-9\s\-\(\)]+$/'];
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'FechaNacimiento.after' => 'Edad máxima para gimnasia: 35 años.',
            'FechaNacimiento.before' => 'Edad mínima para gimnasia: 8 años.',
            'Peso.min' => 'Peso mínimo para gimnasia: 30 kg.',
            'Peso.max' => 'Peso máximo para gimnasia: 120 kg.',
            'Altura.min' => 'Altura mínima para gimnasia: 1.20 m.',
            'Altura.max' => 'Altura máxima para gimnasia: 2.00 m.',
            'Contacto.regex' => 'Formato de contacto inválido (solo números, espacios, guiones y paréntesis).'
        ];
    }
}