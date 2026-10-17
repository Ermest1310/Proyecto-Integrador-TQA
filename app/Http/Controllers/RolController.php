<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use App\Models\Usuario;
use App\Services\PermissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RolController extends Controller
{
    public function index()
    {
        $roles = Rol::orderBy('ID_Rol')->get();
        $permisos = PermissionService::getPermissionsMatrix();
        $usuariosPorRol = Usuario::selectRaw('ID_Rol, COUNT(*) as total')
            ->where('activo', true)
            ->groupBy('ID_Rol')
            ->pluck('total', 'ID_Rol');
        $totalUsuarios = Usuario::where('activo', true)->count();
        $totalPermisos = collect($permisos)->flatten()->unique()->count();

        return view('roles.index', compact('roles', 'permisos', 'usuariosPorRol', 'totalUsuarios', 'totalPermisos'));
    }

    public function store(Request $request)
    {
        \App\Services\PermissionService::abort('roles.create');

        $request->validate([
            'NombreRol' => [
                'required',
                'string',
                'min:3',
                'max:20',
                'unique:roles,NombreRol',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
                function ($attribute, $value, $fail) {
                    $reservados = ['root', 'system', 'admin', 'superuser'];
                    if (in_array(strtolower($value), $reservados)) {
                        $fail('Este nombre de rol está reservado.');
                    }
                }
            ]
        ], [
            'NombreRol.required' => 'El nombre del rol es obligatorio.',
            'NombreRol.min' => 'El nombre debe tener al menos 3 caracteres.',
            'NombreRol.max' => 'El nombre no puede exceder 20 caracteres.',
            'NombreRol.unique' => 'Ya existe un rol con este nombre.',
            'NombreRol.regex' => 'El nombre solo puede contener letras y espacios.'
        ]);

        $rol = Rol::create(['NombreRol' => trim($request->NombreRol)]);
        
        Log::info('Rol creado', [
            'rol_id' => $rol->ID_Rol,
            'nombre_rol' => $rol->NombreRol,
            'admin' => session('usuario_id')
        ]);

        return response()->json(['success' => true, 'message' => 'Rol creado correctamente']);
    }

    public function edit($id)
    {
        $rol = Rol::find($id);
        
        if (!$rol) {
            return response()->json(['error' => 'Rol no encontrado'], 404);
        }
        
        return response()->json($rol);
    }

    public function update(Request $request, $id)
    {
        $rol = Rol::find($id);
        
        if (!$rol) {
            return response()->json(['error' => 'Rol no encontrado'], 404);
        }

        \App\Services\PermissionService::abort('roles.edit');
        
        // Proteger roles del sistema
        $rolesProtegidos = ['Administrador', 'Gimnasta'];
        if (in_array($rol->NombreRol, $rolesProtegidos) && !\App\Services\PermissionService::isAdmin()) {
            return response()->json(['error' => 'No puedes modificar roles del sistema'], 403);
        }
        
        $request->validate([
            'NombreRol' => [
                'required',
                'string',
                'min:3',
                'max:20',
                'unique:roles,NombreRol,' . $rol->ID_Rol . ',ID_Rol',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
                function ($attribute, $value, $fail) {
                    $reservados = ['root', 'system', 'admin', 'superuser'];
                    if (in_array(strtolower($value), $reservados)) {
                        $fail('Este nombre de rol está reservado.');
                    }
                }
            ]
        ], [
            'NombreRol.required' => 'El nombre del rol es obligatorio.',
            'NombreRol.min' => 'El nombre debe tener al menos 3 caracteres.',
            'NombreRol.max' => 'El nombre no puede exceder 20 caracteres.',
            'NombreRol.unique' => 'Ya existe un rol con este nombre.',
            'NombreRol.regex' => 'El nombre solo puede contener letras y espacios.'
        ]);

        $rol->update(['NombreRol' => trim($request->NombreRol)]);

        return response()->json(['success' => true, 'message' => 'Rol actualizado correctamente']);
    }

    public function destroy($id)
    {
        try {
            $rol = Rol::findOrFail($id);
            
            // Proteger roles críticos
            $rolesCriticos = ['Administrador', 'Gimnasta', 'Médico', 'Fisioterapeuta', 'Entrenador'];
            if (in_array($rol->NombreRol, $rolesCriticos)) {
                return response()->json(['message' => 'No se puede desactivar este rol del sistema'], 400);
            }

            // Verificar usuarios asignados
            $usuariosAsignados = \App\Models\Usuario::where('ID_Rol', $rol->ID_Rol)->where('activo', true)->count();
            if ($usuariosAsignados > 0) {
                return response()->json(['message' => "No se puede desactivar. Hay {$usuariosAsignados} usuario(s) con este rol"], 400);
            }
            
            $rol->activo = false;
            $rol->save();
            
            return response()->json(['message' => 'Rol desactivado correctamente']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al desactivar el rol'], 500);
        }
    }

    public function reactivar($id)
    {
        \App\Services\PermissionService::abort('roles.edit');
        
        $rol = Rol::find($id);
        
        if (!$rol) {
            return response()->json(['error' => 'Rol no encontrado'], 404);
        }
        
        $rol->update(['activo' => true]);
        return response()->json(['success' => true, 'message' => 'Rol reactivado correctamente']);
    }

    public function desactivar($id)
    {
        try {
            $rol = Rol::findOrFail($id);
            
            // Proteger roles críticos
            $rolesCriticos = ['Administrador', 'Gimnasta', 'Médico', 'Fisioterapeuta', 'Entrenador'];
            if (in_array($rol->NombreRol, $rolesCriticos)) {
                return response()->json(['message' => 'No se puede desactivar este rol del sistema'], 400);
            }

            // Verificar usuarios asignados
            $usuariosAsignados = \App\Models\Usuario::where('ID_Rol', $rol->ID_Rol)->where('activo', true)->count();
            if ($usuariosAsignados > 0) {
                return response()->json(['message' => "No se puede desactivar. Hay {$usuariosAsignados} usuario(s) con este rol"], 400);
            }
            
            $rol->activo = false;
            $rol->save();
            
            return response()->json(['message' => 'Rol desactivado correctamente']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al desactivar el rol'], 500);
        }
    }
}
