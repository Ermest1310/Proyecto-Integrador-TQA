<?php

namespace App\Http\Controllers;

use App\Models\Lesion;
use App\Models\Usuario;
use Illuminate\Http\Request;
use App\Services\PermissionService;

class MedicoLesionController extends Controller
{
    public function index()
    {
        PermissionService::abort('lesiones.view');
        
        $lesiones = Lesion::with(['gimnasta', 'medico'])
            ->when(!PermissionService::isAdmin(), function($query) {
                // Médicos solo ven sus propias lesiones diagnosticadas
                $query->where('ID_Medico', session('usuario_id'));
            })
            ->orderBy('FechaDiagnostico', 'desc')
            ->get();
            
        return view('medico.lesiones.index', compact('lesiones'));
    }

    public function create()
    {
        PermissionService::abort('lesiones.create');
        
        $gimnastas = Usuario::whereHas('rol', function($q) {
            $q->where('NombreRol', 'Gimnasta');
        })->where('activo', true)->get();
        
        return view('medico.lesiones.create', compact('gimnastas'));
    }

    public function store(Request $request)
    {
        PermissionService::abort('lesiones.create');
        
        $request->validate([
            'ID_Gimnasta' => 'required|exists:usuarios,ID_Usuario',
            'Tipo' => 'required|in:' . implode(',', array_keys(Lesion::getTiposLesion())),
            'ParteCuerpo' => 'required|in:' . implode(',', array_keys(Lesion::getPartesCuerpo())),
            'Gravedad' => 'required|in:' . implode(',', array_keys(Lesion::getGravedades())),
            'FechaLesion' => 'required|date|before_or_equal:today',
            'FechaDiagnostico' => 'required|date|after_or_equal:FechaLesion|before_or_equal:today',
            'Tratamiento' => 'required|string|min:10|max:500',
            'TiempoRecuperacion' => 'required|integer|min:1|max:365'
        ]);

        $data = $request->all();
        $data['ID_Medico'] = session('usuario_id');
        $data['Estado'] = 'Diagnosticada';

        Lesion::create($data);
        
        return redirect()->route('medico.lesiones.index')
            ->with('success', 'Lesión diagnosticada correctamente');
    }

    public function show($id)
    {
        PermissionService::abort('lesiones.view');
        
        $lesion = Lesion::with(['gimnasta', 'medico'])->findOrFail($id);
        
        // Verificar que el médico puede ver esta lesión
        if (!PermissionService::isAdmin() && $lesion->ID_Medico !== session('usuario_id')) {
            abort(403, 'No tienes permisos para ver esta lesión');
        }
        
        return view('medico.lesiones.show', compact('lesion'));
    }

    public function edit($id)
    {
        PermissionService::abort('lesiones.edit');
        
        $lesion = Lesion::findOrFail($id);
        
        // Solo el médico que diagnosticó puede editar
        if ($lesion->ID_Medico !== session('usuario_id') && !PermissionService::isAdmin()) {
            abort(403, 'Solo puedes editar lesiones que hayas diagnosticado');
        }
        
        return view('medico.lesiones.edit', compact('lesion'));
    }

    public function update(Request $request, $id)
    {
        PermissionService::abort('lesiones.edit');
        
        $lesion = Lesion::findOrFail($id);
        
        if ($lesion->ID_Medico !== session('usuario_id') && !PermissionService::isAdmin()) {
            abort(403, 'Solo puedes editar lesiones que hayas diagnosticado');
        }

        $request->validate([
            'Estado' => 'required|in:' . implode(',', array_keys(Lesion::getEstados())),
            'Tratamiento' => 'required|string|min:10|max:500',
            'TiempoRecuperacion' => 'required|integer|min:1|max:365',
            'Observaciones' => 'nullable|string|max:1000'
        ]);

        $lesion->update($request->only([
            'Estado', 'Tratamiento', 'TiempoRecuperacion', 'Observaciones'
        ]));
        
        return redirect()->route('medico.lesiones.index')
            ->with('success', 'Lesión actualizada correctamente');
    }
}