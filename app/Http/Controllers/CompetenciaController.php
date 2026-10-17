<?php

namespace App\Http\Controllers;

use App\Models\Competencia;
use Illuminate\Http\Request;

class CompetenciaController extends Controller
{
    public function index(Request $request)
    {
        $query = Competencia::query();
        
        if ($request->filled('estado')) {
            $query->where('activo', $request->estado == 'activo');
        }
        
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function($q) use ($buscar) {
                $q->where('NombreCompetencia', 'like', "%{$buscar}%")
                  ->orWhere('Ubicacion', 'like', "%{$buscar}%");
            });
        }
        
        $competencias = $query->orderBy('ID_Competencia')->get();
        return view('competencias.index', compact('competencias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'NombreCompetencia' => 'required|string|max:100',
            'Fecha' => 'required|date',
            'Ubicacion' => 'required|string|max:150',
            'Descripcion' => 'nullable|string'
        ]);

        Competencia::create($request->all());
        return response()->json(['success' => true, 'message' => 'Competencia creada correctamente']);
    }

    public function edit($id)
    {
        $competencia = Competencia::find($id);
        
        if (!$competencia) {
            return response()->json(['error' => 'Competencia no encontrada'], 404);
        }
        
        return response()->json($competencia);
    }

    public function update(Request $request, $id)
    {
        $competencia = Competencia::find($id);
        
        if (!$competencia) {
            return response()->json(['error' => 'Competencia no encontrada'], 404);
        }
        
        $request->validate([
            'NombreCompetencia' => 'required|string|max:100',
            'Fecha' => 'required|date',
            'Ubicacion' => 'required|string|max:150',
            'Descripcion' => 'nullable|string'
        ]);

        $competencia->update($request->all());
        return response()->json(['success' => true, 'message' => 'Competencia actualizada correctamente']);
    }

    public function destroy($id)
    {
        $competencia = Competencia::find($id);
        
        if (!$competencia) {
            return response()->json(['error' => 'Competencia no encontrada'], 404);
        }
        
        $competencia->update(['activo' => false]);
        return response()->json(['success' => true, 'message' => 'Competencia desactivada correctamente']);
    }

    public function reactivar($id)
    {
        $competencia = Competencia::find($id);
        
        if (!$competencia) {
            return response()->json(['error' => 'Competencia no encontrada'], 404);
        }
        
        $competencia->update(['activo' => true]);
        return response()->json(['success' => true, 'message' => 'Competencia reactivada correctamente']);
    }
}
