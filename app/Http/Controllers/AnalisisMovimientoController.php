<?php

namespace App\Http\Controllers;

use App\Models\AptitudMedica;
use App\Models\EvaluacionNutricional;
use App\Models\EvaluacionRespiratoria;
use App\Models\EvaluacionPsicologica;
use App\Models\EvaluacionFisiologica;
use App\Models\Gimnasta;
use App\Models\EstadisticaGimnasta;
use Illuminate\Http\Request;

class AnalisisMovimientoController extends Controller
{
    public function dashboard()
    {
        $gimnastasCount = Gimnasta::where('activo', true)->count();
        $evalNutricional = EvaluacionNutricional::count();
        $evalFisiologica = EvaluacionFisiologica::count();
        $evalPsicologica = EvaluacionPsicologica::count();

        return view('analisis.movimiento-dashboard', compact(
            'gimnastasCount', 'evalNutricional', 'evalFisiologica', 'evalPsicologica'
        ));
    }

    private function aptitudStats($aptitudes)
    {
        $total = $aptitudes->count();
        $aptos = $aptitudes->where('AptoParaEntrenar', true)->count();
        $noAptos = $total - $aptos;
        $gimnastasEvaluados = $aptitudes->pluck('ID_Gimnasta')->unique()->count();
        $diagnosticos = $aptitudes->groupBy('Diagnostico')->map(fn($g, $k) => ['diagnostico' => $k ?: 'Sin diagnóstico', 'count' => $g->count()])->sortByDesc('count')->take(5)->values();
        $porMes = $aptitudes->groupBy(fn($a) => \Carbon\Carbon::parse($a->FechaCertificacion)->format('Y-m'))->map(fn($g, $k) => ['mes' => $k, 'count' => $g->count()])->sortBy('mes')->values();
        return compact('total', 'aptos', 'noAptos', 'gimnastasEvaluados', 'diagnosticos', 'porMes');
    }

    // === APTITUD MÉDICA ===
    public function aptitudIndex()
    {
        $gimnastas = Gimnasta::where('activo', true)->get();
        $aptitudes = AptitudMedica::with('gimnasta')->get();
        $stats = $this->aptitudStats($aptitudes);
        return view('analisis.aptitud', compact('gimnastas', 'aptitudes') + $stats);
    }

    public function aptitudStore(Request $request)
    {
        $request->validate([
            'ID_Gimnasta' => 'required|integer',
            'FechaCertificacion' => 'required|date',
            'AptoParaEntrenar' => 'boolean',
        ]);

        AptitudMedica::create($request->all());
        return redirect()->back()->with('success', 'Certificación médica guardada');
    }

    // === EVALUACIÓN NUTRICIONAL ===
    public function nutricionalIndex()
    {
        $gimnastas = Gimnasta::where('activo', true)->get();
        $evaluaciones = EvaluacionNutricional::with('gimnasta')->get();
        $total = $evaluaciones->count();
        $gimnastasEvaluados = $evaluaciones->pluck('ID_Gimnasta')->unique()->count();
        $imcPromedio = $evaluaciones->whereNotNull('IMC')->avg('IMC');
        $grasaPromedio = $evaluaciones->whereNotNull('PorcentajeGrasa')->avg('PorcentajeGrasa');
        $muscularPromedio = $evaluaciones->whereNotNull('MasaMuscular')->avg('MasaMuscular');
        $hidratacionPromedio = $evaluaciones->whereNotNull('NivelHidratacion')->avg('NivelHidratacion');
        $porMes = $evaluaciones->groupBy(fn($e) => \Carbon\Carbon::parse($e->FechaEvaluacion)->format('Y-m'))->map(fn($g, $k) => ['mes' => $k, 'count' => $g->count()])->sortBy('mes')->values();
        return view('analisis.nutricional', compact('gimnastas', 'evaluaciones', 'total', 'gimnastasEvaluados', 'imcPromedio', 'grasaPromedio', 'muscularPromedio', 'hidratacionPromedio', 'porMes'));
    }

    public function nutricionalStore(Request $request)
    {
        $request->validate([
            'ID_Gimnasta' => 'required|integer',
            'FechaEvaluacion' => 'required|date',
            'Peso' => 'nullable|numeric',
            'Altura' => 'nullable|numeric',
        ]);

        $data = $request->all();
        if ($data['Peso'] && $data['Altura'] && $data['Altura'] > 0) {
            $data['IMC'] = round($data['Peso'] / (($data['Altura'] / 100) ** 2), 2);
        }

        EvaluacionNutricional::create($data);
        return redirect()->back()->with('success', 'Evaluación nutricional guardada');
    }

