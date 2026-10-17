<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Postulacion;
use App\Models\Usuario;
use App\Models\Rol;

class PostulacionController extends Controller
{
    public function index(Request $request)
    {
        $query = Postulacion::with('revisor');
        
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function($q) use ($buscar) {
                $q->where('Nombre', 'like', "%{$buscar}%")
                  ->orWhere('Apellido', 'like', "%{$buscar}%")
                  ->orWhere('Email', 'like', "%{$buscar}%")
                  ->orWhere('CedulaIdentidad', 'like', "%{$buscar}%");
            });
        }
        
        $postulaciones = $query->orderBy('fecha_postulacion', 'desc')->paginate(15);
        return view('postulaciones.index', compact('postulaciones'));
    }

    // Las postulaciones se crean desde AuthController::register

    public function aprobar($id)
    {
        $postulacion = Postulacion::findOrFail($id);
        
        if ($postulacion->estado !== 'pendiente') {
            return back()->with('error', 'Esta postulación ya fue procesada.');
        }

        $rolGimnasta = Rol::where('NombreRol', 'Gimnasta')->first();

        Usuario::create([
            'NombreUsuario' => $postulacion->Email,
            'Email' => $postulacion->Email,
            'CedulaIdentidad' => $postulacion->CedulaIdentidad,
            'Contraseña' => bcrypt($postulacion->CedulaIdentidad),
            'Nombre' => $postulacion->Nombre,
            'Apellido' => $postulacion->Apellido,
            'FechaNacimiento' => $postulacion->FechaNacimiento,
            'Nacionalidad' => $postulacion->Nacionalidad,
            'Peso' => $postulacion->Peso,
            'Altura' => $postulacion->Altura,
            'Nivel' => $postulacion->Nivel,
            'ID_Rol' => $rolGimnasta->ID_Rol,
            'activo' => true
        ]);

        $postulacion->update([
            'estado' => 'aprobada',
            'fecha_revision' => now(),
            'revisado_por' => session('usuario_id')
        ]);

        \App\Services\AuditService::critical('POSTULACION_APPROVED', [
            'postulacion_id' => $postulacion->ID_Postulacion,
            'applicant_email' => $postulacion->Email,
            'new_user_created' => true
        ]);

        return back()->with('success', 'Postulación aprobada y usuario creado exitosamente.');
    }

    public function rechazar(Request $request, $id)
    {
        $postulacion = Postulacion::findOrFail($id);
        
        if ($postulacion->estado !== 'pendiente') {
            return back()->with('error', 'Esta postulación ya fue procesada.');
        }

        $postulacion->update([
            'estado' => 'rechazada',
            'comentarios' => $request->comentarios,
            'fecha_revision' => now(),
            'revisado_por' => session('usuario_id')
        ]);

        return back()->with('success', 'Postulación rechazada.');
    }

    public function detalle($id)
    {
        \App\Services\PermissionService::abort('postulaciones.view');
        
        $postulacion = Postulacion::with('revisor')->findOrFail($id);
        return response()->json($postulacion);
    }
}