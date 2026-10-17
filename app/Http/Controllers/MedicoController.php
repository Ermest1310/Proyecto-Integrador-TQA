<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\SesionEntrenamiento;
use App\Models\Gimnasta;

class MedicoController extends Controller
{
    public function dashboard()
    {
        $gimnastas = Usuario::with('rol')
            ->whereHas('rol', function($q) {
                $q->where('NombreRol', 'Gimnasta');
            })
            ->where('activo', true)
            ->get();
        
        return view('medico.dashboard', compact('gimnastas'));
    }

    public function verGimnasta($id)
    {
        $gimnasta = Usuario::with(['rol', 'lesiones' => function($q) {
            $q->where('activo', true);
        }])->findOrFail($id);

        $estadisticas = [
            'total_sesiones' => SesionEntrenamiento::where('ID_Gimnasta', $id)
                ->where('Estado', '!=', 'Cancelada')->count(),
            'sesiones_completadas' => SesionEntrenamiento::where('ID_Gimnasta', $id)
                ->where('Estado', 'Completada')->count(),
            'sesiones_programadas' => SesionEntrenamiento::where('ID_Gimnasta', $id)
                ->where('Estado', 'Programada')->count(),
            'sesiones_este_mes' => SesionEntrenamiento::where('ID_Gimnasta', $id)
                ->where('Estado', '!=', 'Cancelada')
                ->whereMonth('Fecha', now()->month)
                ->whereYear('Fecha', now()->year)->count(),
            'lesiones_activas' => $gimnasta->lesiones->where('Estado', 'Lesionado')->count(),
            'promedio_duracion' => SesionEntrenamiento::where('ID_Gimnasta', $id)
                ->where('Estado', 'Completada')
                ->whereNotNull('Duracion_Minutos')
                ->avg('Duracion_Minutos'),
        ];

        $aparatosSesiones = SesionEntrenamiento::selectRaw('ID_Aparato, COUNT(*) as total')
            ->where('ID_Gimnasta', $id)
            ->where('Estado', '!=', 'Cancelada')
            ->whereNotNull('ID_Aparato')
            ->groupBy('ID_Aparato')
            ->with('aparato')
            ->get();

        $sesionesMensuales = SesionEntrenamiento::selectRaw("DATE_FORMAT(Fecha, '%Y-%m') as mes, COUNT(*) as total")
            ->where('ID_Gimnasta', $id)
            ->where('Estado', '!=', 'Cancelada')
            ->where('Fecha', '>=', now()->subMonths(6)->startOfMonth())
            ->groupBy('mes')
            ->orderBy('mes')
            ->pluck('total', 'mes');

        $gimnastaRecord = Gimnasta::where('ID_Usuario', $id)->first();
        $evaluacionNutricional = null;
        $evaluacionPsicologica = null;
        $evaluacionFisiologica = null;
        if ($gimnastaRecord) {
            $evaluacionNutricional = $gimnastaRecord->evaluacionesNutricionales()->latest('FechaEvaluacion')->first();
            $evaluacionPsicologica = $gimnastaRecord->evaluacionesPsicologicas()->latest('FechaEvaluacion')->first();
            $evaluacionFisiologica = $gimnastaRecord->evaluacionesFisiologicas()->latest('FechaEvaluacion')->first();
        }

        return view('medico.ver-gimnasta', compact(
            'gimnasta', 'estadisticas', 'aparatosSesiones', 'sesionesMensuales',
            'evaluacionNutricional', 'evaluacionPsicologica', 'evaluacionFisiologica'
        ));
    }

    public function historialMedico($id)
    {
        $gimnasta = Usuario::with('rol')->findOrFail($id);
        return view('medico.historial', compact('gimnasta'));
    }
}