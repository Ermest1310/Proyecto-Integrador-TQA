<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SecurityLogger
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        
        // Log intentos sospechosos
        $this->logSuspiciousActivity($request, $response);
        
        return $response;
    }
    
    private function logSuspiciousActivity($request, $response)
    {
        $suspicious = false;
        $reasons = [];
        
        // Detectar múltiples intentos de login fallidos
        if ($request->is('login') && $response->getStatusCode() === 302 && $response->headers->get('location') === $request->url()) {
            $key = 'failed_login_' . $request->ip();
            $attempts = cache()->get($key, 0) + 1;
            cache()->put($key, $attempts, 300); // 5 minutos
            
            if ($attempts >= 5) {
                $suspicious = true;
                $reasons[] = "Multiple failed login attempts: {$attempts}";
            }
        }
        
        // Detectar SQL injection patterns
        $inputs = $request->all();
        foreach ($inputs as $key => $value) {
            if (is_string($value) && preg_match('/(\bunion\b|\bselect\b|\binsert\b|\bdelete\b|\bdrop\b)/i', $value)) {
                $suspicious = true;
                $reasons[] = "SQL injection pattern in field: {$key}";
            }
        }
        
        // Detectar XSS patterns
        foreach ($inputs as $key => $value) {
            if (is_string($value) && preg_match('/<script|javascript:|on\w+=/i', $value)) {
                $suspicious = true;
                $reasons[] = "XSS pattern in field: {$key}";
            }
        }
        
        if ($suspicious) {
            Log::channel('security')->warning('SUSPICIOUS_ACTIVITY', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'user_id' => session('usuario_id'),
                'reasons' => $reasons,
                'timestamp' => now()->toISOString()
            ]);
        }
    }
}