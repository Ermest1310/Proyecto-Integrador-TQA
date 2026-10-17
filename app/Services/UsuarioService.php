<?php

namespace App\Services;

use App\Models\Rol;

class UsuarioService
{
    public function getValidationRules($rol = null, $isUpdate = false, $userId = null)
    {
        $rules = [
            'NombreUsuario' => [
                'required',
                'string',
                'min:3',
                'max:20',
                'regex:/^[a-zA-Z0-9._-]+$/'
            ],
            'Email' => [
                'required',
                'email',
                'max:30',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/'
            ],
            'Nombre' => [
                'required',
                'string',
                'min:2',
                'max:20',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/'
            ],
            'Apellido' => [
                'required',
                'string',
                'min:2',
                'max:20',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/'
            ],
            'ID_Rol' => 'required|exists:roles,ID_Rol'
        ];

        // Agregar unicidad
        if ($isUpdate && $userId) {
            $rules['NombreUsuario'][] = 'unique:usuarios,NombreUsuario,' . $userId . ',ID_Usuario';
            $rules['Email'][] = 'unique:usuarios,Email,' . $userId . ',ID_Usuario';
        } else {
            $rules['NombreUsuario'][] = 'unique:usuarios,NombreUsuario';
            $rules['Email'][] = 'unique:usuarios,Email';
        }

        // Contraseña solo requerida en creación
        if (!$isUpdate) {
            $rules['Contraseña'] = [
                'required',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/'
            ];
        } else {
            $rules['Contraseña'] = [
                'nullable',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/'
            ];
        }

        // Validaciones específicas por rol
        if ($rol) {
            if ($rol->NombreRol === 'Gimnasta') {
                $rules = array_merge($rules, $this->getGimnastaRules($userId));
            } elseif (in_array($rol->NombreRol, ['Entrenador', 'Médico', 'Fisioterapeuta'])) {
                $rules['Contacto'] = [
                    'required',
                    'string',
                    'max:30',
                    'regex:/^[\+]?[0-9\s\-\(\)]+$/'
                ];
            }
        }

        return $rules;
    }

    private function getGimnastaRules($userId = null)
    {
        return [
            'FechaNacimiento' => 'required|date|before:today|after:1900-01-01',
            'Nacionalidad' => 'required|in:' . implode(',', array_keys(self::getNacionalidades())),
            'Peso' => 'required|numeric|min:20|max:200',
            'Altura' => 'required|numeric|min:0.50|max:2.50',
            'Nivel' => 'required|in:Junior,Senior',
            'ID_Entrenador' => [
                'required',
                'exists:usuarios,ID_Usuario',
                function ($attribute, $value, $fail) use ($userId) {
                    if ($userId && $value == $userId) {
                        $fail('Un usuario no puede ser su propio entrenador.');
                    }
                    $entrenador = \App\Models\Usuario::with('rol')->find($value);
                    if (!$entrenador || $entrenador->rol->NombreRol !== 'Entrenador') {
                        $fail('Debe seleccionar un usuario con rol de Entrenador.');
                    }
                    if (!$entrenador->activo) {
                        $fail('El entrenador seleccionado no está activo.');
                    }
                }
            ]
        ];
    }

    public function getValidationMessages()
    {
        return [
            'NombreUsuario.regex' => 'El nombre de usuario solo puede contener letras, números, puntos, guiones y guiones bajos.',
            'Email.regex' => 'El formato del email no es válido.',
            'Contraseña.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'Contraseña.regex' => 'La contraseña debe contener al menos una mayúscula, una minúscula y un número.',
            'Nombre.regex' => 'El nombre solo puede contener letras y espacios.',
            'Apellido.regex' => 'El apellido solo puede contener letras y espacios.',
            'Contacto.regex' => 'El formato del contacto no es válido.'
        ];
    }

    public static function getNacionalidades()
    {
        return [
            'Argentina' => 'Argentina',
            'Bolivia' => 'Bolivia',
            'Brasil' => 'Brasil',
            'Chile' => 'Chile',
            'Colombia' => 'Colombia',
            'Costa Rica' => 'Costa Rica',
            'Cuba' => 'Cuba',
            'Ecuador' => 'Ecuador',
            'El Salvador' => 'El Salvador',
            'España' => 'España',
            'Estados Unidos' => 'Estados Unidos',
            'Guatemala' => 'Guatemala',
            'Honduras' => 'Honduras',
            'México' => 'México',
            'Nicaragua' => 'Nicaragua',
            'Panamá' => 'Panamá',
            'Paraguay' => 'Paraguay',
            'Perú' => 'Perú',
            'República Dominicana' => 'República Dominicana',
            'Uruguay' => 'Uruguay',
            'Venezuela' => 'Venezuela'
        ];
    }
}