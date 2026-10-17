<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LesionRequest extends FormRequest
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
                    $fail('Solo se pueden registrar lesiones de gimnastas.');
                }
            }],
            'ID_Medico' => ['nullable', 'exists:usuarios,ID_Usuario', function($attribute, $value, $fail) {
                if ($value) {
                    $usuario = \App\Models\Usuario::with('rol')->find($value);
                    if (!$usuario || !in_array($usuario->rol->NombreRol, ['Médico', 'Fisioterapeuta'])) {
                        $fail('Solo médicos y fisioterapeutas pueden diagnosticar lesiones.');
                    }
                }
            }],
            'Tipo' => 'required|string|max:30',
            'ParteCuerpo' => 'required|in:' . implode(',', array_keys(\App\Models\Lesion::getPartesCuerpo())),
            'ParteEspecifica' => ['nullable', 'string', 'max:100', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s\-]+$/'],
            'FechaLesion' => ['required', 'date', 'before_or_equal:today', 'after:' . now()->subYears(2)->format('Y-m-d')],
            'FechaDiagnostico' => ['nullable', 'date', 'after_or_equal:FechaLesion', 'before_or_equal:today'],
            'Estado' => 'required|in:' . implode(',', array_keys(\App\Models\Lesion::getEstados())),
            'Gravedad' => 'required|in:' . implode(',', array_keys(\App\Models\Lesion::getGravedades())),
            'Tratamiento' => ['nullable', 'string', 'min:10', 'max:500'],
            'TiempoRecuperacion' => ['nullable', 'integer', 'min:1', 'max:365'],
            'FechaRecuperacion' => ['nullable', 'date', 'after_or_equal:FechaLesion'],
            'Observaciones' => ['nullable', 'string', 'max:1000']
        ];
    }

    public function messages()
    {
        return [
            'FechaLesion.after' => 'La fecha no puede ser mayor a 2 años.',
            'FechaDiagnostico.after_or_equal' => 'El diagnóstico no puede ser anterior a la lesión.',
            'ParteEspecifica.regex' => 'Parte específica solo puede contener letras y guiones.',
            'ID_Gimnasta.exists' => 'El gimnasta seleccionado no existe.',
            'Tratamiento.min' => 'El tratamiento debe describirse con al menos 10 caracteres.',
            'TiempoRecuperacion.min' => 'Tiempo mínimo de recuperación: 1 día.',
            'TiempoRecuperacion.max' => 'Tiempo máximo de recuperación: 365 días.'
        ];
    }
}