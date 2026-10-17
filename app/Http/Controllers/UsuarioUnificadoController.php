<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Rol;
use App\Models\Entrenamiento;
use App\Models\Lesion;
use App\Models\Postulacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UsuarioUnificadoController extends Controller
{
    public function index(Request $request)
    {
        $query = Usuario::with('rol');
        
        // Filtros
        if ($request->filled('rol')) {
            $query->where('ID_Rol', $request->rol);
        }
        
        if ($request->filled('estado')) {
            $query->where('activo', $request->estado == 'activo');
        }
        
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function($q) use ($buscar) {
                $q->where('Nombre', 'like', "%{$buscar}%")
                  ->orWhere('Apellido', 'like', "%{$buscar}%")
                  ->orWhere('Email', 'like', "%{$buscar}%")
                  ->orWhere('NombreUsuario', 'like', "%{$buscar}%");
            });
        }
        
        $usuarios = $query->orderBy('ID_Usuario')->get();
        
        // Datos para filtros
        $roles = Rol::where('activo', true)->get();
        
        return view('usuarios-unificado.index', compact('usuarios', 'roles'));
    }

    public function store(Request $request)
    {
        \App\Services\PermissionService::abort('users.create');

        $rules = [
            'Email' => 'required|email|max:50|unique:usuarios,Email',
            'CedulaIdentidad' => 'required|string|min:6|max:20|unique:usuarios,CedulaIdentidad',
            'Nacionalidad' => 'required|string|min:2|max:30',
            'ID_Rol' => 'required|exists:roles,ID_Rol',
            'Nombre' => ['required', 'string', 'min:2', 'max:20', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/', new \App\Rules\SafeString(false, 20)],
            'Apellido' => ['required', 'string', 'min:2', 'max:20', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/', new \App\Rules\SafeString(false, 20)],
        ];

        $rol = Rol::find($request->ID_Rol);
        if ($rol && $rol->NombreRol === 'Gimnasta') {
            $rules = array_merge($rules, [
                'FechaNacimiento' => 'required|date|before:today|after:' . now()->subYears(40)->format('Y-m-d'),
                'Peso' => 'required|numeric|min:25|max:150',
                'Altura' => 'required|numeric|min:1.00|max:2.20',
                'Nivel' => 'required|in:Junior,Senior'
            ]);
        } elseif ($rol && in_array($rol->NombreRol, ['Entrenador', 'Médico', 'Fisioterapeuta'])) {
            $rules['Contacto'] = 'required|string|max:30';
        }

        $request->validate($rules);

        // Validar cédula según nacionalidad
        if ($request->Nacionalidad && !Postulacion::validarCedula($request->Nacionalidad, $request->CedulaIdentidad)) {
            return response()->json(['error' => 'Formato de cédula inválido para ' . $request->Nacionalidad], 400);
        }

        $data = $request->all();
        $data['NombreUsuario'] = $request->Email;
        $data['Contraseña'] = bcrypt($request->CedulaIdentidad);
        
        $usuario = Usuario::create($data);
        
        \App\Services\AuditService::created($usuario, ['rol' => $rol->NombreRol]);
        
        return response()->json(['success' => true, 'message' => 'Usuario creado correctamente']);
    }

    public function edit($id)
    {
        $usuario = Usuario::with('rol')->find($id);
        
        if (!$usuario) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }
        
        return response()->json($usuario);
    }

    public function update(Request $request, $id)
    {
        \App\Services\PermissionService::abort('users.edit');

        $usuario = Usuario::with('rol')->find($id);
        
        if (!$usuario) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        if ($usuario->rol->NombreRol === 'Administrador' && $usuario->ID_Usuario == 1) {
            return response()->json(['error' => 'No se puede modificar el administrador principal'], 403);
        }

        $rules = [
            'Email' => 'required|email|max:50|unique:usuarios,Email,' . $usuario->ID_Usuario . ',ID_Usuario',
            'CedulaIdentidad' => 'required|string|min:6|max:20|unique:usuarios,CedulaIdentidad,' . $usuario->ID_Usuario . ',ID_Usuario',
            'Nacionalidad' => 'required|string|min:2|max:30',
            'ID_Rol' => 'required|exists:roles,ID_Rol',
            'Nombre' => ['required', 'string', 'min:2', 'max:20', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/', new \App\Rules\SafeString(false, 20)],
            'Apellido' => ['required', 'string', 'min:2', 'max:20', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/', new \App\Rules\SafeString(false, 20)],
        ];

        $rol = Rol::find($request->ID_Rol);
        if ($rol && $rol->NombreRol === 'Gimnasta') {
            $rules = array_merge($rules, [
                'FechaNacimiento' => 'required|date|before:today|after:' . now()->subYears(40)->format('Y-m-d'),
                'Peso' => 'required|numeric|min:25|max:150',
                'Altura' => 'required|numeric|min:1.00|max:2.20',
                'Nivel' => 'required|in:Junior,Senior'
            ]);
        } elseif ($rol && in_array($rol->NombreRol, ['Entrenador', 'Médico', 'Fisioterapeuta'])) {
            $rules['Contacto'] = 'required|string|max:30';
        }

        $request->validate($rules);

        $data = $request->all();
        $data['NombreUsuario'] = $request->Email;
        $data['Contraseña'] = bcrypt($request->CedulaIdentidad);
        
        $oldData = $usuario->toArray();
        $usuario->update($data);
        
        \App\Services\AuditService::updated($usuario, [
            'old' => $oldData,
            'new' => $usuario->fresh()->toArray()
        ]);
        
        return response()->json(['success' => true, 'message' => 'Usuario actualizado correctamente']);
    }

    public function destroy($id)
    {
        \App\Services\PermissionService::abort('users.delete');

        $usuario = Usuario::with('rol')->find($id);
        
        if (!$usuario) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        if ($usuario->rol->NombreRol === 'Administrador') {
            return response()->json(['error' => 'No se puede desactivar un usuario administrador'], 403);
        }

        if ($usuario->ID_Usuario == session('usuario_id')) {
            return response()->json(['error' => 'No puedes desactivar tu propia cuenta'], 403);
        }
        
        $usuario->update(['activo' => false]);
        
        \App\Services\AuditService::critical('USER_DEACTIVATED', [
            'target_user' => $usuario->Email,
            'target_role' => $usuario->rol->NombreRol
        ]);
        
        return response()->json(['success' => true, 'message' => 'Usuario desactivado correctamente']);
    }

    public function reactivar($id)
    {
        if (!session('is_admin')) {
            return response()->json(['error' => 'No tienes permisos para reactivar usuarios'], 403);
        }

        $usuario = Usuario::find($id);
        
        if (!$usuario) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }
        
        $usuario->update(['activo' => true]);
        return response()->json(['success' => true, 'message' => 'Usuario reactivado correctamente']);
    }

    public function getRoles()
    {
        $roles = Rol::where('activo', true)->get();
        
        return response()->json([
            'roles' => $roles
        ]);
    }
}