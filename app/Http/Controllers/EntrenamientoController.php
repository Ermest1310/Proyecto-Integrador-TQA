<?php

namespace App\Http\Controllers;

use App\Models\Entrenamiento;
use App\Models\Usuario;
use App\Models\Aparato;
use Illuminate\Http\Request;

class EntrenamientoController extends Controller
{
    public function index(Request $request)
    {
        $query = Entrenamiento::with(['usuario', 'aparato']);
        
        if ($request->filled('gimnasta')) {
            $query->where('ID_Gimnasta', $request->gimnasta);
        }
        
        if ($request->filled('aparato')) {
            $query->where('ID_Aparato', $request->aparato);
        }
        
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->whereHas('usuario', function($q) use ($buscar) {
                $q->where('Nombre', 'like', "%{$buscar}%")
                  ->orWhere('Apellido', 'like', "%{$buscar}%");
            });
        }
        
        $entrenamientos = $query->orderBy('ID_Entrenamiento')->get();
        
        // Si no hay entrenamientos, crear algunos de prueba
        if ($entrenamientos->count() == 0) {
            $gimnastas = Usuario::whereHas('rol', function($q) {
                $q->where('NombreRol', 'Gimnasta');
            })->where('activo', true)->get();
            
            $aparatos = Aparato::where('activo', true)->get();
            
            if ($gimnastas->count() > 0 && $aparatos->count() > 0) {
                // Crear 5 entrenamientos de prueba
                for ($i = 0; $i < 5; $i++) {
                    Entrenamiento::create([
                        'ID_Gimnasta' => $gimnastas->random()->ID_Usuario,
                        'ID_Aparato' => $aparatos->random()->ID_Aparato,
                        'Fecha' => now()->subDays(rand(1, 30))->format('Y-m-d'),
                        'Duracion' => rand(60, 180),
                        'Observaciones' => 'Entrenamiento de prueba '
                    ]);
                }
                
                // Recargar entrenamientos
                $entrenamientos = Entrenamiento::with(['usuario', 'aparato'])
                                             ->orderBy('ID_Entrenamiento')
                                             ->get();
            }
        }
        
        $gimnastas = Usuario::whereHas('rol', function($q) {
            $q->where('NombreRol', 'Gimnasta');
        })->where('activo', true)->get();
        $aparatos = Aparato::where('activo', true)->get();
        
        return view('entrenamientos.index', compact('entrenamientos', 'gimnastas', 'aparatos'));
    }

    public function store(\App\Http\Requests\EntrenamientoRequest $request)
    {
        Entrenamiento::create($request->validated());
        return response()->json(['success' => true, 'message' => 'Entrenamiento creado correctamente']);
    }

    public function edit($id)
    {
        $entrenamiento = Entrenamiento::with(['usuario', 'aparato'])->find($id);
        
        if (!$entrenamiento) {
            return response()->json(['error' => 'Entrenamiento no encontrado'], 404);
        }
        
        return response()->json($entrenamiento);
    }

    public function update(\App\Http\Requests\EntrenamientoRequest $request, $id)
    {
        $entrenamiento = Entrenamiento::findOrFail($id);
        $entrenamiento->update($request->validated());
        return response()->json(['success' => true, 'message' => 'Entrenamiento actualizado correctamente']);
    }

    public function destroy($id)
    {
        $entrenamiento = Entrenamiento::find($id);
        
        if (!$entrenamiento) {
            return response()->json(['error' => 'Entrenamiento no encontrado'], 404);
        }
        
        $entrenamiento->update(['activo' => false]);
        return response()->json(['success' => true, 'message' => 'Entrenamiento desactivado correctamente']);
    }

    public function reactivar($id)
    {
        $entrenamiento = Entrenamiento::find($id);
        
        if (!$entrenamiento) {
            return response()->json(['error' => 'Entrenamiento no encontrado'], 404);
        }
        
        $entrenamiento->update(['activo' => true]);
        return response()->json(['success' => true, 'message' => 'Entrenamiento reactivado correctamente']);
    }

    public function getRelatedData()
    {
        $gimnastas = Usuario::whereHas('rol', function($q) {
            $q->where('NombreRol', 'Gimnasta');
        })->where('activo', true)->get();
        $aparatos = Aparato::where('activo', true)->get();
        
        return response()->json([
            'gimnastas' => $gimnastas,
            'aparatos' => $aparatos
        ]);
    }
}