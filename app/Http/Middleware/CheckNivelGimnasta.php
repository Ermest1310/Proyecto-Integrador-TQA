<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\SolicitudNivel;

class CheckNivelGimnasta
{
    /**
     * Verifica si el gimnasta tiene el nivel requerido para acceder a la ruta
     */
    public function handle(Request $request, Closure $next, $nivelRequerido)
    {
        $user = auth()->user();
        
        if (!$user || !$user->rol || $user->rol->NombreRol !== 'Gimnasta') {
            return $next($request);
        }

        $nivelesOrden = SolicitudNivel::NIVELES;
        $nivelUsuario = $nivelesOrden[$user->Nivel] ?? 0;
        $nivelMinimo = $nivelesOrden[$nivelRequerido] ?? 0;

        if ($nivelUsuario < $nivelMinimo) {
            return redirect()->back()->with('error', "Necesitas nivel {$nivelRequerido} o superior para acceder a esta función");
        }

        return $next($request);
    }
}
