<?php

namespace App\Http\Controllers;

use App\Models\Lesion;
use App\Models\Usuario;
use Illuminate\Http\Request;

class LesionController extends Controller
{
    public function index(Request $request)
    {
        // Debug: verificar si hay gimnastas
        $gimnastas = Usuario::whereHas('rol', function($q) {
            $q->where('NombreRol', 'Gimnasta');
        })->where('activo', true)->get();
        
        $query = Lesion::with('usuario');
        
        if ($request->filled('gimnasta')) {
            $query->where('ID_Gimnasta', $request->gimnasta);
        }
        
        if ($request->filled('estado')) {
            if ($request->estado == 'activo') {
                $query->where('activo', true);
            } else {
                $query->where('activo', false);
            }
        }
        
        if ($request->filled('tipo')) {
            $query->where('Tipo', 'like', "%{$request->tipo}%");
        }
        
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function($q) use ($buscar) {
                $q->where('Tipo', 'like', "%{$buscar}%")
                  ->orWhere('ParteCuerpo', 'like', "%{$buscar}%")
                  ->orWhereHas('usuario', function($subq) use ($buscar) {
                      $subq->where('Nombre', 'like', "%{$buscar}%")
                           ->orWhere('Apellido', 'like', "%{$buscar}%");
                  });
            });
        }
        
        $lesiones = $query->get();
        
        return view('lesiones.index', compact('lesiones', 'gimnastas'));
    }

    public function create()
    {
        return view('lesiones.create');
    }

    public function store(\App\Http\Requests\LesionRequest $request)
    {
        Lesion::create($request->validated());
        return response()->json(['success' => true, 'message' => 'Lesión creada correctamente']);
    }

    public function show($id)
    {
        $lesion = Lesion::with(['usuario', 'medico'])->findOrFail($id);
        return view('lesiones.show', compact('lesion'));
    }

    public function edit($id)
    {
        $lesion = Lesion::with('usuario')->find($id);
        
        if (!$lesion) {
            return response()->json(['error' => 'Lesión no encontrada'], 404);
        }
        
        return response()->json($lesion);
    }

    public function update(\App\Http\Requests\LesionRequest $request, $id)
    {
        $lesion = Lesion::findOrFail($id);
        $lesion->update($request->validated());
        return response()->json(['success' => true, 'message' => 'Lesión actualizada correctamente']);
    }

    public function destroy($id)
    {
        $lesion = Lesion::find($id);
        
        if (!$lesion) {
            return response()->json(['error' => 'Lesión no encontrada'], 404);
        }
        
        $lesion->update(['activo' => false]);
        return response()->json(['success' => true, 'message' => 'Lesión desactivada correctamente']);
    }

    public function reactivar($id)
    {
        $lesion = Lesion::find($id);
        
        if (!$lesion) {
            return response()->json(['error' => 'Lesión no encontrada'], 404);
        }
        
        $lesion->update(['activo' => true]);
        return response()->json(['success' => true, 'message' => 'Lesión reactivada correctamente']);
    }

    public function getGimnastas()
    {
        $gimnastas = Usuario::whereHas('rol', function($q) {
            $q->where('NombreRol', 'Gimnasta');
        })->where('activo', true)->get();
        
        $medicos = Usuario::whereHas('rol', function($q) {
            $q->whereIn('NombreRol', ['Médico', 'Fisioterapeuta']);
        })->where('activo', true)->get();
        
        return response()->json([
            'gimnastas' => $gimnastas,
            'medicos' => $medicos,
            'tipos_lesion' => \App\Models\Lesion::getTiposLesion(),
            'gravedades' => \App\Models\Lesion::getGravedades(),
            'estados' => \App\Models\Lesion::getEstados()
        ]);
    }
}