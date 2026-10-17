<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EvaluacionesJuez;
use App\Models\DescuentosJuez;
use App\Models\DeduccionEjecucion;
use App\Models\Competencia;
use App\Models\Aparato;
use App\Models\Usuario;
use App\Models\Video;
use App\Models\PoseResultado;
use Carbon\Carbon;

class JuezController extends Controller
{
    public function dashboard()
    {
        $juezId = session('usuario_id');

        $evaluaciones = EvaluacionesJuez::with([
            'gimnasta', 'competencia', 'aparato', 'descuentos'
        ])
        ->where('ID_Usuario', $juezId)
        ->orderByRaw("FIELD(estado, 'pendiente', 'en_revision', 'confirmado')")
        ->orderBy('fecha_evaluacion', 'desc')
        ->get();

        $stats = [
            'pendientes' => $evaluaciones->where('estado', 'pendiente')->count(),
            'en_revision' => $evaluaciones->where('estado', 'en_revision')->count(),
            'confirmados' => $evaluaciones->where('estado', 'confirmado')->count(),
            'total' => $evaluaciones->count(),
        ];

        return view('juez.dashboard', compact('evaluaciones', 'stats'));
    }

    public function evaluar($id)
    {
        $evaluacion = EvaluacionesJuez::with([
            'gimnasta', 'competencia', 'aparato', 'descuentos'
        ])->findOrFail($id);

        if ($evaluacion->ID_Usuario !== session('usuario_id')) {
            abort(403, 'No tienes permiso para evaluar esta rutina.');
        }

        $deducciones = DeduccionEjecucion::where('ID_Aparato', $evaluacion->ID_Aparato)
            ->orWhereNull('ID_Aparato')
            ->orderBy('Tipo')
            ->get();

        $videoPath = $evaluacion->video_ruta;

        return view('juez.evaluar', compact('evaluacion', 'deducciones', 'videoPath'));
    }

    public function storeScore(Request $request, $id)
    {
        $evaluacion = EvaluacionesJuez::findOrFail($id);

        if ($evaluacion->ID_Usuario !== session('usuario_id')) {
            return response()->json(['success' => false, 'error' => 'Sin permiso'], 403);
        }

        $request->validate([
            'nota_dificultad' => 'required|numeric|min:0|max:10',
            'nota_ejecucion' => 'required|numeric|min:0|max:10',
            'observaciones' => 'nullable|string|max:1000',
        ]);

        $notaDificultad = (float) $request->nota_dificultad;
        $notaEjecucion = (float) $request->nota_ejecucion;
        $totalDescuentos = (float) ($request->total_descuentos ?? 0);
        $notaFinal = max(0, $notaDificultad + $notaEjecucion - $totalDescuentos);

        $evaluacion->update([
            'nota_dificultad' => $notaDificultad,
            'nota_ejecucion' => $notaEjecucion,
            'nota_final' => $notaFinal,
            'observaciones' => $request->observaciones,
            'estado' => $request->estado ?? 'en_revision',
            'fecha_evaluacion' => Carbon::now(),
        ]);

        return response()->json([
            'success' => true,
            'nota_final' => $notaFinal,
            'message' => 'Evaluación guardada correctamente.'
        ]);
    }

    public function storeDeduction(Request $request, $id)
    {
        $evaluacion = EvaluacionesJuez::findOrFail($id);

        if ($evaluacion->ID_Usuario !== session('usuario_id')) {
            return response()->json(['success' => false, 'error' => 'Sin permiso'], 403);
        }

        $request->validate([
            'tipo_error' => 'required|string|max:50',
            'descripcion_error' => 'required|string|max:500',
            'descuento_final' => 'required|numeric|min:0|max:10',
            'nombre_elemento' => 'nullable|string|max:100',
            'angulo_detectado' => 'nullable|numeric',
            'justificacion' => 'nullable|string|max:500',
            'angulos_json' => 'nullable|array',
            'angulos_json.*' => 'nullable|numeric|min:0|max:360',
            'ID_Aparato' => 'nullable|integer|exists:aparatos,ID_Aparato',
            'descuento_ia_propuesto' => 'nullable|numeric|min:0|max:10',
            'confianza_ia' => 'nullable|numeric|min:0|max:100',
        ]);

        $angulos = $request->input('angulos_json');
        $anguloPrincipal = $request->input('angulo_detectado');

        if (!empty($angulos) && is_array($angulos)) {
            $figRefs = [
                'codo_izq' => 180, 'codo_der' => 180,
                'rodilla_izq' => 180, 'rodilla_der' => 180,
                'cadera_izq' => 180, 'cadera_der' => 180,
                'hombro_izq' => 180, 'hombro_der' => 180,
                'tobillo_izq' => 90, 'tobillo_der' => 90,
                'tronco_izq' => 180, 'tronco_der' => 180,
            ];
            $peor = 0;
            $peorRef = 180;
            foreach ($figRefs as $clave => $ref) {
                if (isset($angulos[$clave]) && is_numeric($angulos[$clave])) {
                    $dev = abs((float) $angulos[$clave] - $ref);
                    if ($dev > $peor) {
                        $peor = $dev;
                        $peorRef = $ref;
                    }
                }
            }
            if ($anguloPrincipal === null && $peor > 0) {
                $anguloPrincipal = round($peorRef - ($peor / 2), 2);
            }
        }

        $descuentoIa = $request->input('descuento_ia_propuesto');
        $fueCorregido = $descuentoIa !== null
            && abs((float) $descuentoIa - (float) $request->input('descuento_final')) > 0.001;

        $deduccion = DescuentosJuez::create([
            'ID_EvaluacionJuez' => $id,
            'ID_Aparato' => $request->input('ID_Aparato') ?? $evaluacion->ID_Aparato,
            'nombre_elemento' => $request->nombre_elemento,
            'tipo_error' => $request->tipo_error,
            'descripcion_error' => $request->descripcion_error,
            'descuento_ia_propuesto' => $descuentoIa,
            'confianza_ia' => $request->input('confianza_ia'),
            'descuento_final' => $request->descuento_final,
            'angulo_detectado' => $anguloPrincipal,
            'angulos_json' => !empty($angulos) && is_array($angulos) ? $angulos : null,
            'justificacion' => $request->justificacion,
            'fue_corregido' => $fueCorregido,
        ]);

        return response()->json([
            'success' => true,
            'deduccion' => $deduccion,
            'fue_corregido' => $fueCorregido,
            'message' => $fueCorregido
                ? 'Deducción registrada. Tu corrección ha entrenado al Juez IA.'
                : 'Deducción registrada.',
        ]);
    }

