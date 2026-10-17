<?php

namespace App\Http\Controllers;

use App\Models\Aparato;
use Illuminate\Http\Request;

class AparatoController extends Controller
{
    public function index()
    {
        // Actualizar automáticamente aparatos que necesitan mantenimiento
        Aparato::where('activo', true)
            ->where('EstadoMantenimiento', 'Operativo')
            ->whereNotNull('ProximoMantenimiento')
            ->where('ProximoMantenimiento', '<=', now()->addDays(7))
            ->update(['EstadoMantenimiento' => 'Mantenimiento']);
        
        $aparatos = Aparato::orderBy('ID_Aparato')->get();
        return view('aparatos.index', compact('aparatos'));
    }

    public function store(\App\Http\Requests\AparatoRequest $request)
    {
        Aparato::create($request->validated());
        return response()->json(['success' => true, 'message' => 'Aparato creado correctamente']);
    }

    public function edit($id)
    {
        $aparato = Aparato::find($id);
        
        if (!$aparato) {
            return response()->json(['error' => 'Aparato no encontrado'], 404);
        }
        
        return response()->json($aparato);
    }

    public function update(\App\Http\Requests\AparatoRequest $request, $id)
    {
        $aparato = Aparato::findOrFail($id);
        $aparato->update($request->validated());
        return response()->json(['success' => true, 'message' => 'Aparato actualizado correctamente']);
    }

    public function destroy($id)
    {
        $aparato = Aparato::find($id);
        
        if (!$aparato) {
            return response()->json(['error' => 'Aparato no encontrado'], 404);
        }
        
        $aparato->update(['activo' => false]);
        return response()->json(['success' => true, 'message' => 'Aparato desactivado correctamente']);
    }

    public function reactivar($id)
    {
        $aparato = Aparato::find($id);
        
        if (!$aparato) {
            return response()->json(['error' => 'Aparato no encontrado'], 404);
        }
        
        $aparato->update([
            'activo' => true,
            'EstadoMantenimiento' => 'Operativo'
        ]);
        return response()->json(['success' => true, 'message' => 'Aparato reactivado correctamente']);
    }

    public function desactivar($id)
    {
        $aparato = Aparato::find($id);
        
        if (!$aparato) {
            return response()->json(['error' => 'Aparato no encontrado'], 404);
        }
        
        $aparato->update([
            'activo' => false,
            'EstadoMantenimiento' => 'Mantenimiento'
        ]);
        return response()->json(['success' => true, 'message' => 'Aparato desactivado correctamente']);
    }

    public function finalizarMantenimiento($id)
    {
        $aparato = Aparato::find($id);

        if (!$aparato) {
            return response()->json(['error' => 'Aparato no encontrado'], 404);
        }

        $aparato->update([
            'EstadoMantenimiento' => 'Operativo',
            'UltimoMantenimiento' => now()->toDateString(),
        ]);
        return response()->json(['success' => true, 'message' => 'Mantenimiento finalizado correctamente']);
    }
}
