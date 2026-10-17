<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario;

class PerfilController extends Controller
{
    public function show()
    {
        $usuario = Usuario::with('rol')->find(session('usuario_id'));
        
        return view('perfil.admin', compact('usuario'));
    }

    public function update(Request $request)
    {
        $usuario = Usuario::find(session('usuario_id'));
        
        $request->validate([
            'Email' => 'required|email|max:50|unique:usuarios,Email,' . $usuario->ID_Usuario . ',ID_Usuario',
            'CedulaIdentidad' => 'required|string|min:6|max:20|unique:usuarios,CedulaIdentidad,' . $usuario->ID_Usuario . ',ID_Usuario',
            'Nacionalidad' => 'required|string|min:2|max:30'
        ]);

        $usuario->NombreUsuario = $request->Email;
        $usuario->Email = $request->Email;
        $usuario->CedulaIdentidad = $request->CedulaIdentidad;
        $usuario->Nacionalidad = $request->Nacionalidad;
        $usuario->Contacto = $request->Contacto;
        $usuario->Contrasena = bcrypt($request->CedulaIdentidad);
        
        $usuario->save();

        session(['usuario_nombre' => $usuario->Email]);
        
        return redirect()->route('perfil.show')->with('success', 'Perfil actualizado correctamente');
    }
}