    public function deleteDeduction($evalId, $dedId)
    {
        $ded = DescuentosJuez::where('ID_EvaluacionJuez', $evalId)->findOrFail($dedId);
        $evaluacion = EvaluacionesJuez::findOrFail($evalId);

        if ($evaluacion->ID_Usuario !== session('usuario_id')) {
            return response()->json(['success' => false, 'error' => 'Sin permiso'], 403);
        }

        $ded->delete();

        return response()->json(['success' => true, 'message' => 'Deducción eliminada.']);
    }

    public function getLandmarks($id)
    {
        $evaluacion = EvaluacionesJuez::findOrFail($id);
        $videos = $evaluacion->videos;

        $resultados = PoseResultado::whereIn('ID_Video', $videos->pluck('ID_Video'))
            ->orderBy('FrameNum')
            ->get();

        return response()->json([
            'success' => true,
            'resultados' => $resultados,
        ]);
    }

    public function storeEvaluation(Request $request)
    {
        $request->validate([
            'ID_Gimnasta' => 'required|integer|exists:usuarios,ID_Usuario',
            'ID_Competencia' => 'required|integer|exists:competencias,ID_Competencia',
            'ID_Aparato' => 'required|integer|exists:aparatos,ID_Aparato',
        ]);

        $evaluacion = EvaluacionesJuez::create([
            'ID_Gimnasta' => $request->ID_Gimnasta,
            'ID_Competencia' => $request->ID_Competencia,
            'ID_Aparato' => $request->ID_Aparato,
            'ID_Usuario' => session('usuario_id'),
            'estado' => 'pendiente',
            'fecha_evaluacion' => Carbon::now(),
        ]);

        // Handle video upload
        if ($request->hasFile('video')) {
            $file = $request->file('video');
            $fileName = 'eval_' . $evaluacion->ID_EvaluacionJuez . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('juez/videos', $fileName, 'public_uploads');

            $evaluacion->update(['video_ruta' => 'uploads/juez/videos/' . $fileName]);
        }

        return response()->json([
            'success' => true,
            'evaluacion_id' => $evaluacion->ID_EvaluacionJuez,
            'redirect' => route('juez.evaluar', $evaluacion->ID_EvaluacionJuez),
        ]);
    }

    public function createEvaluation()
    {
        $gimnastas = Usuario::whereHas('rol', fn($q) => $q->where('NombreRol', 'Gimnasta'))
            ->where('activo', true)
            ->orderBy('Nombre')
            ->get();

        $competencias = Competencia::where('activo', true)
            ->orderBy('Fecha', 'desc')
            ->get();

        $aparatos = Aparato::where('activo', true)
            ->orderBy('Nombre')
            ->get();

        return view('juez.crear', compact('gimnastas', 'competencias', 'aparatos'));
    }

    public function getPendingGymnasts()
    {
        $juezId = session('usuario_id');
        $competenciaId = request('competencia_id');

        $query = EvaluacionesJuez::where('ID_Usuario', $juezId)
            ->where('estado', 'pendiente');

        if ($competenciaId) {
            $query->where('ID_Competencia', $competenciaId);
        }

        $gimnastas = $query->with(['gimnasta', 'competencia', 'aparato'])
            ->get()
            ->groupBy(fn($e) => $e->competencia->NombreCompetencia ?? 'Sin competencia');

        return response()->json($gimnastas);
    }
}
