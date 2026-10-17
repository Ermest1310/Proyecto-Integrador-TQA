<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Services\SanitizationService;

class SafeString implements Rule
{
    private $allowHtml;
    private $maxLength;

    public function __construct($allowHtml = false, $maxLength = 255)
    {
        $this->allowHtml = $allowHtml;
        $this->maxLength = $maxLength;
    }

    public function passes($attribute, $value)
    {
        if (!is_string($value)) {
            return false;
        }

        // Verificar longitud
        if (strlen($value) > $this->maxLength) {
            return false;
        }

        // Detectar contenido malicioso
        if (SanitizationService::detectMalicious($value)) {
            return false;
        }

        // Verificar HTML si no está permitido
        if (!$this->allowHtml && $value !== strip_tags($value)) {
            return false;
        }

        return true;
    }

    public function message()
    {
        return 'El campo contiene caracteres no válidos o es demasiado largo.';
    }
}