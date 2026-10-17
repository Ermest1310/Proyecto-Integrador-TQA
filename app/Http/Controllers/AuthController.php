<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario;
use App\Models\Postulacion;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // 1. Añadimos CedulaIdentidad si el sistema lo requiere según tu mensaje de error
        $request->validate([
            'username' => ['required', 'email', 'max:50'],
            'nacionalidad' => ['required', 'string', 'min:2', 'max:30'],
            'cedula' => ['required', 'string', 'min:5', 'max:20'], // Captura de documento
            'password' => ['required', 'string', 'min:6', 'max:20']
        ], [
            'username.email' => 'Debe ser un email válido',
            'password.min' => 'Mínimo 6 caracteres',
            'nacionalidad.min' => 'Nacionalidad muy corta',
            'cedula.required' => 'El documento de identidad es obligatorio'
        ]);

        // 2. Ajustamos la consulta con las columnas reales de tu SQL (CedulaIdentidad)
        $usuario = Usuario::with('rol')
            ->where('Email', $request->username)
            ->where('Nacionalidad', $request->nacionalidad)
            ->where('CedulaIdentidad', $request->cedula) // Validación por documento
            ->where('activo', true)
            ->first();

        if ($usuario) {
            // 3. CORRECCIÓN CLAVE: Cambiado de $usuario->Contraseña a $usuario->Contrasena (según tu tabla SQL)
            if (Hash::check($request->password, $usuario->Contrasena)) {
                
                session([
                    'usuario_id' => $usuario->ID_Usuario,
                    'usuario_nombre' => $usuario->Email,
                    'usuario_rol' => $usuario->rol->NombreRol,
                    'is_admin' => $usuario->rol->NombreRol === 'Administrador'
                ]);
                
                \App\Services\AuditService::login(true, $request->only(['username', 'nacionalidad', 'cedula']));
                
                // Redireccionar según el rol (mapeado exactamente a tus 6 roles del SQL)
                switch($usuario->rol->NombreRol) {
                    case 'Administrador':
                        return redirect()->route('admin.inicio');
                    case 'Entrenador':
                        return redirect()->route('entrenador.dashboard');
                    case 'Médico':
                        return redirect()->route('medico.dashboard');
                    case 'Gimnasta':
                        return redirect()->route('gimnasta.dashboard');
                    default:
                        return redirect()->route('admin.inicio');
                }
            } else {
                // Contraseña incorrecta
                \App\Services\AuditService::login(false, $request->only(['username', 'nacionalidad', 'cedula', 'password']));
                \App\Services\AuditService::security('INVALID_PASSWORD', [
                    'email' => $request->username,
                    'nacionalidad' => $request->nacionalidad
                ]);
            }
        } else {
            // Usuario no encontrado
            \App\Services\AuditService::login(false, $request->only(['username', 'nacionalidad', 'cedula', 'password']));
            \App\Services\AuditService::security('USER_NOT_FOUND', [
                'email' => $request->username,
                'nacionalidad' => $request->nacionalidad
            ]);
        }

        return back()->withErrors(['username' => 'Credenciales incorrectas. Verifica email, nacionalidad, documento y contraseña.'])->withInput();
    }

    public function register(Request $request)
    {
        // Rate limiting - máximo 3 registros por IP por hora
        $key = 'registro_' . $request->ip();
        if (cache()->has($key) && cache()->get($key) >= 3) {
            \App\Services\AuditService::security('RATE_LIMIT_EXCEEDED', [
                'type' => 'registro',
                'ip' => $request->ip()
            ]);
            return back()->withErrors(['Email' => 'Límite de registros excedido. Intenta en 1 hora.'])->withInput();
        }
        
        // Validaciones alineadas con la tabla 'usuarios'
        $request->validate([
            'Nombre' => ['required', 'string', 'min:2', 'max:20', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/'],
            'Apellido' => ['required', 'string', 'min:2', 'max:20', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/'],
            'Email' => 'required|email|max:50|unique:usuarios,Email',
            'Nacionalidad' => 'required|string|min:2|max:30',
            'CedulaIdentidad' => 'required|string|min:6|max:20|unique:usuarios,CedulaIdentidad',
            'FechaNacimiento' => 'required|date|before:today',
            'Peso' => 'required|numeric|min:25|max:150',
            'Altura' => 'required|numeric|min:1.00|max:2.20',
            'Nivel' => 'required|in:Junior,Senior',
            'password' => 'required|string|min:6|confirmed'
        ], [
            'Email.unique' => 'Este email ya está registrado.',
            'CedulaIdentidad.unique' => 'Este documento ya está registrado.',
            'Peso.min' => 'Peso mínimo: 25 kg.',
            'Altura.min' => 'Altura mínima: 1.00 m.',
            'password.confirmed' => 'Las contraseñas no coinciden.'
        ]);
        
        // Calcular edad para validación de negocio
        $fechaNacimiento = \Carbon\Carbon::parse($request->FechaNacimiento);
        $edad = $fechaNacimiento->age;
        
        if ($edad < 10 || $edad > 40) {
            return back()->withErrors(['FechaNacimiento' => 'Edad permitida para gimnastas: 10-40 años. Tu edad: ' . $edad . ' años.'])->withInput();
        }

        // Supongamos que en tu tabla 'roles', el ID_Rol para 'Gimnasta' es 4 (verifica este ID en tu phpMyAdmin)
        $idRolGimnasta = 4;

        // Crear el usuario directamente en la tabla 'usuarios' para acceso inmediato
        $usuario = Usuario::create([
            'NombreUsuario' => strstr($request->Email, '@', true),
            'Contrasena' => Hash::make($request->password),
            'ID_Rol' => $idRolGimnasta,
            'Email' => $request->Email,
            'CedulaIdentidad' => $request->CedulaIdentidad,
            'Nombre' => $request->Nombre,
            'Apellido' => $request->Apellido,
            'FechaNacimiento' => $request->FechaNacimiento,
            'Nacionalidad' => $request->Nacionalidad,
            'Peso' => $request->Peso,
            'Altura' => $request->Altura,
            'Nivel' => $request->Nivel,
            'activo' => true,
        ]);

        // Incrementar contador de rate limiting
        cache()->put($key, (cache()->get($key, 0) + 1), 3600);

        return redirect()->route('login')->with('success', '¡Registro exitoso! Ya puedes ingresar al sistema utilizando tus credenciales.');
    }

    public function logout()
    {
        \App\Services\AuditService::logout();
        session()->flush();
        return redirect()->route('login');
    }
}