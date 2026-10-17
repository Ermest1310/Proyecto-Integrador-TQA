<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ValidatePostulacion
{
    public function handle(Request $request, Closure $next)
    {
        // Validar que solo admins puedan acceder a postulaciones
        if (!session('is_admin')) {
            return redirect()->route('dashboard')->with('error', 'Acceso denegado.');
        }

        // Validar rate limiting para acciones críticas
        if (in_array($request->route()->getName(), ['postulaciones.aprobar', 'postulaciones.rechazar'])) {
            $key = 'admin_action_' . session('usuario_id');
            if (cache()->has($key) && cache()->get($key) >= 10) {
                return back()->with('error', 'Límite de acciones excedido. Espera 5 minutos.');
            }
            cache()->put($key, (cache()->get($key, 0) + 1), 300);
        }

        return $next($request);
    }
}