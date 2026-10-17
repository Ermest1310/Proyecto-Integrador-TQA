<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\PermissionService;

class CheckPermission
{
    public function handle(Request $request, Closure $next, $permission)
    {
        if (!PermissionService::can($permission)) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Sin permisos'], 403);
            }
            
            return redirect()->route('dashboard')
                ->with('error', 'No tienes permisos para acceder a esta sección');
        }

        return $next($request);
    }
}