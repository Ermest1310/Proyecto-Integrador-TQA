<?php

namespace App\Http\Controllers;

use App\Models\Aparato;
use App\Models\Gimnasta;
use App\Models\Rutina;
use App\Services\RutinaAStarService;
use Illuminate\Http\Request;

class RutinaController extends Controller
{
    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(Rutina $rutina)
    {
        //
    }

    public function edit(Rutina $rutina)
    {
        //
    }

    public function update(Request $request, Rutina $rutina)
    {
        //
    }

    public function destroy(Rutina $rutina)
    {
        //
    }

    public function planificarOptima(Request $request)
    {
        $request->validate([
            'gimnasta_id' => 'required|integer|exists:gimnastas,ID_Gimnasta',
            'aparato_id' => 'required|integer|exists:aparatos,ID_Aparato',
            'max_elementos' => 'nullable|integer|min:4|max:12',
        ]);

        $gimnasta = Gimnasta::find($request->input('gimnasta_id'));
        if (!$gimnasta || !$gimnasta->activo) {
            return response()->json([
                'success' => false,
                'mensaje' => 'Gimnasta no encontrado o inactivo',
            ], 404);
        }

        $aparato = Aparato::find($request->input('aparato_id'));
        if (!$aparato || !$aparato->activo) {
            return response()->json([
                'success' => false,
                'mensaje' => 'Aparato no encontrado o inactivo',
            ], 404);
        }

        $maxElementos = $request->input('max_elementos', 10);

        try {
            $servicio = new RutinaAStarService($maxElementos);
            $resultado = $servicio->planificar(
                $request->input('gimnasta_id'),
                $request->input('aparato_id')
            );

            return response()->json($resultado);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'mensaje' => $e->getMessage(),
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'mensaje' => 'Error durante la planificación: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function mostrarVista(Request $request)
    {
        $gimnastas = Gimnasta::where('activo', true)
            ->orderBy('Nombre')
            ->get();
        $aparatos = Aparato::where('activo', true)
            ->orderBy('Nombre')
            ->get();

        return view('rutinas.planificar', compact('gimnastas', 'aparatos'));
    }
}
