<?php

namespace App\Http\Controllers;

use App\Models\Video;
use App\Models\PoseResultado;
use App\Models\Gimnasta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class VideoAnalysisController extends Controller
{
    public function index()
    {
        $videos = Video::where('ID_Usuario', session('usuario_id'))
            ->orderBy('created_at', 'desc')
            ->get();
        return view('analisis.index', compact('videos'));
    }

    public function videoUpload()
    {
        $gimnastas = Gimnasta::where('activo', true)->get();
        $aparatos = \App\Models\Aparato::all();
        return view('analisis.video', compact('gimnastas', 'aparatos'));
    }

    public function uploadVideo(Request $request)
    {
        $request->validate([
            'video' => 'required|file|mimes:mp4,webm,ogg,mov,avi|max:204800',
        ]);

        $file = $request->file('video');
        $fileName = 'vid_' . uniqid() . '-' . $file->getClientOriginalName();
        $path = $file->storeAs('videos', $fileName, 'public_uploads');

        $video = Video::create([
            'ID_Usuario' => session('usuario_id'),
            'NombreArchivo' => $fileName,
            'NombreOriginal' => $file->getClientOriginalName(),
            'Tipo' => $file->getClientMimeType(),
            'Tamano' => $file->getSize(),
        ]);

        return response()->json([
            'success' => true,
            'video' => $video,
            'url' => asset('uploads/videos/' . $fileName)
        ]);
    }

    public function savePoseResults(Request $request)
    {
        $request->validate([
            'id_video' => 'required|integer',
            'frame_num' => 'required|integer',
            'landmarks' => 'required|json',
        ]);

        $video = Video::findOrFail($request->id_video);

        if ($video->ID_Usuario !== session('usuario_id')) {
            return response()->json(['success' => false, 'error' => 'Sin permiso'], 403);
        }

        PoseResultado::create([
            'ID_Video' => $request->id_video,
            'FrameNum' => $request->frame_num,
            'Landmarks' => json_decode($request->landmarks, true),
            'AngulosCalculados' => $request->angulos ? json_decode($request->angulos, true) : null,
        ]);

        return response()->json(['success' => true]);
    }

    private function validarDeducciones($deducciones)
    {
        $valoresValidos = [0.10, 0.30, 0.50];
        foreach ($deducciones as $d) {
            if (!in_array($d['valor'], $valoresValidos)) {
                $d['valor'] = 0.10;
            }
        }
        return $deducciones;
    }

    public function analyzeWithAI(Request $request)
    {
        $request->validate([
            'observaciones' => 'required|string|min:3',
            'ID_Aparato' => 'nullable|exists:aparatos,ID_Aparato',
            'angulos_detectados' => 'nullable|json',
        ]);

        $apiKey = env('OPENAI_API_KEY');

        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'error' => 'API key de OpenAI no configurada. Configura OPENAI_API_KEY en .env'
            ]);
        }

        try {
            $query = \Illuminate\Support\Facades\DB::table('deducciones_ejecucion')
                ->join('aparatos', 'deducciones_ejecucion.ID_Aparato', '=', 'aparatos.ID_Aparato')
                ->select('deducciones_ejecucion.*', 'aparatos.Nombre as aparato_nombre');

            if ($request->filled('ID_Aparato')) {
                $query->where('deducciones_ejecucion.ID_Aparato', $request->ID_Aparato);
            }

            $rules = $query->orderBy('deducciones_ejecucion.ID_Aparato')
                ->orderBy('deducciones_ejecucion.Tipo')
                ->get();

            $rulesContext = "TABLA DE DEDUCCIONES FIG 2025-2028 (GAM):\n\n";
            $currentAparato = '';
            foreach ($rules as $r) {
                if ($r->aparato_nombre !== $currentAparato) {
                    $currentAparato = $r->aparato_nombre;
                    $rulesContext .= "=== {$currentAparato} (Jurado {$r->Tipo}) ===\n";
                }
                $crit = $r->Criterios ? " ({$r->Criterios})" : '';
                $rulesContext .= "- {$r->Descripcion}: {$r->Valor_Deduccion} pts{$crit}\n";
            }

            $angulosContext = '';
            if ($request->filled('angulos_detectados')) {
                $angulos = json_decode($request->angulos_detectados, true);
                if (!empty($angulos)) {
                    $angulosContext = "ÁNGULOS ARTICULARES DETECTADOS (MediaPipe Pose):\n";
                    foreach ($angulos as $nombre => $valor) {
                        $angulosContext .= "- {$nombre}: {$valor}°\n";
                    }
                    $angulosContext .= "\n";
                }
            }

            $systemPrompt = "Eres un juez oficial de gimnasia artística especializado en el Código de Puntuación FIG 2025-2028. Tu función EXCLUSIVA es evaluar rutinas, identificar errores técnicos y aplicar las deducciones estándar según la normativa vigente.

REGLAS ESTRICTAS:
1. Responde SIEMPRE en español.
2. Identifica CADA error observado, indica la deducción estándar FIG (en puntos), cita la regla específica de la tabla y explica por qué aplica.
3. Clasifica cada error como: Pequeño (0.10), Medio (0.30) o Grande (0.50) según la tabla FIG.
4. Al final, entrega un resumen con tabla de deducciones aplicadas y puntaje total descontado.
5. Sé objetivo, técnico y estrictamente coherente con el reglamento FIG.
6. Si no hay suficiente información para determinar una deducción, indícalo claramente.
7. NO des consejos de entrenamiento, sugerencias de mejora técnica ni retroalimentación constructiva. Solo evalúa y aplica deducciones.
8. NO inventes deducciones que no estén en la tabla proporcionada.
9. Si recibes ángulos articulares detectados, úsalos como evidencia objetiva para respaldar tus deducciones. Por ejemplo, si un ángulo de rodilla muestra 150° y el ideal FIG es 180°, hay 30° de desviación lo que corresponde a una deducción.

FORMATO DE RESPUESTA:
Primero, realiza un análisis paso a paso de cada error observado, explicando tu razonamiento.
Luego, presenta la tabla de deducciones.
Finalmente, indica el puntaje total descontado y tu nivel de confianza en la evaluación.

EJEMPLO:
Usuario: \"El gimnasta flexionó las rodillas en el salto y separó las piernas en el mortal.\"
Ángulos: Rodilla Izq: 155°, Rodilla Der: 160°
Respuesta esperada:
- Paso 1: Identificar error 1 → Flexión de rodillas en salto. Los ángulos muestran 155° y 160°, desviación de 20-25° del ideal 180°. Aplica deducción Grande (0.50).
- Paso 2: Identificar error 2 → Separación de piernas en mortal. Error Pequeño (0.10).
- Tabla de deducciones con cada error, valor y regla.
- Total descontado: 0.60 pts.
- Confianza: alta

A continuación se listan las reglas de deducción FIG oficiales que DEBES usar como referencia:\n\n{$rulesContext}";

            $userContent = "Observaciones de la rutina de gimnasia artística para evaluación como juez FIG:\n\n{$request->observaciones}\n\n";
            if ($angulosContext) {
                $userContent .= "Datos objetivos capturados por el sistema de análisis de movimiento:\n{$angulosContext}\n";
            }
            $userContent .= "Evalúa los errores, aplica las deducciones correspondientes según el código FIG y entrega el puntaje total descontado. Sigue el formato de respuesta especificado: primero el análisis paso a paso, luego la tabla de deducciones, y finalmente el total y confianza.";

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userContent]
                ],
                'functions' => [[
                    'name' => 'registrar_evaluacion_fig',
                    'description' => 'Registra una evaluación de rutina de gimnasia artística con deducciones FIG',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'analisis_paso_a_paso' => [
                                'type' => 'string',
                                'description' => 'Análisis detallado paso a paso de cada error observado, explicando por qué aplica cada deducción y cómo se relaciona con los ángulos detectados'
                            ],
                            'deducciones' => [
                                'type' => 'array',
                                'description' => 'Lista de deducciones aplicadas',
                                'items' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'error' => ['type' => 'string', 'description' => 'Descripción del error detectado'],
                                        'valor' => ['type' => 'number', 'description' => 'Valor de la deducción en puntos (0.10, 0.30 o 0.50)'],
                                        'regla' => ['type' => 'string', 'description' => 'Regla FIG específica aplicada con código y justificación'],
                                    ],
                                    'required' => ['error', 'valor', 'regla']
                                ]
                            ],
                            'total_descontado' => [
                                'type' => 'number',
                                'description' => 'Suma total de todas las deducciones aplicadas'
                            ],
                            'confianza' => [
                                'type' => 'string',
                                'enum' => ['alta', 'media', 'baja'],
                                'description' => 'Nivel de confianza en la evaluación basado en la cantidad y claridad de la información disponible'
                            ]
                        ],
                        'required' => ['analisis_paso_a_paso', 'deducciones', 'total_descontado', 'confianza']
                    ]
                ]],
                'function_call' => ['name' => 'registrar_evaluacion_fig'],
                'max_tokens' => 1500,
                'temperature' => 0.3
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $functionCall = $data['choices'][0]['message']['function_call'] ?? null;

                if ($functionCall && $functionCall['name'] === 'registrar_evaluacion_fig') {
                    $args = json_decode($functionCall['arguments'], true);

                    $args['deducciones'] = $this->validarDeducciones($args['deducciones'] ?? []);

                    $totalCalculado = array_sum(array_column($args['deducciones'], 'valor'));
                    if (abs(($args['total_descontado'] ?? 0) - $totalCalculado) > 0.01) {
                        $args['total_descontado'] = round($totalCalculado, 2);
                    }

                    return response()->json([
                        'success' => true,
                        'evaluacion' => $args
                    ]);
                }

                return response()->json([
                    'success' => true,
                    'evaluacion' => [
                        'analisis_paso_a_paso' => $data['choices'][0]['message']['content'] ?? 'No se pudo generar análisis estructurado.',
                        'deducciones' => [],
                        'total_descontado' => 0,
                        'confianza' => 'baja'
                    ]
                ]);
            }

            return response()->json([
                'success' => false,
                'error' => 'Error de API: ' . ($response->json()['error']['message'] ?? 'Error desconocido')
            ]);

        } catch (\Exception $e) {
            Log::error('OpenAI API error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Error de conexión: ' . $e->getMessage()
            ]);
        }
    }

    public function angleAnalysis()
    {
        $aparatos = \App\Models\Aparato::all();
        return view('analisis.angulo', compact('aparatos'));
    }

    private function prepararGimnastas($gimnastaId = null)
    {
        $query = Gimnasta::with(['usuario.lesiones', 'usuario.sesionesComoGimnasta'])
            ->where('activo', true)->whereNotNull('ID_Usuario');
        if ($gimnastaId) $query->where('ID_Gimnasta', $gimnastaId);
        return $query->get();
    }

    private function calcularStats($gimnastas)
    {
        $total = $gimnastas->count();
        $conDatos = $gimnastas->filter(fn($g) => $g->Peso && $g->Altura);
        $imcProm = $conDatos->count() > 0 ? round($conDatos->avg(fn($g) => $g->Peso / ($g->Altura ** 2)), 1) : 0;
        $totalSes = 0; $comp = 0;
        foreach ($gimnastas as $g) {
            $ses = $g->usuario->sesionesComoGimnasta ?? collect();
            $totalSes += $ses->count();
            $comp += $ses->where('Estado', 'Completada')->count();
        }
        $rendProm = $totalSes > 0 ? round($comp / $totalSes * 100, 1) : 0;
        $lesAct = 0;
        foreach ($gimnastas as $g) $lesAct += ($g->usuario->lesiones ?? collect())->where('activo', true)->count();

        $data = $gimnastas->map(fn($g) => [
            'id' => $g->ID_Gimnasta,
            'nombre' => ($g->Nombre ?? $g->usuario->Nombre) . ' ' . ($g->Apellido ?? $g->usuario->Apellido),
            'nivel' => $g->Nivel ?? $g->usuario->Nivel,
            'peso' => (float)($g->Peso ?? 0),
            'altura' => (float)($g->Altura ?? 0),
            'imc' => $g->Altura > 0 ? round($g->Peso / ($g->Altura ** 2), 1) : 0,
            'edad' => $g->FechaNacimiento ? \Carbon\Carbon::parse($g->FechaNacimiento)->age : ($g->usuario->FechaNacimiento ? $g->usuario->FechaNacimiento->age : 0),
            'rendimiento' => ($g->usuario->sesionesComoGimnasta ?? collect())->count() > 0
                ? round($g->usuario->sesionesComoGimnasta->where('Estado', 'Completada')->count() / $g->usuario->sesionesComoGimnasta->count() * 100, 1) : 0,
            'lesiones_activas' => ($g->usuario->lesiones ?? collect())->where('activo', true)->count(),
        ]);

        $alertas = [];
        foreach ($data as $g) {
            $gas = [];
            if ($g['imc'] > 0 && ($g['imc'] < 18.5 || $g['imc'] > 25))
                $gas[] = ['tipo' => 'IMC', 'icono' => 'fa-weight', 'color' => '#f59e0b', 'texto' => "IMC {$g['imc']} fuera de rango normal (18.5-25)"];
            if ($g['rendimiento'] < 50)
                $gas[] = ['tipo' => 'Rendimiento', 'icono' => 'fa-chart-line', 'color' => '#ef4444', 'texto' => "Rendimiento {$g['rendimiento']}% — bajo del 50%"];
            if ($g['lesiones_activas'] > 0)
                $gas[] = ['tipo' => 'Lesión', 'icono' => 'fa-bolt', 'color' => '#dc2626', 'texto' => "{$g['lesiones_activas']} lesión(es) activa(s)"];
            if (!empty($gas)) $alertas[] = ['gimnasta' => $g['nombre'], 'alertas' => $gas];
        }

        $imcPorNivel = $data->groupBy('nivel')->map(fn($gs, $nivel) => [
            'nivel' => $nivel ?: 'Sin nivel', 'imc_promedio' => round($gs->avg('imc'), 1), 'count' => $gs->count(),
        ])->values();

        $distEdad = [0,0,0,0,0,0,0];
        $rangos = ['0-12','13-15','16-18','19-21','22-25','26-30','30+'];
        foreach ($data as $g) {
            $e = $g['edad'];
            if ($e <= 12) $distEdad[0]++; elseif ($e <= 15) $distEdad[1]++; elseif ($e <= 18) $distEdad[2]++;
            elseif ($e <= 21) $distEdad[3]++; elseif ($e <= 25) $distEdad[4]++; elseif ($e <= 30) $distEdad[5]++; else $distEdad[6]++;
        }
        $pesoAlturaData = $data->filter(fn($g) => $g['peso'] > 0 && $g['altura'] > 0)->values();

        return compact('total','imcProm','rendProm','lesAct','comp','data','alertas','imcPorNivel','distEdad','rangos','pesoAlturaData');
    }

    public function exportExcel(Request $request)
    {
        $gimnastas = $this->prepararGimnastas($request->gimnasta_id);
        $s = $this->calcularStats($gimnastas);

        $html = '<html><meta charset="UTF-8"><style>
            body{font-family:Arial,sans-serif;padding:20px}
            h1{color:#1a237e;text-align:center;border-bottom:3px solid #1a237e;padding-bottom:10px}
            .sub{text-align:center;color:#666;margin-bottom:20px}
            .stats{display:flex;gap:10px;margin-bottom:20px}
            .stat-card{flex:1;padding:12px;border-radius:8px;color:#fff;text-align:center}
            .sc1{background:linear-gradient(135deg,#17a2b8,#0d6efd)}
            .sc2{background:linear-gradient(135deg,#28a745,#20c997)}
            .sc3{background:linear-gradient(135deg,#ffc107,#fd7e14)}
            .sc4{background:linear-gradient(135deg,#dc3545,#e83e8c)}
            .sc5{background:linear-gradient(135deg,#6c757d,#495057)}
            .stat-card .num{font-size:24px;font-weight:bold}
            table{width:100%;border-collapse:collapse;margin-top:15px}
            th{background:#1a237e;color:#fff;padding:8px;text-align:left}
            td{padding:6px;border:1px solid #ddd}
            tr:nth-child(even){background:#f5f5f5}
            .barra{height:14px;border-radius:7px;display:inline-block}
            .badge{display:inline-block;padding:2px 8px;border-radius:10px;color:#fff;font-size:11px}
            .bg-success{background:#28a745} .bg-warning{background:#ffc107;color:#333} .bg-danger{background:#dc3545}
            .chart-section{margin-top:25px}
            .chart-title{color:#1a237e;margin-bottom:10px}
            .bar-chart{margin:5px 0}
            .bar-label{display:inline-block;width:100px}
            .bar-track{display:inline-block;width:300px;height:20px;background:#eee;border-radius:4px;vertical-align:middle}
            .bar-fill{height:20px;border-radius:4px}
        </style></head><body>
        <h1>Estadísticas Deportivas</h1>
        <p class="sub">Generado: ' . now()->format('d/m/Y H:i') . ' | ' . ($request->gimnasta_id ? 'Gimnasta filtrado' : 'Todos los gimnastas') . '</p>
        <div class="stats">
            <div class="stat-card sc1"><div class="num">' . $s['total'] . '</div>Total Gimnastas</div>
            <div class="stat-card sc2"><div class="num">' . ($s['imcProm'] ?: 'N/A') . '</div>IMC Promedio</div>
            <div class="stat-card sc3"><div class="num">' . $s['rendProm'] . '%</div>Rendimiento</div>
            <div class="stat-card sc4"><div class="num">' . $s['lesAct'] . '</div>Lesiones Activas</div>
            <div class="stat-card sc5"><div class="num">' . $s['comp'] . '</div>Completadas</div>
        </div>';

        // IMC por nivel chart
        $html .= '<div class="chart-section"><h3 class="chart-title">IMC Promedio por Nivel</h3>';
        $maxImc = $s['imcPorNivel']->max('imc_promedio') ?: 1;
        foreach ($s['imcPorNivel'] as $n) {
            $w = round($n['imc_promedio'] / $maxImc * 300);
            $html .= '<div class="bar-chart"><span class="bar-label">' . $n['nivel'] . ' (' . $n['count'] . ')</span>
                <span class="bar-track"><span class="bar-fill" style="width:' . $w . 'px;background:#0d6efd"></span></span>
                <span style="margin-left:5px">' . $n['imc_promedio'] . '</span></div>';
        }
        $html .= '</div>';

        // Table
        $html .= '<table><thead><tr><th>Gimnasta</th><th>Nivel</th><th>Peso</th><th>Altura</th><th>IMC</th><th>Edad</th><th>Rendimiento</th><th>Lesiones</th></tr></thead><tbody>';
        foreach ($s['data'] as $g) {
            $imcClass = $g['imc'] > 0 && ($g['imc'] < 18.5 || $g['imc'] > 25) ? 'bg-warning' : 'bg-success';
            $rendClass = $g['rendimiento'] >= 70 ? 'bg-success' : ($g['rendimiento'] >= 40 ? 'bg-warning' : 'bg-danger');
            $lesClass = $g['lesiones_activas'] > 0 ? 'bg-danger' : '';
            $html .= '<tr><td>' . $g['nombre'] . '</td><td>' . ($g['nivel'] ?: '-') . '</td>
                <td>' . ($g['peso'] ?: '-') . '</td><td>' . ($g['altura'] ?: '-') . '</td>
                <td><span class="badge ' . $imcClass . '">' . ($g['imc'] ?: '-') . '</span></td>
                <td>' . ($g['edad'] ?: '-') . '</td>
                <td><span class="badge ' . $rendClass . '">' . $g['rendimiento'] . '%</span></td>
                <td><span class="badge ' . $lesClass . '">' . $g['lesiones_activas'] . '</span></td></tr>';
        }
        $html .= '</tbody></table></body></html>';

        return response($html, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="estadisticas_' . now()->format('Y-m-d') . '.xls"'
        ]);
    }

    public function exportPdf(Request $request)
    {
        $gimnastas = $this->prepararGimnastas($request->gimnasta_id);
        $s = $this->calcularStats($gimnastas);

        require_once(base_path('fpdf186/fpdf.php'));
        $pdf = new \FPDF('L', 'mm', 'A4');
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->SetTextColor(26, 35, 126);
        $pdf->Cell(0, 12, 'Estadisticas Deportivas', 0, 1, 'C');
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->SetTextColor(100, 100, 100);
        $pdf->Cell(0, 5, 'Generado: ' . now()->format('d/m/Y H:i'), 0, 1, 'C');
        $pdf->Ln(4);

        // Stats cards
        $stats = [
            ['Total Gimnastas', $s['total'], 23, 162, 184],
            ['IMC Promedio', $s['imcProm'] ?: 'N/A', 40, 167, 69],
            ['Rendimiento', $s['rendProm'] . '%', 255, 193, 7],
            ['Lesiones Activas', $s['lesAct'], 220, 53, 69],
            ['Completadas', $s['comp'], 108, 117, 125],
        ];
        $x0 = $pdf->GetX();
        foreach ($stats as $i => $st) {
            $pdf->SetFillColor($st[2], $st[3], $st[4]);
            $pdf->Rect($x0 + $i * 56 + 2, $pdf->GetY(), 50, 20, 'F');
            $pdf->SetXY($x0 + $i * 56 + 2, $pdf->GetY() + 2);
            $pdf->SetFont('Arial', 'B', 14);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(50, 8, $st[1], 0, 0, 'C');
            $pdf->SetXY($x0 + $i * 56 + 2, $pdf->GetY() + 8);
            $pdf->SetFont('Arial', '', 7);
            $pdf->Cell(50, 5, $st[0], 0, 0, 'C');
        }
        $pdf->Ln(22);

        // IMC chart
        $pdf->SetTextColor(26, 35, 126);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(0, 6, 'IMC Promedio por Nivel:', 0, 1);
        $maxImc = $s['imcPorNivel']->max('imc_promedio') ?: 1;
        $pdf->SetFont('Arial', '', 7);
        $yStart = $pdf->GetY();
        foreach ($s['imcPorNivel'] as $i => $n) {
            $barW = ($n['imc_promedio'] / $maxImc) * 80;
            $pdf->SetXY(10, $yStart + $i * 7);
            $pdf->Cell(35, 5, $n['nivel'] . ' (' . $n['count'] . ')');
            $pdf->SetFillColor(13, 110, 253);
            $pdf->Rect(50, $yStart + $i * 7 + 1, max($barW, 2), 4, 'F');
            $pdf->SetXY(55 + $barW, $yStart + $i * 7);
            $pdf->Cell(10, 5, number_format($n['imc_promedio'], 1));
        }
        $pdf->SetY($yStart + count($s['imcPorNivel']) * 7 + 5);

        // Table header
        $pdf->SetFillColor(26, 35, 126);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(45, 7, 'Gimnasta', 1, 0, '', true);
        $pdf->Cell(14, 7, 'Nivel', 1, 0, 'C', true);
        $pdf->Cell(16, 7, 'Peso', 1, 0, 'C', true);
        $pdf->Cell(18, 7, 'Altura', 1, 0, 'C', true);
        $pdf->Cell(14, 7, 'IMC', 1, 0, 'C', true);
        $pdf->Cell(12, 7, 'Edad', 1, 0, 'C', true);
        $pdf->Cell(22, 7, 'Rendimiento', 1, 0, 'C', true);
        $pdf->Cell(22, 7, 'Lesiones', 1, 0, 'C', true);
        $pdf->Ln();

        // Table rows
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', '', 7);
        foreach ($s['data'] as $i => $g) {
            if ($pdf->GetY() > 170) { $pdf->AddPage('L'); }
            $pdf->SetFillColor($i % 2 == 0 ? 245 : 255, $i % 2 == 0 ? 245 : 255, $i % 2 == 0 ? 245 : 255);
            $pdf->Cell(45, 6, substr($g['nombre'], 0, 25), 1, 0, '', true);
            $pdf->Cell(14, 6, $g['nivel'] ?: '-', 1, 0, 'C', true);
            $pdf->Cell(16, 6, $g['peso'] ?: '-', 1, 0, 'C', true);
            $pdf->Cell(18, 6, $g['altura'] ?: '-', 1, 0, 'C', true);
            $imcColor = ($g['imc'] > 0 && ($g['imc'] < 18.5 || $g['imc'] > 25)) ? [255, 193, 7] : [40, 167, 69];
            $pdf->SetFillColor($imcColor[0], $imcColor[1], $imcColor[2]);
            $pdf->Cell(14, 6, $g['imc'] ?: '-', 1, 0, 'C', true);
            $pdf->SetFillColor($i % 2 == 0 ? 245 : 255, $i % 2 == 0 ? 245 : 255, $i % 2 == 0 ? 245 : 255);
            $pdf->Cell(12, 6, $g['edad'] ?: '-', 1, 0, 'C', true);
            // Rendimiento bar
            $rendW = max($g['rendimiento'] * 0.18, 1);
            $rendColor = $g['rendimiento'] >= 70 ? [40, 167, 69] : ($g['rendimiento'] >= 40 ? [255, 193, 7] : [220, 53, 69]);
            $pdf->Cell(22, 6, '', 1, 0, '', true);
            $pdf->SetFillColor($rendColor[0], $rendColor[1], $rendColor[2]);
            $pdf->Rect($pdf->GetX() - 20, $pdf->GetY() + 1, $rendW, 4, 'F');
            $pdf->SetXY($pdf->GetX() - 20, $pdf->GetY());
            $pdf->Cell(20, 6, number_format($g['rendimiento'], 1) . '%', 0, 0, 'R');
            $pdf->SetFillColor($i % 2 == 0 ? 245 : 255, $i % 2 == 0 ? 245 : 255, $i % 2 == 0 ? 245 : 255);
            $pdf->Cell(22, 6, $g['lesiones_activas'], 1, 0, 'C', true);
            $pdf->Ln();
        }

        return response($pdf->Output('S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="estadisticas_' . now()->format('Y-m-d') . '.pdf"'
        ]);
    }

    public function statistics(Request $request)
    {
        $gimnastasLista = Gimnasta::where('activo', true)->whereNotNull('ID_Usuario')->get(['ID_Gimnasta', 'Nombre', 'Apellido']);
        $gimnastas = $this->prepararGimnastas($request->gimnasta_id);
        $s = $this->calcularStats($gimnastas);

        return view('analisis.estadisticas', [
            'gimnastasLista' => $gimnastasLista,
            'gimnastaSeleccionado' => $request->gimnasta_id,
            'totalGimnastas' => $s['total'],
            'imcPromedio' => $s['imcProm'],
            'rendimientoPromedio' => $s['rendProm'],
            'lesionesActivas' => $s['lesAct'],
            'completadas' => $s['comp'],
            'gimnastasData' => $s['data'],
            'alertas' => $s['alertas'],
            'imcPorNivel' => $s['imcPorNivel'],
            'distEdad' => $s['distEdad'],
            'rangos' => $s['rangos'],
            'pesoAlturaData' => $s['pesoAlturaData'],
        ]);
    }
}
