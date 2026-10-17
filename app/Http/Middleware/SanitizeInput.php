<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\SanitizationService;
use Illuminate\Support\Facades\Log;

class SanitizeInput
{
    public function handle(Request $request, Closure $next)
    {
        $input = $request->all();
        
        // Detectar contenido malicioso antes de sanitizar
        $this->detectMaliciousContent($input);
        
        // Sanitizar entradas
        $sanitized = $this->sanitizeInput($input);
        
        // Reemplazar input del request
        $request->replace($sanitized);
        
        return $next($request);
    }
    
    private function detectMaliciousContent($input)
    {
        foreach ($input as $key => $value) {
            if (is_string($value) && SanitizationService::detectMalicious($value)) {
                Log::channel('security')->warning('MALICIOUS_INPUT_DETECTED', [
                    'field' => $key,
                    'value' => $value,
                    'ip' => request()->ip(),
                    'user_id' => session('usuario_id'),
                    'url' => request()->fullUrl()
                ]);
                
                // Bloquear request malicioso
                abort(422, 'Entrada no válida detectada');
            }
        }
    }
    
    private function sanitizeInput($input)
    {
        $sanitized = [];
        
        foreach ($input as $key => $value) {
            if (is_array($value)) {
                $sanitized[$key] = $this->sanitizeInput($value);
            } else {
                $sanitized[$key] = $this->sanitizeByField($key, $value);
            }
        }
        
        return $sanitized;
    }
    
    private function sanitizeByField($field, $value)
    {
        // Campos específicos
        if (in_array($field, ['Nombre', 'Apellido', 'NombreRol', 'NombreCompetencia'])) {
            return SanitizationService::sanitizeName($value);
        }
        
        if (in_array($field, ['Email', 'username'])) {
            return SanitizationService::sanitizeEmail($value);
        }
        
        if (in_array($field, ['Peso', 'Altura', 'IMC'])) {
            return SanitizationService::sanitizeNumeric($value, true);
        }
        
        if (in_array($field, ['ID_Rol', 'ID_Usuario', 'ID_Entrenador'])) {
            return SanitizationService::sanitizeNumeric($value);
        }
        
        // Sanitización general
        return SanitizationService::sanitizeString($value);
    }
}