<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\Gimnasta;
use App\Models\Entrenamiento;
use App\Models\SesionEntrenamiento;
use App\Models\Lesion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MineriaDatosController extends Controller
{
    public function index()
    {
        $totalGimnastas = Usuario::whereHas('rol', fn($q) => $q->where('NombreRol', 'Gimnasta'))->count();
        $totalSesiones = SesionEntrenamiento::count();
        $totalLesiones = Lesion::where('activo', true)->count();
        $totalEntrenamientos = Entrenamiento::count();

        $clusters = $this->calcularClusters();
        $patronesLesiones = $this->analizarPatronesLesiones();
        $correlaciones = $this->calcularCorrelaciones();

        return view('mineria.index', compact(
            'totalGimnastas', 'totalSesiones', 'totalLesiones', 'totalEntrenamientos',
            'clusters', 'patronesLesiones', 'correlaciones'
        ));
    }

    public function clustering()
    {
        $clusters = $this->calcularClusters();
        $gimnastas = Usuario::with(['rol', 'lesiones', 'sesionesComoGimnasta'])
            ->leftJoin('gimnastas', 'usuarios.ID_Usuario', '=', 'gimnastas.ID_Usuario')
            ->whereHas('rol', fn($q) => $q->where('NombreRol', 'Gimnasta'))
            ->where('usuarios.activo', true)
            ->select('usuarios.*', 'gimnastas.Peso as g_peso', 'gimnastas.Altura as g_altura', 'gimnastas.FechaNacimiento as g_fecha_nac', 'gimnastas.Nivel as g_nivel')
            ->get()
            ->map(fn($g) => [
                'id' => $g->ID_Usuario,
                'nombre' => $g->Nombre . ' ' . $g->Apellido,
                'nivel' => $g->g_nivel ?? $g->Nivel,
                'peso' => $g->g_peso ?? $g->Peso ?? 0,
                'altura' => $g->g_altura ?? $g->Altura ?? 0,
                'imc' => ($g->g_altura ?? $g->Altura) > 0
                    ? round(($g->g_peso ?? $g->Peso) / (($g->g_altura ?? $g->Altura) ** 2), 1)
                    : 0,
                'edad' => $g->g_fecha_nac
                    ? \Carbon\Carbon::parse($g->g_fecha_nac)->age
                    : ($g->FechaNacimiento ? $g->FechaNacimiento->age : 0),
                'rendimiento' => $g->sesionesComoGimnasta->count() > 0
                    ? round($g->sesionesComoGimnasta->where('Estado', 'Completada')->count() / $g->sesionesComoGimnasta->count() * 100, 1)
                    : 0,
                'lesiones' => $g->lesiones->count(),
            ]);

        return view('mineria.clustering', compact('clusters', 'gimnastas'));
    }

    public function patrones()
    {
        $patrones = $this->analizarPatronesLesiones();
        return view('mineria.patrones', compact('patrones'));
    }

    public function correlaciones()
    {
        $correlaciones = $this->calcularCorrelaciones();
        return view('mineria.correlaciones', compact('correlaciones'));
    }

    public function prediccionIA(Request $request)
    {
        $resultado = null;
        $gimnastas = Usuario::leftJoin('gimnastas', 'usuarios.ID_Usuario', '=', 'gimnastas.ID_Usuario')
            ->whereHas('rol', fn($q) => $q->where('NombreRol', 'Gimnasta'))
            ->where('usuarios.activo', true)
            ->select('usuarios.*', 'gimnastas.Peso as g_peso', 'gimnastas.Altura as g_altura', 'gimnastas.FechaNacimiento as g_fecha_nac', 'gimnastas.Nivel as g_nivel')
            ->get();

        if ($request->isMethod('post')) {
            $resultado = $this->analizarConIA($request->tipo_analisis ?? 'rendimiento');
        }

        return view('mineria.prediccion', compact('resultado', 'gimnastas'));
    }

    private function calcularClusters()
    {
        $gimnastas = Usuario::with(['lesiones', 'sesionesComoGimnasta'])
            ->leftJoin('gimnastas', 'usuarios.ID_Usuario', '=', 'gimnastas.ID_Usuario')
            ->whereHas('rol', fn($q) => $q->where('NombreRol', 'Gimnasta'))
            ->where('usuarios.activo', true)
            ->select('usuarios.*', 'gimnastas.Peso as g_peso', 'gimnastas.Altura as g_altura', 'gimnastas.FechaNacimiento as g_fecha_nac')
            ->get();

        if ($gimnastas->count() < 3) return [];

        $data = [];
        foreach ($gimnastas as $g) {
            $peso = $g->g_peso ?? $g->Peso ?? 0;
            $altura = $g->g_altura ?? $g->Altura ?? 0;
            $imc = $altura > 0 ? $peso / ($altura ** 2) : 0;
            $edad = $g->g_fecha_nac ? \Carbon\Carbon::parse($g->g_fecha_nac)->age : ($g->FechaNacimiento ? $g->FechaNacimiento->age : 0);
            $total = $g->sesionesComoGimnasta->count();
            $comp = $g->sesionesComoGimnasta->where('Estado', 'Completada')->count();
            $rend = $total > 0 ? $comp / $total : 0;
            $data[] = ['gimnasta' => $g, 'imc' => $imc, 'edad' => $edad, 'rendimiento' => $rend, 'lesiones' => $g->lesiones->count()];
        }

        $k = 3;
        if (count($data) < $k) return [];

        $centroids = [];
        $indices = array_rand($data, $k);
        foreach (is_array($indices) ? $indices : [$indices] as $i) {
            $centroids[] = ['rendimiento' => $data[$i]['rendimiento'], 'lesiones' => $data[$i]['lesiones'] / 10, 'imc_n' => $data[$i]['imc'] / 40, 'edad_n' => $data[$i]['edad'] / 40];
        }

        $assignments = [];
        for ($iter = 0; $iter < 10; $iter++) {
            foreach ($data as $idx => $p) {
                $min = INF;
                $best = 0;
                foreach ($centroids as $c => $ct) {
                    $d = sqrt(pow($p['rendimiento'] - $ct['rendimiento'], 2) * 2 + pow(($p['lesiones'] / 10) - $ct['lesiones'], 2) + pow(($p['imc'] / 40) - $ct['imc_n'], 2) + pow(($p['edad'] / 40) - $ct['edad_n'], 2));
                    if ($d < $min) { $min = $d; $best = $c; }
                }
                $assignments[$idx] = $best;
            }
            $newC = array_fill(0, $k, ['rendimiento' => 0, 'lesiones' => 0, 'imc_n' => 0, 'edad_n' => 0, 'cnt' => 0]);
            foreach ($assignments as $idx => $c) {
                $newC[$c]['rendimiento'] += $data[$idx]['rendimiento'];
                $newC[$c]['lesiones'] += $data[$idx]['lesiones'] / 10;
                $newC[$c]['imc_n'] += $data[$idx]['imc'] / 40;
                $newC[$c]['edad_n'] += $data[$idx]['edad'] / 40;
                $newC[$c]['cnt']++;
            }
            $changed = false;
            foreach ($newC as $c => $nc) {
                if ($nc['cnt'] > 0) {
                    $avg = ['rendimiento' => $nc['rendimiento'] / $nc['cnt'], 'lesiones' => $nc['lesiones'] / $nc['cnt'], 'imc_n' => $nc['imc_n'] / $nc['cnt'], 'edad_n' => $nc['edad_n'] / $nc['cnt']];
                    if ($avg !== $centroids[$c]) $changed = true;
                    $centroids[$c] = $avg;
                }
            }
            if (!$changed) break;
        }

        $labels = [
            0 => ['nombre' => 'Rendimiento Óptimo', 'icono' => 'fa-trophy', 'color' => '#10b981'],
            1 => ['nombre' => 'Rendimiento Moderado', 'icono' => 'fa-chart-line', 'color' => '#f59e0b'],
            2 => ['nombre' => 'Requiere Atención', 'icono' => 'fa-exclamation-triangle', 'color' => '#ef4444'],
        ];

        $result = [];
        foreach ($assignments as $idx => $c) {
            $g = $data[$idx]['gimnasta'];
            $result[$c][] = [
                'nombre' => $g->Nombre . ' ' . $g->Apellido,
                'nivel' => $g->g_nivel ?? $g->Nivel,
                'peso' => $g->g_peso ?? $g->Peso ?? 0,
                'altura' => $g->g_altura ?? $g->Altura ?? 0,
                'imc' => round($data[$idx]['imc'], 1),
                'edad' => $data[$idx]['edad'],
                'rendimiento' => round($data[$idx]['rendimiento'] * 100, 1),
                'lesiones' => $data[$idx]['lesiones'],
                'info' => $labels[$c] ?? ['nombre' => 'Cluster ' . $c, 'icono' => 'fa-circle', 'color' => '#6366f1'],
            ];
        }
        ksort($result);
        return $result;
    }

    private function analizarPatronesLesiones()
    {
        $porParteCuerpo = Lesion::selectRaw('ParteCuerpo, COUNT(*) as total')
            ->where('activo', true)->groupBy('ParteCuerpo')->orderByDesc('total')->get();
        $porGravedad = Lesion::selectRaw('Gravedad, COUNT(*) as total')
            ->where('activo', true)->groupBy('Gravedad')->orderByDesc('total')->get();
        $porEstado = Lesion::selectRaw('Estado, COUNT(*) as total')
            ->where('activo', true)->groupBy('Estado')->orderByDesc('total')->get();
        $porNivel = DB::table('lesiones')
            ->select('usuarios.Nivel', DB::raw('COUNT(*) as total'))
            ->join('usuarios', 'lesiones.ID_Gimnasta', '=', 'usuarios.ID_Usuario')
            ->where('lesiones.activo', true)
            ->groupBy('usuarios.Nivel')->get();
        $totalLesiones = Lesion::where('activo', true)->count();

        $gimnastas = Usuario::whereHas('rol', fn($q) => $q->where('NombreRol', 'Gimnasta'))
            ->where('activo', true)->get(['ID_Usuario', 'FechaNacimiento']);
        $porEdad = [];
        foreach ($gimnastas as $g) {
            if (!$g->FechaNacimiento) continue;
            $edad = $g->FechaNacimiento->age;
            $range = floor($edad / 5) * 5 . '-' . (floor($edad / 5) * 5 + 4);
            $lc = Lesion::where('ID_Gimnasta', $g->ID_Usuario)->where('activo', true)->count();
            if (!isset($porEdad[$range])) $porEdad[$range] = ['gimnastas' => 0, 'lesiones' => 0];
            $porEdad[$range]['gimnastas']++;
            $porEdad[$range]['lesiones'] += $lc;
        }
        ksort($porEdad);

        $porTipo = Lesion::selectRaw('Tipo, COUNT(*) as total')
            ->where('activo', true)->groupBy('Tipo')->orderByDesc('total')->get();
        $porParteEspecifica = Lesion::selectRaw('ParteEspecifica, COUNT(*) as total')
            ->where('activo', true)->whereNotNull('ParteEspecifica')->groupBy('ParteEspecifica')->orderByDesc('total')->limit(10)->get();

        return compact('porParteCuerpo', 'porGravedad', 'porEstado', 'porNivel', 'porEdad', 'totalLesiones', 'porTipo', 'porParteEspecifica');
    }

    private function calcularCorrelaciones()
    {
        $gimnastas = Usuario::with(['lesiones', 'sesionesComoGimnasta'])
            ->leftJoin('gimnastas', 'usuarios.ID_Usuario', '=', 'gimnastas.ID_Usuario')
            ->whereHas('rol', fn($q) => $q->where('NombreRol', 'Gimnasta'))
            ->where('usuarios.activo', true)
            ->select('usuarios.*', 'gimnastas.Peso as g_peso', 'gimnastas.Altura as g_altura', 'gimnastas.FechaNacimiento as g_fecha_nac', 'gimnastas.Nivel as g_nivel')
            ->get();

        if ($gimnastas->count() < 3) return [];

        $imcs = []; $rends = []; $edades = []; $lesiones = []; $sesiones = []; $niveles = [];
        foreach ($gimnastas as $g) {
            $peso = $g->g_peso ?? $g->Peso ?? 0;
            $altura = $g->g_altura ?? $g->Altura ?? 0;
            $imc = $altura > 0 ? $peso / ($altura ** 2) : 0;
            $total = $g->sesionesComoGimnasta->count();
            $comp = $g->sesionesComoGimnasta->where('Estado', 'Completada')->count();
            $imcs[] = $imc;
            $rends[] = $total > 0 ? $comp / $total : 0;
            $edades[] = $g->g_fecha_nac ? \Carbon\Carbon::parse($g->g_fecha_nac)->age : ($g->FechaNacimiento ? $g->FechaNacimiento->age : 0);
            $lesiones[] = $g->lesiones->where('activo', true)->count();
            $sesiones[] = $total;
            $niveles[] = $g->Nivel === 'Senior' ? 1 : 0;
        }

        $pairs = [
            'imc_vs_rendimiento' => ['x' => $imcs, 'y' => $rends, 'label_x' => 'IMC', 'label_y' => 'Rendimiento (%)'],
            'edad_vs_lesiones' => ['x' => $edades, 'y' => $lesiones, 'label_x' => 'Edad', 'label_y' => 'Lesiones'],
            'sesiones_vs_rendimiento' => ['x' => $sesiones, 'y' => $rends, 'label_x' => 'Sesiones Totales', 'label_y' => 'Rendimiento (%)'],
            'nivel_vs_lesiones' => ['x' => $niveles, 'y' => $lesiones, 'label_x' => 'Nivel (0=Junior, 1=Senior)', 'label_y' => 'Lesiones'],
            'imc_vs_lesiones' => ['x' => $imcs, 'y' => $lesiones, 'label_x' => 'IMC', 'label_y' => 'Lesiones'],
            'edad_vs_rendimiento' => ['x' => $edades, 'y' => $rends, 'label_x' => 'Edad', 'label_y' => 'Rendimiento (%)'],
        ];

        $resultados = [];
        foreach ($pairs as $key => $p) {
            $r = $this->pearson($p['x'], $p['y']);
            $resultados[$key] = [
                'r' => $r,
                'interpretacion' => $this->interpretarCorrelacion($r),
                'label_x' => $p['label_x'],
                'label_y' => $p['label_y'],
            ];
        }
        $resultados['total_gimnastas'] = $gimnastas->count();
        return $resultados;
    }

    private function pearson($x, $y)
    {
        $n = count($x);
        if ($n < 3) return 0;
        $sx = array_sum($x); $sy = array_sum($y);
        $sxy = 0; $sx2 = 0; $sy2 = 0;
        for ($i = 0; $i < $n; $i++) {
            $sxy += $x[$i] * $y[$i];
            $sx2 += $x[$i] ** 2;
            $sy2 += $y[$i] ** 2;
        }
        $num = $n * $sxy - $sx * $sy;
        $den = sqrt(($n * $sx2 - $sx ** 2) * ($n * $sy2 - $sy ** 2));
        return $den != 0 ? round($num / $den, 4) : 0;
    }

    private function interpretarCorrelacion($r): array
    {
        $abs = abs($r);
        if ($abs >= 0.8) $fuerza = 'Muy fuerte';
        elseif ($abs >= 0.6) $fuerza = 'Fuerte';
        elseif ($abs >= 0.4) $fuerza = 'Moderada';
        elseif ($abs >= 0.2) $fuerza = 'Débil';
        else $fuerza = 'Muy débil o nula';
        $direccion = $r >= 0 ? 'positiva' : 'negativa';
        return compact('fuerza', 'direccion');
    }

    private function analizarConIA($tipoAnalisis)
    {
        $gimnastas = Usuario::with(['lesiones', 'sesionesComoGimnasta'])
            ->leftJoin('gimnastas', 'usuarios.ID_Usuario', '=', 'gimnastas.ID_Usuario')
            ->whereHas('rol', fn($q) => $q->where('NombreRol', 'Gimnasta'))
            ->where('usuarios.activo', true)
            ->select('usuarios.*', 'gimnastas.Peso as g_peso', 'gimnastas.Altura as g_altura')
            ->get();

        $stats = [
            'total_gimnastas' => $gimnastas->count(),
            'sesiones_totales' => SesionEntrenamiento::count(),
            'lesiones_totales' => Lesion::where('activo', true)->count(),
            'entrenamientos_totales' => Entrenamiento::count(),
        ];

        $gimnastasData = $gimnastas->map(fn($g) => [
            'nombre' => $g->Nombre . ' ' . $g->Apellido,
            'nivel' => $g->Nivel,
            'edad' => $g->FechaNacimiento ? $g->FechaNacimiento->age : 0,
            'imc' => ($g->g_altura ?? $g->Altura) > 0 ? round(($g->g_peso ?? $g->Peso) / (($g->g_altura ?? $g->Altura) ** 2), 1) : 0,
            'rendimiento_%' => $g->sesionesComoGimnasta->count() > 0
                ? round($g->sesionesComoGimnasta->where('Estado', 'Completada')->count() / $g->sesionesComoGimnasta->count() * 100, 1)
                : 0,
            'lesiones_activas' => $g->lesiones->where('activo', true)->count(),
            'total_lesiones_hist' => $g->lesiones->count(),
        ])->toArray();

        $prompt = $this->construirPromptIA($tipoAnalisis, $stats, $gimnastasData);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    ['role' => 'system', 'content' => 'Eres un científico de datos deportivos especializado en minería de datos aplicada a gimnasia artística. Analiza los datos proporcionados y genera insights accionables. Responde SIEMPRE en español. Usa formato Markdown con tablas, listas y encabezados.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.4,
                'max_tokens' => 2000,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['choices'][0]['message']['content'] ?? 'No se pudo generar el análisis.';
            }
            Log::error('OpenAI API error: ' . $response->body());
            return 'Error al conectar con la IA: ' . ($response->json()['error']['message'] ?? 'Error desconocido');
        } catch (\Exception $e) {
            Log::error('OpenAI exception: ' . $e->getMessage());
            return 'Error de conexión: ' . $e->getMessage();
        }
    }

    private function construirPromptIA($tipo, $stats, $gimnastas)
    {
        $dataJson = json_encode($gimnastas, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        $statsJson = json_encode($stats, JSON_UNESCAPED_UNICODE);

        $base = "Estadísticas generales del sistema:\n$statsJson\n\nDatos individuales de gimnastas:\n$dataJson\n\n";

        $prompts = [
            'rendimiento' => $base . "Genera un análisis de minería de datos sobre el RENDIMIENTO DEPORTIVO:\n" .
                "1. Predicción de rendimiento futuro para cada gimnasta (etiquetas: bajo/medio/alto)\n" .
                "2. Factores clave que más correlacionan con alto rendimiento (IMC, edad, nivel, etc.)\n" .
                "3. Recomendaciones personalizadas para optimizar el rendimiento de cada gimnasta\n" .
                "4. Identifica outliers o casos atípicos",

            'lesiones' => $base . "Genera un análisis de minería de datos sobre RIESGO DE LESIONES:\n" .
                "1. Perfil de riesgo de lesión para cada gimnasta (bajo/medio/alto) basado en sus métricas\n" .
                "2. Patrones identificados entre edad, IMC, rendimiento y propensión a lesiones\n" .
                "3. Recomendaciones preventivas específicas para cada gimnasta\n" .
                "4. Correlaciones entre nivel (Junior/Senior) y tasa de lesiones",

            'recomendaciones' => $base . "Genera un análisis integral con RECOMENDACIONES PERSONALIZADAS:\n" .
                "1. Perfil completo de cada gimnasta con fortalezas y áreas de mejora\n" .
                "2. Plan de entrenamiento optimizado según sus métricas actuales\n" .
                "3. Sugerencias de periodización deportiva y carga de entrenamiento\n" .
                "4. Recomendaciones nutricionales basadas en IMC y nivel de actividad",
        ];

        return $prompts[$tipo] ?? $prompts['rendimiento'];
    }
}