    // === EVALUACIÓN RESPIRATORIA ===
    public function respiratoriaIndex()
    {
        $gimnastas = Gimnasta::where('activo', true)->get();
        $evaluaciones = EvaluacionRespiratoria::with('gimnasta')->get();
        $total = $evaluaciones->count();
        $gimnastasEvaluados = $evaluaciones->pluck('ID_Gimnasta')->unique()->count();
        $capPromedio = $evaluaciones->whereNotNull('CapacidadPulmonar')->avg('CapacidadPulmonar');
        $satPromedio = $evaluaciones->whereNotNull('SaturacionOxigeno')->avg('SaturacionOxigeno');
        $porMes = $evaluaciones->groupBy(fn($e) => \Carbon\Carbon::parse($e->FechaEvaluacion)->format('Y-m'))->map(fn($g, $k) => ['mes' => $k, 'count' => $g->count()])->sortBy('mes')->values();
        $porDiagnostico = $evaluaciones->groupBy('DiagnosticoRespiratorio')->map(fn($g, $k) => ['diagnostico' => $k ?: 'Sin diagnóstico', 'count' => $g->count()])->sortByDesc('count')->take(5)->values();
        return view('analisis.respiratoria', compact('gimnastas', 'evaluaciones', 'total', 'gimnastasEvaluados', 'capPromedio', 'satPromedio', 'porMes', 'porDiagnostico'));
    }

    public function respiratoriaStore(Request $request)
    {
        $request->validate([
            'ID_Gimnasta' => 'required|integer',
            'FechaEvaluacion' => 'required|date',
        ]);

        EvaluacionRespiratoria::create($request->all());
        return redirect()->back()->with('success', 'Evaluación respiratoria guardada');
    }

    // === EVALUACIÓN PSICOLÓGICA ===
    public function psicologicaIndex()
    {
        $gimnastas = Gimnasta::where('activo', true)->get();
        $evaluaciones = EvaluacionPsicologica::with('gimnasta')->get();
        $total = $evaluaciones->count();
        $gimnastasEvaluados = $evaluaciones->pluck('ID_Gimnasta')->unique()->count();
        $ansiedadProm = $evaluaciones->whereNotNull('NivelAnsiedad')->avg('NivelAnsiedad');
        $estresProm = $evaluaciones->whereNotNull('NivelEstres')->avg('NivelEstres');
        $motivacionProm = $evaluaciones->whereNotNull('NivelMotivacion')->avg('NivelMotivacion');
        $suenoProm = $evaluaciones->whereNotNull('CalidadSueno')->avg('CalidadSueno');
        $concentracionProm = $evaluaciones->whereNotNull('Concentracion')->avg('Concentracion');
        return view('analisis.psicologica', compact('gimnastas', 'evaluaciones', 'total', 'gimnastasEvaluados', 'ansiedadProm', 'estresProm', 'motivacionProm', 'suenoProm', 'concentracionProm'));
    }

    public function psicologicaStore(Request $request)
    {
        $request->validate([
            'ID_Gimnasta' => 'required|integer',
            'FechaEvaluacion' => 'required|date',
        ]);

        EvaluacionPsicologica::create($request->all());
        return redirect()->back()->with('success', 'Evaluación psicológica guardada');
    }

    // === EVALUACIÓN FISIOLÓGICA ===
    public function fisiologicaIndex()
    {
        $gimnastas = Gimnasta::where('activo', true)->get();
        $evaluaciones = EvaluacionFisiologica::with('gimnasta')->get();
        $total = $evaluaciones->count();
        $gimnastasEvaluados = $evaluaciones->pluck('ID_Gimnasta')->unique()->count();
        $fcReposoProm = $evaluaciones->whereNotNull('FCReposo')->avg('FCReposo');
        $fcEsfuerzoProm = $evaluaciones->whereNotNull('FCEsfuerzo')->avg('FCEsfuerzo');
        $sisProm = $evaluaciones->whereNotNull('PresionSistolica')->avg('PresionSistolica');
        $diaProm = $evaluaciones->whereNotNull('PresionDiastolica')->avg('PresionDiastolica');
        $satProm = $evaluaciones->whereNotNull('SaturacionOxigeno')->avg('SaturacionOxigeno');
        return view('analisis.fisiologica', compact('gimnastas', 'evaluaciones', 'total', 'gimnastasEvaluados', 'fcReposoProm', 'fcEsfuerzoProm', 'sisProm', 'diaProm', 'satProm'));
    }

    public function fisiologicaStore(Request $request)
    {
        $request->validate([
            'ID_Gimnasta' => 'required|integer',
            'FechaEvaluacion' => 'required|date',
        ]);

        EvaluacionFisiologica::create($request->all());
        return redirect()->back()->with('success', 'Evaluación fisiológica guardada');
    }

    // === EXPORT METHODS ===
    private function exportCsv($data, $headers, $filename)
    {
        $handle = fopen('php://temp', 'w+');
        fputs($handle, "\xEF\xBB\xBF");
        fputcsv($handle, $headers);
        foreach ($data as $row) fputcsv($handle, $row);
        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);
        return response($content, 200, ['Content-Type' => 'text/csv; charset=utf-8', 'Content-Disposition' => "attachment; filename=\"$filename\""]);
    }

    public function aptitudExport()
    {
        $aptitudes = AptitudMedica::with('gimnasta')->get();
        return $this->exportCsv($aptitudes->map(fn($a) => [
            ($a->gimnasta->Nombre ?? '-') . ' ' . ($a->gimnasta->Apellido ?? ''),
            $a->FechaCertificacion, $a->Diagnostico ?? '-', $a->RestriccionesMedicas ?? '-',
            $a->AptoParaEntrenar ? 'Sí' : 'No', $a->MedicoResponsable ?? '-',
        ])->toArray(), ['Gimnasta', 'Fecha', 'Diagnóstico', 'Restricciones', 'Apto', 'Médico'], 'aptitud_medica.csv');
    }

    public function nutricionalExport()
    {
        $evaluaciones = EvaluacionNutricional::with('gimnasta')->get();
        return $this->exportCsv($evaluaciones->map(fn($e) => [
            ($e->gimnasta->Nombre ?? '-') . ' ' . ($e->gimnasta->Apellido ?? ''),
            $e->FechaEvaluacion, $e->Peso ?? '-', $e->Altura ?? '-', $e->IMC ?? '-',
            $e->PorcentajeGrasa ?? '-', $e->MasaMuscular ?? '-', $e->NivelHidratacion ?? '-',
        ])->toArray(), ['Gimnasta', 'Fecha', 'Peso', 'Altura', 'IMC', '% Grasa', 'Masa Musc.', 'Hidrat.'], 'nutricional.csv');
    }

    public function respiratoriaExport()
    {
        $evaluaciones = EvaluacionRespiratoria::with('gimnasta')->get();
        return $this->exportCsv($evaluaciones->map(fn($e) => [
            ($e->gimnasta->Nombre ?? '-') . ' ' . ($e->gimnasta->Apellido ?? ''),
            $e->FechaEvaluacion, $e->CapacidadPulmonar ?? '-', $e->SaturacionOxigeno ?? '-',
            $e->DiagnosticoRespiratorio ?? '-',
        ])->toArray(), ['Gimnasta', 'Fecha', 'Cap. Pulmonar', 'Sat O2', 'Diagnóstico'], 'respiratoria.csv');
    }

    public function psicologicaExport()
    {
        $evaluaciones = EvaluacionPsicologica::with('gimnasta')->get();
        return $this->exportCsv($evaluaciones->map(fn($e) => [
            ($e->gimnasta->Nombre ?? '-') . ' ' . ($e->gimnasta->Apellido ?? ''),
            $e->FechaEvaluacion, $e->NivelAnsiedad ?? '-', $e->NivelEstres ?? '-',
            $e->NivelMotivacion ?? '-', $e->CalidadSueno ?? '-', $e->Concentracion ?? '-',
        ])->toArray(), ['Gimnasta', 'Fecha', 'Ansiedad', 'Estrés', 'Motivación', 'Sueño', 'Concentración'], 'psicologica.csv');
    }

    public function fisiologicaExport()
    {
        $evaluaciones = EvaluacionFisiologica::with('gimnasta')->get();
        return $this->exportCsv($evaluaciones->map(fn($e) => [
            ($e->gimnasta->Nombre ?? '-') . ' ' . ($e->gimnasta->Apellido ?? ''),
            $e->FechaEvaluacion, $e->FCReposo ?? '-', $e->FCEsfuerzo ?? '-',
            $e->PresionSistolica ?? '-', $e->PresionDiastolica ?? '-', $e->SaturacionOxigeno ?? '-',
        ])->toArray(), ['Gimnasta', 'Fecha', 'FC Reposo', 'FC Esfuerzo', 'Pres. Sist.', 'Pres. Diast.', 'Sat O2'], 'fisiologica.csv');
    }

    public function aptitudExportPdf()
    {
        require_once(base_path('fpdf186/fpdf.php'));
        $aptitudes = AptitudMedica::with('gimnasta')->get();
        $pdf = new \FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 10, 'Aptitud Medica', 0, 1, 'C');
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->Cell(0, 6, 'Generado: ' . now()->format('d/m/Y H:i'), 0, 1, 'C');
        $pdf->Ln(5);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(40, 7, 'Gimnasta', 1); $pdf->Cell(25, 7, 'Fecha', 1); $pdf->Cell(60, 7, 'Diagnostico', 1); $pdf->Cell(15, 7, 'Apto', 1); $pdf->Ln();
        $pdf->SetFont('Arial', '', 8);
        foreach ($aptitudes as $a) {
            if ($pdf->GetY() > 250) { $pdf->AddPage(); }
            $pdf->Cell(40, 6, substr(($a->gimnasta->Nombre ?? '') . ' ' . ($a->gimnasta->Apellido ?? ''), 0, 20), 1);
            $pdf->Cell(25, 6, $a->FechaCertificacion, 1);
            $pdf->Cell(60, 6, substr($a->Diagnostico ?? '-', 0, 35), 1);
            $pdf->Cell(15, 6, $a->AptoParaEntrenar ? 'Si' : 'No', 1);
            $pdf->Ln();
        }
        return response($pdf->Output('S'), 200, ['Content-Type' => 'application/pdf', 'Content-Disposition' => 'attachment; filename="aptitud_medica.pdf"']);
    }

    public function nutricionalExportPdf()
    {
        $evaluaciones = EvaluacionNutricional::with('gimnasta')->get();
        $pdf = new \FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 10, 'Evaluacion Nutricional', 0, 1, 'C');
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->Cell(0, 6, 'Generado: ' . now()->format('d/m/Y H:i'), 0, 1, 'C');
        $pdf->Ln(5);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(35, 7, 'Gimnasta', 1); $pdf->Cell(20, 7, 'Fecha', 1); $pdf->Cell(15, 7, 'Peso', 1); $pdf->Cell(15, 7, 'Altura', 1); $pdf->Cell(15, 7, 'IMC', 1); $pdf->Cell(18, 7, '% Grasa', 1); $pdf->Cell(18, 7, 'Masa M.', 1); $pdf->Cell(18, 7, 'Hidrat.', 1); $pdf->Ln();
        $pdf->SetFont('Arial', '', 8);
        foreach ($evaluaciones as $e) {
            if ($pdf->GetY() > 250) { $pdf->AddPage(); }
            $pdf->Cell(35, 6, substr(($e->gimnasta->Nombre ?? '') . ' ' . ($e->gimnasta->Apellido ?? ''), 0, 18), 1);
            $pdf->Cell(20, 6, $e->FechaEvaluacion, 1);
            $pdf->Cell(15, 6, $e->Peso ?? '-', 1); $pdf->Cell(15, 6, $e->Altura ?? '-', 1);
            $pdf->Cell(15, 6, $e->IMC ?? '-', 1); $pdf->Cell(18, 6, $e->PorcentajeGrasa ?? '-', 1);
            $pdf->Cell(18, 6, $e->MasaMuscular ?? '-', 1); $pdf->Cell(18, 6, $e->NivelHidratacion ?? '-', 1);
            $pdf->Ln();
        }
        return response($pdf->Output('S'), 200, ['Content-Type' => 'application/pdf', 'Content-Disposition' => 'attachment; filename="nutricional.pdf"']);
    }

    public function respiratoriaExportPdf()
    {
        $evaluaciones = EvaluacionRespiratoria::with('gimnasta')->get();
        $pdf = new \FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 10, 'Evaluacion Respiratoria', 0, 1, 'C');
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->Cell(0, 6, 'Generado: ' . now()->format('d/m/Y H:i'), 0, 1, 'C');
        $pdf->Ln(5);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(40, 7, 'Gimnasta', 1); $pdf->Cell(25, 7, 'Fecha', 1); $pdf->Cell(30, 7, 'Cap. Pulmonar', 1); $pdf->Cell(20, 7, 'Sat O2', 1); $pdf->Cell(50, 7, 'Diagnostico', 1); $pdf->Ln();
        $pdf->SetFont('Arial', '', 8);
        foreach ($evaluaciones as $e) {
            if ($pdf->GetY() > 250) { $pdf->AddPage(); }
            $pdf->Cell(40, 6, substr(($e->gimnasta->Nombre ?? '') . ' ' . ($e->gimnasta->Apellido ?? ''), 0, 20), 1);
            $pdf->Cell(25, 6, $e->FechaEvaluacion, 1);
            $pdf->Cell(30, 6, $e->CapacidadPulmonar ?? '-', 1); $pdf->Cell(20, 6, $e->SaturacionOxigeno ?? '-', 1);
            $pdf->Cell(50, 6, substr($e->DiagnosticoRespiratorio ?? '-', 0, 30), 1);
            $pdf->Ln();
        }
        return response($pdf->Output('S'), 200, ['Content-Type' => 'application/pdf', 'Content-Disposition' => 'attachment; filename="respiratoria.pdf"']);
    }

    public function psicologicaExportPdf()
    {
        $evaluaciones = EvaluacionPsicologica::with('gimnasta')->get();
        $pdf = new \FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 10, 'Evaluacion Psicologica', 0, 1, 'C');
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->Cell(0, 6, 'Generado: ' . now()->format('d/m/Y H:i'), 0, 1, 'C');
        $pdf->Ln(5);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(35, 7, 'Gimnasta', 1); $pdf->Cell(22, 7, 'Fecha', 1); $pdf->Cell(18, 7, 'Ansiedad', 1); $pdf->Cell(16, 7, 'Estres', 1); $pdf->Cell(22, 7, 'Motivacion', 1); $pdf->Cell(14, 7, 'Sueno', 1); $pdf->Cell(25, 7, 'Concentracion', 1); $pdf->Ln();
        $pdf->SetFont('Arial', '', 8);
        foreach ($evaluaciones as $e) {
            if ($pdf->GetY() > 250) { $pdf->AddPage(); }
            $pdf->Cell(35, 6, substr(($e->gimnasta->Nombre ?? '') . ' ' . ($e->gimnasta->Apellido ?? ''), 0, 18), 1);
            $pdf->Cell(22, 6, $e->FechaEvaluacion, 1);
            $pdf->Cell(18, 6, $e->NivelAnsiedad ?? '-', 1); $pdf->Cell(16, 6, $e->NivelEstres ?? '-', 1);
            $pdf->Cell(22, 6, $e->NivelMotivacion ?? '-', 1); $pdf->Cell(14, 6, $e->CalidadSueno ?? '-', 1);
            $pdf->Cell(25, 6, $e->Concentracion ?? '-', 1);
            $pdf->Ln();
        }
        return response($pdf->Output('S'), 200, ['Content-Type' => 'application/pdf', 'Content-Disposition' => 'attachment; filename="psicologica.pdf"']);
    }

    public function fisiologicaExportPdf()
    {
        $evaluaciones = EvaluacionFisiologica::with('gimnasta')->get();
        $pdf = new \FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 10, 'Evaluacion Fisiologica', 0, 1, 'C');
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->Cell(0, 6, 'Generado: ' . now()->format('d/m/Y H:i'), 0, 1, 'C');
        $pdf->Ln(5);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(35, 7, 'Gimnasta', 1); $pdf->Cell(22, 7, 'Fecha', 1); $pdf->Cell(20, 7, 'FC Reposo', 1); $pdf->Cell(22, 7, 'FC Esfuerzo', 1); $pdf->Cell(20, 7, 'Pres. Sis.', 1); $pdf->Cell(20, 7, 'Pres. Dias.', 1); $pdf->Cell(16, 7, 'Sat O2', 1); $pdf->Ln();
        $pdf->SetFont('Arial', '', 8);
        foreach ($evaluaciones as $e) {
            if ($pdf->GetY() > 250) { $pdf->AddPage(); }
            $pdf->Cell(35, 6, substr(($e->gimnasta->Nombre ?? '') . ' ' . ($e->gimnasta->Apellido ?? ''), 0, 18), 1);
            $pdf->Cell(22, 6, $e->FechaEvaluacion, 1);
            $pdf->Cell(20, 6, $e->FCReposo ?? '-', 1); $pdf->Cell(22, 6, $e->FCEsfuerzo ?? '-', 1);
            $pdf->Cell(20, 6, $e->PresionSistolica ?? '-', 1); $pdf->Cell(20, 6, $e->PresionDiastolica ?? '-', 1);
            $pdf->Cell(16, 6, $e->SaturacionOxigeno ?? '-', 1);
            $pdf->Ln();
        }
        return response($pdf->Output('S'), 200, ['Content-Type' => 'application/pdf', 'Content-Disposition' => 'attachment; filename="fisiologica.pdf"']);
    }
}
