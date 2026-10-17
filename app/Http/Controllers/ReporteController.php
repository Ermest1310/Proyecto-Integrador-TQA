<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\SolicitudNivel;
use App\Models\SesionEntrenamiento;
use App\Models\Postulacion;
use App\Models\Lesion;
use App\Models\Competencia;
use App\Services\PdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
require_once(base_path('fpdf186/fpdf.php'));

class ReporteController extends Controller
{
    public function index()
    {
        $estadisticas = [
            'usuarios' => $this->getEstadisticasUsuarios(),
            'niveles' => $this->getEstadisticasNiveles(),
            'sesiones' => $this->getEstadisticasSesiones(),
            'postulaciones' => $this->getEstadisticasPostulaciones(),
            'lesiones' => $this->getEstadisticasLesiones(),
        ];

        return view('admin.reportes.index', compact('estadisticas'));
    }

    private function getEstadisticasUsuarios()
    {
        return [
            'total' => Usuario::count(),
            'activos' => Usuario::where('activo', true)->count(),
            'por_rol' => Usuario::selectRaw('roles.NombreRol, COUNT(*) as total')
                ->join('roles', 'usuarios.ID_Rol', '=', 'roles.ID_Rol')
                ->groupBy('roles.NombreRol')
                ->pluck('total', 'NombreRol'),
        ];
    }

    private function getEstadisticasNiveles()
    {
        return [
            'por_nivel' => Usuario::selectRaw('Nivel, COUNT(*) as total')
                ->whereHas('rol', function($q) {
                    $q->where('NombreRol', 'Gimnasta');
                })
                ->groupBy('Nivel')
                ->pluck('total', 'Nivel'),
            'solicitudes_pendientes' => SolicitudNivel::where('estado', 'pendiente')->count(),
            'solicitudes_aprobadas' => SolicitudNivel::where('estado', 'aprobada')->count(),
            'solicitudes_rechazadas' => SolicitudNivel::where('estado', 'rechazada')->count(),
        ];
    }

    private function getEstadisticasSesiones()
    {
        return [
            'total' => SesionEntrenamiento::count(),
            'programadas' => SesionEntrenamiento::where('Estado', 'Programada')->count(),
            'completadas' => SesionEntrenamiento::where('Estado', 'Completada')->count(),
            'canceladas' => SesionEntrenamiento::where('Estado', 'Cancelada')->count(),
            'este_mes' => SesionEntrenamiento::whereMonth('Fecha', now()->month)->count(),
        ];
    }

    private function getEstadisticasPostulaciones()
    {
        return [
            'total' => Postulacion::count(),
            'pendientes' => Postulacion::where('estado', 'pendiente')->count(),
            'aprobadas' => Postulacion::where('estado', 'aprobada')->count(),
            'rechazadas' => Postulacion::where('estado', 'rechazada')->count(),
        ];
    }

    private function getEstadisticasLesiones()
    {
        return [
            'total' => Lesion::count(),
            'activas' => Lesion::where('activo', true)->count(),
            'por_tipo' => Lesion::selectRaw('Tipo, COUNT(*) as total')
                ->groupBy('Tipo')
                ->pluck('total', 'Tipo'),
        ];
    }

    public function backup()
    {
        $backupDir = storage_path('app/backups');
        $backups = [];

        if (file_exists($backupDir)) {
            $files = glob("{$backupDir}/*.sql");
            foreach ($files as $file) {
                $backups[] = [
                    'nombre' => basename($file),
                    'tamano' => round(filesize($file) / 1024 / 1024, 2),
                    'fecha' => date('Y-m-d H:i:s', filemtime($file)),
                ];
            }
            usort($backups, function($a, $b) {
                return strtotime($b['fecha']) - strtotime($a['fecha']);
            });
        }

        return view('admin.reportes.backup', compact('backups'));
    }

    public function crearBackup(Request $request)
    {
        $type = $request->input('type', 'completo');
        
        \Artisan::call('backup:database', ['--type' => $type]);
        
        return back()->with('success', 'Backup creado exitosamente');
    }

    public function descargarBackup($filename)
    {
        $filepath = storage_path("app/backups/{$filename}");
        
        if (file_exists($filepath)) {
            return response()->download($filepath);
        }
        
        return back()->with('error', 'Archivo no encontrado');
    }

    public function reporteCompleto(PdfService $pdfService)
    {
        $datos = $pdfService->generarReporteCompleto();
        return view('admin.reportes.completo', compact('datos'));
    }

    public function exportarPdf(Request $request, PdfService $pdfService)
    {
        $tipo = $request->input('tipo', 'completo');
        
        $pdf = new \FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);
        
        switch($tipo) {
            case 'gimnastas':
                $datos = $pdfService->generarReporteGimnastas();
                $this->generarPdfGimnastas($pdf, $datos);
                break;
            case 'sesiones':
                $datos = $pdfService->generarReporteSesiones();
                $this->generarPdfSesiones($pdf, $datos);
                break;
            case 'estadisticas':
                $datos = [
                    'usuarios' => $this->getEstadisticasUsuarios(),
                    'niveles' => $this->getEstadisticasNiveles(),
                    'sesiones' => $this->getEstadisticasSesiones(),
                    'postulaciones' => $this->getEstadisticasPostulaciones(),
                    'lesiones' => $this->getEstadisticasLesiones(),
                ];
                $this->generarPdfEstadisticas($pdf, $datos);
                break;
            default:
                $datos = $pdfService->generarReporteCompleto();
                $this->generarPdfCompleto($pdf, $datos);
        }
        
        return response($pdf->Output('S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="reporte_' . $tipo . '_' . now()->format('Y-m-d') . '.pdf"'
        ]);
    }

    public function exportarReporteGimnasta($id)
    {
        $gimnasta = Usuario::with(['rol', 'lesiones' => function($q) {
            $q->where('activo', true);
        }])->findOrFail($id);

        $sesiones = SesionEntrenamiento::with(['entrenador', 'aparato'])
            ->where('ID_Gimnasta', $id)
            ->where('Estado', '!=', 'Cancelada')
            ->orderBy('Fecha', 'desc')
            ->get();

        $estadisticas = [
            'total' => $sesiones->count(),
            'completadas' => $sesiones->where('Estado', 'Completada')->count(),
            'programadas' => $sesiones->where('Estado', 'Programada')->count(),
            'lesiones_activas' => $gimnasta->lesiones->where('Estado', 'Lesionado')->count(),
            'promedio_duracion' => $sesiones->where('Estado', 'Completada')->whereNotNull('Duracion_Minutos')->avg('Duracion_Minutos'),
        ];

        $pdf = new \FPDF();
        $pdf->AddPage();
        $pdf->SetFillColor(13, 110, 253);
        $pdf->Rect(0, 0, 210, 35, 'F');
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('Arial', 'B', 20);
        $pdf->SetY(12);
        $pdf->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'REPORTE INDIVIDUAL'), 0, 1, 'C');
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(0, 5, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $gimnasta->Nombre . ' ' . $gimnasta->Apellido), 0, 1, 'C');
        $pdf->SetFont('Arial', 'I', 7);
        $pdf->SetTextColor(230, 230, 230);
        $usuario = session('usuario_id') ? Usuario::find(session('usuario_id'))->Nombre . ' ' . Usuario::find(session('usuario_id'))->Apellido : 'Sistema';
        $pdf->Cell(0, 4, 'Generado: ' . now()->format('d/m/Y H:i:s') . ' | Por: ' . iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $usuario), 0, 1, 'C');
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln(8);

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetFillColor(23, 162, 184);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(0, 8, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', '  DATOS PERSONALES'), 1, 1, 'L', true);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetFillColor(248, 249, 250);
        $pdf->Cell(60, 7, 'Nombre Completo:', 1, 0, 'L', true);
        $pdf->Cell(0, 7, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $gimnasta->Nombre . ' ' . $gimnasta->Apellido), 1, 1);
        $pdf->Cell(60, 7, 'Email:', 1, 0, 'L', true);
        $pdf->Cell(0, 7, $gimnasta->Email, 1, 1);
        $pdf->Cell(60, 7, 'Edad:', 1, 0, 'L', true);
        $pdf->Cell(0, 7, \Carbon\Carbon::parse($gimnasta->FechaNacimiento)->age . ' años', 1, 1);
        $pdf->Cell(60, 7, 'Nacionalidad:', 1, 0, 'L', true);
        $pdf->Cell(0, 7, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $gimnasta->Nacionalidad), 1, 1);
        $pdf->Cell(60, 7, 'Nivel:', 1, 0, 'L', true);
        $pdf->Cell(0, 7, $gimnasta->Nivel, 1, 1);
        $pdf->Ln(5);

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetFillColor(40, 167, 69);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(0, 8, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', '  DATOS FISICOS'), 1, 1, 'L', true);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetFillColor(248, 249, 250);
        $pdf->Cell(60, 7, 'Peso:', 1, 0, 'L', true);
        $pdf->Cell(0, 7, $gimnasta->Peso . ' kg', 1, 1);
        $pdf->Cell(60, 7, 'Altura:', 1, 0, 'L', true);
        $pdf->Cell(0, 7, $gimnasta->Altura . ' m', 1, 1);
        $imc = ($gimnasta->Altura > 0) ? round($gimnasta->Peso / ($gimnasta->Altura * $gimnasta->Altura), 2) : 0;
        $pdf->Cell(60, 7, 'IMC:', 1, 0, 'L', true);
        $pdf->Cell(0, 7, $imc, 1, 1);
        $pdf->Ln(5);

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetFillColor(255, 193, 7);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(0, 8, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', '  ESTADISTICAS DE SESIONES'), 1, 1, 'L', true);
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetFillColor(248, 249, 250);
        $pdf->Cell(60, 7, 'Total de Sesiones:', 1, 0, 'L', true);
        $pdf->Cell(0, 7, $estadisticas['total'], 1, 1);
        $pdf->Cell(60, 7, 'Sesiones Completadas:', 1, 0, 'L', true);
        $pdf->Cell(0, 7, $estadisticas['completadas'], 1, 1);
        $pdf->Cell(60, 7, 'Sesiones Programadas:', 1, 0, 'L', true);
        $pdf->Cell(0, 7, $estadisticas['programadas'], 1, 1);
        $pdf->Cell(60, 7, 'Duracion Promedio:', 1, 0, 'L', true);
        $pdf->Cell(0, 7, ($estadisticas['promedio_duracion'] ? round($estadisticas['promedio_duracion']) : 0) . ' min', 1, 1);
        $pdf->Ln(5);

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetFillColor(220, 53, 69);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(0, 8, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', '  LESIONES'), 1, 1, 'L', true);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetFillColor(248, 249, 250);
        $pdf->Cell(60, 7, 'Lesiones Activas:', 1, 0, 'L', true);
        $pdf->Cell(0, 7, $estadisticas['lesiones_activas'], 1, 1);
        
        $lesionesActivas = $gimnasta->lesiones->where('Estado', 'Lesionado');
        if ($lesionesActivas->count() > 0) {
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->SetFillColor(220, 53, 69);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(50, 7, 'TIPO', 1, 0, 'C', true);
            $pdf->Cell(40, 7, 'PARTE', 1, 0, 'C', true);
            $pdf->Cell(30, 7, 'GRAVEDAD', 1, 0, 'C', true);
            $pdf->Cell(70, 7, 'TRATAMIENTO', 1, 1, 'C', true);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont('Arial', '', 7);
            foreach ($lesionesActivas as $l) {
                $pdf->Cell(50, 6, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', substr($l->Tipo, 0, 24)), 1, 0, 'L');
                $pdf->Cell(40, 6, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', substr($l->ParteCuerpo, 0, 20)), 1, 0, 'L');
                $pdf->Cell(30, 6, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $l->Gravedad), 1, 0, 'C');
                $pdf->Cell(70, 6, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', substr($l->Tratamiento ?? 'N/A', 0, 34)), 1, 1, 'L');
            }
        }
        $pdf->Ln(5);

        if ($sesiones->count() > 0) {
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->SetFillColor(23, 162, 184);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(0, 8, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', '  ULTIMAS SESIONES'), 1, 1, 'L', true);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont('Arial', 'B', 7);
            $pdf->SetFillColor(200, 200, 200);
            $pdf->Cell(25, 7, 'FECHA', 1, 0, 'C', true);
            $pdf->Cell(40, 7, 'ENTRENADOR', 1, 0, 'C', true);
            $pdf->Cell(35, 7, 'APARATO', 1, 0, 'C', true);
            $pdf->Cell(20, 7, 'DURACION', 1, 0, 'C', true);
            $pdf->Cell(25, 7, 'ESTADO', 1, 1, 'C', true);
            $pdf->SetFont('Arial', '', 7);
            $i = 0;
            foreach ($sesiones->take(20) as $s) {
                $fill = $i % 2 == 0;
                $pdf->SetFillColor(248, 249, 250);
                $pdf->Cell(25, 6, \Carbon\Carbon::parse($s->Fecha)->format('d/m/Y'), 1, 0, 'C', $fill);
                $pdf->Cell(40, 6, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', substr($s->entrenador->Nombre . ' ' . $s->entrenador->Apellido, 0, 22)), 1, 0, 'L', $fill);
                $pdf->Cell(35, 6, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', substr($s->aparato->Nombre ?? 'General', 0, 18)), 1, 0, 'L', $fill);
                $pdf->Cell(20, 6, ($s->Duracion_Minutos ?? '-') . ' min', 1, 0, 'C', $fill);
                $pdf->SetFillColor($s->Estado == 'Completada' ? 40 : ($s->Estado == 'Programada' ? 23 : 220), 167, $s->Estado == 'Completada' ? 69 : ($s->Estado == 'Programada' ? 162 : 53));
                $pdf->SetTextColor(255, 255, 255);
                $pdf->SetFont('Arial', 'B', 7);
                $pdf->Cell(25, 6, $s->Estado, 1, 1, 'C', true);
                $pdf->SetFont('Arial', '', 7);
                $pdf->SetTextColor(0, 0, 0);
                $i++;
            }
        }

        return response($pdf->Output('S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="reporte_' . str_replace(' ', '_', $gimnasta->Nombre . '_' . $gimnasta->Apellido) . '_' . now()->format('Y-m-d') . '.pdf"'
        ]);
    }
    
    private function generarPdfCompleto($pdf, $datos)
    {
        $pdf->SetFillColor(13, 110, 253);
        $pdf->Rect(0, 0, 210, 35, 'F');
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('Arial', 'B', 22);
        $pdf->SetY(12);
        $pdf->Cell(0, 10, 'REPORTE COMPLETO DEL SISTEMA', 0, 1, 'C');
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(0, 5, 'Sistema de Gestion Deportiva - Gimnasia', 0, 1, 'C');
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->SetTextColor(230, 230, 230);
        $usuarioId = session('usuario_id');
        $usuario = $usuarioId ? Usuario::find($usuarioId)->Nombre . ' ' . Usuario::find($usuarioId)->Apellido : 'Sistema';
        $pdf->Cell(0, 4, 'Generado: ' . now()->format('d/m/Y H:i:s') . ' | Por: ' . iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $usuario), 0, 1, 'C');
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln(10);
        
        $pdf->SetDrawColor(40, 167, 69);
        $pdf->SetLineWidth(0.8);
        $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
        $pdf->SetLineWidth(0.2);
        $pdf->Ln(2);
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->SetTextColor(40, 167, 69);
        $pdf->Cell(10, 8, chr(149), 0, 0);
        $pdf->Cell(0, 8, 'GIMNASTAS Y RENDIMIENTO EN SESIONES', 0, 1);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln(3);
        
        $pdf->SetFillColor(40, 167, 69);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetDrawColor(40, 167, 69);
        $pdf->Cell(50, 8, 'GIMNASTA', 1, 0, 'C', true);
        $pdf->Cell(22, 8, 'NIVEL', 1, 0, 'C', true);
        $pdf->Cell(18, 8, 'TOTAL', 1, 0, 'C', true);
        $pdf->Cell(25, 8, 'COMPLETADAS', 1, 0, 'C', true);
        $pdf->Cell(25, 8, 'TIEMPO (h)', 1, 0, 'C', true);
        $pdf->Cell(50, 8, 'ENTRENADORES', 1, 1, 'C', true);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetDrawColor(200, 200, 200);
        
        $pdf->SetFont('Arial', '', 7);
        $i = 0;
        foreach($datos['gimnastas_sesiones'] as $g) {
            $fill = $i % 2 == 0;
            $pdf->SetFillColor(248, 249, 250);
            $pdf->Cell(50, 7, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', substr($g->Nombre . ' ' . $g->Apellido, 0, 22)), 1, 0, 'L', $fill);
            
            if($g->Nivel == 'Elite') $pdf->SetFillColor(220, 53, 69);
            elseif($g->Nivel == 'Avanzado') $pdf->SetFillColor(255, 193, 7);
            elseif($g->Nivel == 'Intermedio') $pdf->SetFillColor(23, 162, 184);
            else $pdf->SetFillColor(108, 117, 125);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetFont('Arial', 'B', 7);
            $pdf->Cell(22, 7, $g->Nivel, 1, 0, 'C', true);
            $pdf->SetFont('Arial', '', 7);
            $pdf->SetTextColor(0, 0, 0);
            
            $pdf->SetFillColor(248, 249, 250);
            $pdf->Cell(18, 7, $g->total_sesiones, 1, 0, 'C', $fill);
            $pdf->Cell(25, 7, $g->sesiones_completadas, 1, 0, 'C', $fill);
            
            $pdf->SetFillColor(23, 162, 184);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetFont('Arial', 'B', 7);
            $pdf->Cell(25, 7, $g->tiempo_total_horas . 'h', 1, 0, 'C', true);
            $pdf->SetFont('Arial', '', 7);
            $pdf->SetTextColor(0, 0, 0);
            
            $pdf->SetFillColor(248, 249, 250);
            $entrenadores = $g->entrenadores ? iconv('UTF-8', 'ISO-8859-1//TRANSLIT', substr($g->entrenadores, 0, 22)) : 'N/A';
            $pdf->Cell(50, 7, $entrenadores, 1, 1, 'L', $fill);
            $i++;
        }
        
        $pdf->Ln(10);
        
        $pdf->SetDrawColor(220, 53, 69);
        $pdf->SetLineWidth(0.8);
        $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
        $pdf->SetLineWidth(0.2);
        $pdf->Ln(2);
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->SetTextColor(220, 53, 69);
        $pdf->Cell(10, 8, chr(149), 0, 0);
        $pdf->Cell(0, 8, 'LESIONES ACTIVAS', 0, 1);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln(3);
        
        if(count($datos['lesiones_activas']) > 0) {
            $pdf->SetFillColor(220, 53, 69);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->SetDrawColor(220, 53, 69);
            $pdf->Cell(50, 8, 'GIMNASTA', 1, 0, 'C', true);
            $pdf->Cell(38, 8, 'TIPO', 1, 0, 'C', true);
            $pdf->Cell(42, 8, 'PARTE AFECTADA', 1, 0, 'C', true);
            $pdf->Cell(30, 8, 'GRAVEDAD', 1, 0, 'C', true);
            $pdf->Cell(30, 8, 'ESTADO', 1, 1, 'C', true);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetDrawColor(200, 200, 200);
            
            $pdf->SetFont('Arial', '', 7);
            $i = 0;
            foreach($datos['lesiones_activas'] as $l) {
                $fill = $i % 2 == 0;
                $pdf->SetFillColor(248, 249, 250);
                $pdf->Cell(50, 7, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $l->Nombre . ' ' . $l->Apellido), 1, 0, 'L', $fill);
                $pdf->Cell(38, 7, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $l->Tipo), 1, 0, 'L', $fill);
                $pdf->Cell(42, 7, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $l->ParteCuerpo), 1, 0, 'L', $fill);
                
                if($l->Gravedad == 'Grave') $pdf->SetFillColor(220, 53, 69);
                elseif($l->Gravedad == 'Moderada') $pdf->SetFillColor(255, 193, 7);
                else $pdf->SetFillColor(23, 162, 184);
                $pdf->SetTextColor(255, 255, 255);
                $pdf->SetFont('Arial', 'B', 7);
                $pdf->Cell(30, 7, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $l->Gravedad), 1, 0, 'C', true);
                $pdf->SetFont('Arial', '', 7);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->SetFillColor(248, 249, 250);
                $pdf->Cell(30, 7, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $l->Estado), 1, 1, 'C', $fill);
                $i++;
            }
        } else {
            $pdf->SetFillColor(212, 237, 218);
            $pdf->SetTextColor(21, 87, 36);
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(0, 10, chr(252) . ' No hay lesiones activas', 1, 1, 'C', true);
            $pdf->SetTextColor(0, 0, 0);
        }
        
        $pdf->Ln(10);
        
        $pdf->SetDrawColor(23, 162, 184);
        $pdf->SetLineWidth(0.8);
        $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
        $pdf->SetLineWidth(0.2);
        $pdf->Ln(2);
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->SetTextColor(23, 162, 184);
        $pdf->Cell(10, 8, chr(149), 0, 0);
        $pdf->Cell(0, 8, 'RENDIMIENTO POR NIVEL', 0, 1);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln(3);
        
        $pdf->SetFillColor(23, 162, 184);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetDrawColor(23, 162, 184);
        $pdf->Cell(63, 8, 'NIVEL', 1, 0, 'C', true);
        $pdf->Cell(63, 8, 'TOTAL SESIONES', 1, 0, 'C', true);
        $pdf->Cell(64, 8, 'TASA DE EXITO', 1, 1, 'C', true);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetDrawColor(200, 200, 200);
        
        $pdf->SetFont('Arial', '', 9);
        $i = 0;
        foreach($datos['rendimiento_nivel'] as $n) {
            $fill = $i % 2 == 0;
            $porc = round($n->porcentaje_completadas);
            
            if($n->Nivel == 'Elite') $pdf->SetFillColor(220, 53, 69);
            elseif($n->Nivel == 'Avanzado') $pdf->SetFillColor(255, 193, 7);
            elseif($n->Nivel == 'Intermedio') $pdf->SetFillColor(23, 162, 184);
            else $pdf->SetFillColor(108, 117, 125);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(63, 8, $n->Nivel, 1, 0, 'C', true);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont('Arial', '', 9);
            
            $pdf->SetFillColor(248, 249, 250);
            $pdf->Cell(63, 8, $n->total_sesiones, 1, 0, 'C', $fill);
            
            if($porc >= 80) $pdf->SetFillColor(40, 167, 69);
            elseif($porc >= 50) $pdf->SetFillColor(255, 193, 7);
            else $pdf->SetFillColor(220, 53, 69);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(64, 8, $porc . '%', 1, 1, 'C', true);
            $pdf->SetTextColor(0, 0, 0);
            $i++;
        }
    }
    
    private function generarPdfGimnastas($pdf, $datos)
    {
        $pdf->SetFillColor(40, 167, 69);
        $pdf->Rect(0, 0, 210, 35, 'F');
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('Arial', 'B', 22);
        $pdf->SetY(12);
        $pdf->Cell(0, 10, 'REPORTE DE GIMNASTAS', 0, 1, 'C');
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(0, 5, 'Sistema de Gestion Deportiva - Gimnasia', 0, 1, 'C');
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->SetTextColor(230, 230, 230);
        $usuarioId = session('usuario_id');
        $usuario = $usuarioId ? Usuario::find($usuarioId)->Nombre . ' ' . Usuario::find($usuarioId)->Apellido : 'Sistema';
        $pdf->Cell(0, 4, 'Generado: ' . now()->format('d/m/Y H:i:s') . ' | Por: ' . iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $usuario), 0, 1, 'C');
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln(10);
        
        $pdf->SetDrawColor(40, 167, 69);
        $pdf->SetLineWidth(0.8);
        $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
        $pdf->SetLineWidth(0.2);
        $pdf->Ln(2);
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->SetTextColor(40, 167, 69);
        $pdf->Cell(10, 8, chr(149), 0, 0);
        $pdf->Cell(0, 8, 'DATOS DE GIMNASTAS', 0, 1);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln(3);
        
        $pdf->SetFillColor(40, 167, 69);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetDrawColor(40, 167, 69);
        $pdf->Cell(70, 8, 'NOMBRE COMPLETO', 1, 0, 'C', true);
        $pdf->Cell(30, 8, 'NIVEL', 1, 0, 'C', true);
        $pdf->Cell(25, 8, 'PESO (kg)', 1, 0, 'C', true);
        $pdf->Cell(25, 8, 'ALTURA (m)', 1, 0, 'C', true);
        $pdf->Cell(40, 8, 'LESIONES', 1, 1, 'C', true);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetDrawColor(200, 200, 200);
        
        $pdf->SetFont('Arial', '', 8);
        $i = 0;
        $totalGimnastas = 0;
        $totalLesiones = 0;
        
        foreach($datos as $g) {
            $fill = $i % 2 == 0;
            $pdf->SetFillColor(248, 249, 250);
            $pdf->Cell(70, 7, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $g->Nombre . ' ' . $g->Apellido), 1, 0, 'L', $fill);
            
            if($g->Nivel == 'Elite') $pdf->SetFillColor(220, 53, 69);
            elseif($g->Nivel == 'Avanzado') $pdf->SetFillColor(255, 193, 7);
            elseif($g->Nivel == 'Intermedio') $pdf->SetFillColor(23, 162, 184);
            else $pdf->SetFillColor(108, 117, 125);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->Cell(30, 7, $g->Nivel, 1, 0, 'C', true);
            $pdf->SetFont('Arial', '', 8);
            $pdf->SetTextColor(0, 0, 0);
            
            $pdf->SetFillColor(248, 249, 250);
            $pdf->Cell(25, 7, $g->Peso, 1, 0, 'C', $fill);
            $pdf->Cell(25, 7, $g->Altura, 1, 0, 'C', $fill);
            
            if($g->lesiones_activas > 0) {
                $pdf->SetFillColor(220, 53, 69);
                $pdf->SetTextColor(255, 255, 255);
                $pdf->SetFont('Arial', 'B', 8);
            } else {
                $pdf->SetFillColor(40, 167, 69);
                $pdf->SetTextColor(255, 255, 255);
                $pdf->SetFont('Arial', 'B', 8);
            }
            $pdf->Cell(40, 7, $g->lesiones_activas, 1, 1, 'C', true);
            $pdf->SetFont('Arial', '', 8);
            $pdf->SetTextColor(0, 0, 0);
            
            $totalGimnastas++;
            $totalLesiones += $g->lesiones_activas;
            $i++;
        }
        
        $pdf->Ln(10);
        $pdf->SetDrawColor(23, 162, 184);
        $pdf->SetLineWidth(0.8);
        $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
        $pdf->SetLineWidth(0.2);
        $pdf->Ln(2);
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->SetTextColor(23, 162, 184);
        $pdf->Cell(10, 8, chr(149), 0, 0);
        $pdf->Cell(0, 8, 'RESUMEN GENERAL', 0, 1);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln(3);
        
        $pdf->SetFillColor(23, 162, 184);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(95, 8, 'METRICA', 1, 0, 'C', true);
        $pdf->Cell(95, 8, 'VALOR', 1, 1, 'C', true);
        $pdf->SetTextColor(0, 0, 0);
        
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(95, 7, 'Total de Gimnastas', 1, 0, 'L');
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(95, 7, $totalGimnastas, 1, 1, 'C');
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(95, 7, 'Total de Lesiones Activas', 1, 0, 'L');
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(95, 7, $totalLesiones, 1, 1, 'C');
    }
    
    private function generarPdfSesiones($pdf, $datos)
    {
        $pdf->SetFillColor(255, 193, 7);
        $pdf->Rect(0, 0, 210, 35, 'F');
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', 'B', 22);
        $pdf->SetY(12);
        $pdf->Cell(0, 10, 'REPORTE DE SESIONES', 0, 1, 'C');
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(0, 5, 'Sistema de Gestion Deportiva - Gimnasia', 0, 1, 'C');
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->SetTextColor(100, 100, 100);
        $usuarioId = session('usuario_id');
        $usuario = $usuarioId ? Usuario::find($usuarioId)->Nombre . ' ' . Usuario::find($usuarioId)->Apellido : 'Sistema';
        $pdf->Cell(0, 4, 'Generado: ' . now()->format('d/m/Y H:i:s') . ' | Por: ' . iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $usuario), 0, 1, 'C');
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln(10);
        
        $pdf->SetDrawColor(255, 193, 7);
        $pdf->SetLineWidth(0.8);
        $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
        $pdf->SetLineWidth(0.2);
        $pdf->Ln(2);
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->SetTextColor(255, 193, 7);
        $pdf->Cell(10, 8, chr(149), 0, 0);
        $pdf->Cell(0, 8, 'HISTORIAL DE SESIONES', 0, 1);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln(3);
        
        $pdf->SetFillColor(255, 193, 7);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetDrawColor(255, 193, 7);
        $pdf->Cell(25, 8, 'FECHA', 1, 0, 'C', true);
        $pdf->Cell(25, 8, 'HORARIO', 1, 0, 'C', true);
        $pdf->Cell(45, 8, 'GIMNASTA', 1, 0, 'C', true);
        $pdf->Cell(45, 8, 'ENTRENADOR', 1, 0, 'C', true);
        $pdf->Cell(25, 8, 'APARATO', 1, 0, 'C', true);
        $pdf->Cell(25, 8, 'ESTADO', 1, 1, 'C', true);
        $pdf->SetDrawColor(200, 200, 200);
        
        $pdf->SetFont('Arial', '', 8);
        $i = 0;
        $programadas = 0;
        $completadas = 0;
        $canceladas = 0;
        
        foreach($datos as $s) {
            $fill = $i % 2 == 0;
            $pdf->SetFillColor(248, 249, 250);
            $pdf->Cell(25, 7, \Carbon\Carbon::parse($s->Fecha)->format('d/m/Y'), 1, 0, 'C', $fill);
            $horario = substr($s->Hora_Inicio, 0, 5) . '-' . substr($s->Hora_Fin, 0, 5);
            $pdf->Cell(25, 7, $horario, 1, 0, 'C', $fill);
            $pdf->Cell(45, 7, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', substr($s->gimnasta, 0, 24)), 1, 0, 'L', $fill);
            $pdf->Cell(45, 7, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', substr($s->entrenador, 0, 24)), 1, 0, 'L', $fill);
            $pdf->Cell(25, 7, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', substr($s->aparato, 0, 14)), 1, 0, 'C', $fill);
            
            if($s->Estado == 'Completada') $pdf->SetFillColor(40, 167, 69);
            elseif($s->Estado == 'Programada') $pdf->SetFillColor(23, 162, 184);
            else $pdf->SetFillColor(220, 53, 69);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->Cell(25, 7, $s->Estado, 1, 1, 'C', true);
            $pdf->SetFont('Arial', '', 8);
            $pdf->SetTextColor(0, 0, 0);
            
            if($s->Estado == 'Programada') $programadas++;
            if($s->Estado == 'Completada') $completadas++;
            if($s->Estado == 'Cancelada') $canceladas++;
            $i++;
        }
        
        $pdf->Ln(10);
        $pdf->SetDrawColor(23, 162, 184);
        $pdf->SetLineWidth(0.8);
        $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
        $pdf->SetLineWidth(0.2);
        $pdf->Ln(2);
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->SetTextColor(23, 162, 184);
        $pdf->Cell(10, 8, chr(149), 0, 0);
        $pdf->Cell(0, 8, 'RESUMEN ESTADISTICO', 0, 1);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln(3);
        
        $pdf->SetFillColor(23, 162, 184);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->SetDrawColor(23, 162, 184);
        $pdf->Cell(63, 9, 'ESTADO', 1, 0, 'C', true);
        $pdf->Cell(63, 9, 'CANTIDAD', 1, 0, 'C', true);
        $pdf->Cell(64, 9, 'PORCENTAJE', 1, 1, 'C', true);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetDrawColor(200, 200, 200);
        
        $pdf->SetFont('Arial', '', 10);
        $total = count($datos);
        $pdf->Cell(63, 7, 'Total de Sesiones', 1, 0, 'L');
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(63, 7, $total, 1, 0, 'C');
        $pdf->SetFillColor(108, 117, 125);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(64, 7, '100%', 1, 1, 'C', true);
        $pdf->SetTextColor(0, 0, 0);
        
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(63, 7, 'Sesiones Programadas', 1, 0, 'L');
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(63, 7, $programadas, 1, 0, 'C');
        $porcProg = $total > 0 ? round(($programadas/$total)*100, 1) : 0;
        $pdf->SetFillColor(23, 162, 184);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(64, 7, $porcProg . '%', 1, 1, 'C', true);
        $pdf->SetTextColor(0, 0, 0);
        
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(63, 7, 'Sesiones Completadas', 1, 0, 'L');
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(63, 7, $completadas, 1, 0, 'C');
        $porcComp = $total > 0 ? round(($completadas/$total)*100, 1) : 0;
        $pdf->SetFillColor(40, 167, 69);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(64, 7, $porcComp . '%', 1, 1, 'C', true);
        $pdf->SetTextColor(0, 0, 0);
        
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(63, 7, 'Sesiones Canceladas', 1, 0, 'L');
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(63, 7, $canceladas, 1, 0, 'C');
        $porcCanc = $total > 0 ? round(($canceladas/$total)*100, 1) : 0;
        $pdf->SetFillColor(220, 53, 69);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(64, 7, $porcCanc . '%', 1, 1, 'C', true);
        $pdf->SetTextColor(0, 0, 0);   
    }
    
    private function generarPdfEstadisticas($pdf, $datos)
    {
        $pdf->SetFillColor(13, 110, 253);
        $pdf->Rect(0, 0, 210, 35, 'F');
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('Arial', 'B', 22);
        $pdf->SetY(12);
        $pdf->Cell(0, 10, 'REPORTE ESTADISTICO', 0, 1, 'C');
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(0, 5, 'Sistema de Gestion Deportiva - Gimnasia', 0, 1, 'C');
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->SetTextColor(230, 230, 230);
        $usuarioId = session('usuario_id');
        $usuario = $usuarioId ? Usuario::find($usuarioId)->Nombre . ' ' . Usuario::find($usuarioId)->Apellido : 'Sistema';
        $pdf->Cell(0, 4, 'Generado: ' . now()->format('d/m/Y H:i:s') . ' | Por: ' . iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $usuario), 0, 1, 'C');
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln(10);
        
        // USUARIOS
        $pdf->SetDrawColor(23, 162, 184);
        $pdf->SetLineWidth(0.8);
        $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
        $pdf->SetLineWidth(0.2);
        $pdf->Ln(2);
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->SetTextColor(23, 162, 184);
        $pdf->Cell(10, 8, chr(149), 0, 0);
        $pdf->Cell(0, 8, 'USUARIOS DEL SISTEMA', 0, 1);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln(3);
        
        $pdf->SetFillColor(23, 162, 184);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetDrawColor(23, 162, 184);
        $pdf->Cell(95, 8, 'METRICA', 1, 0, 'L', true);
        $pdf->Cell(95, 8, 'VALOR', 1, 1, 'C', true);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetDrawColor(200, 200, 200);
        
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(95, 7, 'Total de Usuarios', 1);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetFillColor(23, 162, 184);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(95, 7, $datos['usuarios']['total'], 1, 1, 'C', true);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(95, 7, 'Usuarios Activos', 1);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetFillColor(40, 167, 69);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(95, 7, $datos['usuarios']['activos'], 1, 1, 'C', true);
        $pdf->SetTextColor(0, 0, 0);
        
        $pdf->Ln(5);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(0, 7, 'Distribucion por Rol:', 0, 1);
        $pdf->SetFillColor(108, 117, 125);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(95, 7, 'ROL', 1, 0, 'C', true);
        $pdf->Cell(95, 7, 'CANTIDAD', 1, 1, 'C', true);
        $pdf->SetTextColor(0, 0, 0);
        
        $pdf->SetFont('Arial', '', 9);
        $i = 0;
        foreach($datos['usuarios']['por_rol'] as $rol => $cant) {
            $fill = $i % 2 == 0;
            $pdf->SetFillColor(248, 249, 250);
            $pdf->Cell(95, 6, $rol, 1, 0, 'L', $fill);
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(95, 6, $cant, 1, 1, 'C', $fill);
            $pdf->SetFont('Arial', '', 9);
            $i++;
        }
        
        $pdf->Ln(8);
        
        // NIVELES
        $pdf->SetFillColor(40, 167, 69);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('Arial', 'B', 13);
        $pdf->Cell(0, 9, 'NIVELES DE GIMNASTAS', 0, 1, 'L', true);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln(2);
        
        $pdf->SetFillColor(220, 220, 220);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(95, 7, 'Nivel', 1, 0, 'C', true);
        $pdf->Cell(95, 7, 'Cantidad', 1, 1, 'C', true);
        
        $pdf->SetFont('Arial', '', 9);
        $fill = false;
        foreach($datos['niveles']['por_nivel'] as $nivel => $cant) {
            $pdf->SetFillColor(245, 245, 245);
            $pdf->Cell(95, 6, $nivel, 1, 0, 'L', $fill);
            $pdf->Cell(95, 6, $cant, 1, 1, 'C', $fill);
            $fill = !$fill;
        }
        
        $pdf->Ln(3);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(0, 7, 'Solicitudes de Cambio de Nivel:', 0, 1);
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(95, 6, 'Pendientes', 1);
        $pdf->Cell(95, 6, $datos['niveles']['solicitudes_pendientes'], 1, 1, 'C');
        $pdf->Cell(95, 6, 'Aprobadas', 1);
        $pdf->Cell(95, 6, $datos['niveles']['solicitudes_aprobadas'], 1, 1, 'C');
        $pdf->Cell(95, 6, 'Rechazadas', 1);
        $pdf->Cell(95, 6, $datos['niveles']['solicitudes_rechazadas'], 1, 1, 'C');
        
        $pdf->AddPage();
        
        // SESIONES
        $pdf->SetFillColor(255, 193, 7);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', 'B', 13);
        $pdf->Cell(0, 9, 'SESIONES DE ENTRENAMIENTO', 0, 1, 'L', true);
        $pdf->Ln(2);
        
        $pdf->SetFillColor(220, 220, 220);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(95, 7, 'Estado', 1, 0, 'C', true);
        $pdf->Cell(95, 7, 'Cantidad', 1, 1, 'C', true);
        
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(95, 6, 'Total de Sesiones', 1);
        $pdf->Cell(95, 6, $datos['sesiones']['total'], 1, 1, 'C');
        $pdf->Cell(95, 6, 'Sesiones Programadas', 1);
        $pdf->Cell(95, 6, $datos['sesiones']['programadas'], 1, 1, 'C');
        $pdf->Cell(95, 6, 'Sesiones Completadas', 1);
        $pdf->Cell(95, 6, $datos['sesiones']['completadas'], 1, 1, 'C');
        $pdf->Cell(95, 6, 'Sesiones Canceladas', 1);
        $pdf->Cell(95, 6, $datos['sesiones']['canceladas'], 1, 1, 'C');
        $pdf->Cell(95, 6, 'Sesiones Este Mes', 1);
        $pdf->Cell(95, 6, $datos['sesiones']['este_mes'], 1, 1, 'C');
        
        $pdf->Ln(8);
        
        // POSTULACIONES
        $pdf->SetFillColor(108, 117, 125);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('Arial', 'B', 13);
        $pdf->Cell(0, 9, 'POSTULACIONES', 0, 1, 'L', true);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln(2);
        
        $pdf->SetFillColor(220, 220, 220);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(95, 7, 'Estado', 1, 0, 'C', true);
        $pdf->Cell(95, 7, 'Cantidad', 1, 1, 'C', true);
        
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(95, 6, 'Total de Postulaciones', 1);
        $pdf->Cell(95, 6, $datos['postulaciones']['total'], 1, 1, 'C');
        $pdf->Cell(95, 6, 'Postulaciones Pendientes', 1);
        $pdf->Cell(95, 6, $datos['postulaciones']['pendientes'], 1, 1, 'C');
        $pdf->Cell(95, 6, 'Postulaciones Aprobadas', 1);
        $pdf->Cell(95, 6, $datos['postulaciones']['aprobadas'], 1, 1, 'C');
        $pdf->Cell(95, 6, 'Postulaciones Rechazadas', 1);
        $pdf->Cell(95, 6, $datos['postulaciones']['rechazadas'], 1, 1, 'C');
        
        $pdf->Ln(8);
        
        // LESIONES
        $pdf->SetFillColor(220, 53, 69);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('Arial', 'B', 13);
        $pdf->Cell(0, 9, 'LESIONES', 0, 1, 'L', true);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln(2);
        
        $pdf->SetFillColor(220, 220, 220);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(95, 7, 'Metrica', 1, 0, 'C', true);
        $pdf->Cell(95, 7, 'Cantidad', 1, 1, 'C', true);
        
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(95, 6, 'Total de Lesiones', 1);
        $pdf->Cell(95, 6, $datos['lesiones']['total'], 1, 1, 'C');
        $pdf->Cell(95, 6, 'Lesiones Activas', 1);
        $pdf->Cell(95, 6, $datos['lesiones']['activas'], 1, 1, 'C');
        
        $pdf->Ln(3);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(0, 7, 'Distribucion por Tipo:', 0, 1);
        $pdf->SetFillColor(220, 220, 220);
        $pdf->Cell(95, 7, 'Tipo de Lesion', 1, 0, 'C', true);
        $pdf->Cell(95, 7, 'Cantidad', 1, 1, 'C', true);
        
        $pdf->SetFont('Arial', '', 9);
        $fill = false;
        foreach($datos['lesiones']['por_tipo'] as $tipo => $cant) {
            $pdf->SetFillColor(245, 245, 245);
            $pdf->Cell(95, 6, utf8_decode($tipo), 1, 0, 'L', $fill);
            $pdf->Cell(95, 6, $cant, 1, 1, 'C', $fill);
            $fill = !$fill;
        }
    }

    private function generarHtmlCompleto($datos)
    {
        $html = '<h2>Gimnastas y Sesiones</h2><table border="1" cellpadding="5"><tr><th>Nombre</th><th>Nivel</th><th>Total Sesiones</th><th>Completadas</th></tr>';
        foreach($datos['gimnastas_sesiones'] as $g) {
            $html .= "<tr><td>{$g->Nombre} {$g->Apellido}</td><td>{$g->Nivel}</td><td>{$g->total_sesiones}</td><td>{$g->sesiones_completadas}</td></tr>";
        }
        $html .= '</table><br><h2>Lesiones Activas</h2><table border="1" cellpadding="5"><tr><th>Gimnasta</th><th>Tipo</th><th>Parte</th><th>Gravedad</th></tr>';
        foreach($datos['lesiones_activas'] as $l) {
            $html .= "<tr><td>{$l->Nombre} {$l->Apellido}</td><td>{$l->Tipo}</td><td>{$l->ParteCuerpo}</td><td>{$l->Gravedad}</td></tr>";
        }
        $html .= '</table>';
        return $html;
    }

    private function generarHtmlGimnastas($datos)
    {
        $html = '<table border="1" cellpadding="5"><tr><th>Nombre</th><th>Nivel</th><th>Peso</th><th>Altura</th><th>Lesiones Activas</th></tr>';
        foreach($datos as $g) {
            $html .= "<tr><td>{$g->Nombre} {$g->Apellido}</td><td>{$g->Nivel}</td><td>{$g->Peso}kg</td><td>{$g->Altura}m</td><td>{$g->lesiones_activas}</td></tr>";
        }
        $html .= '</table>';
        return $html;
    }

    private function generarHtmlSesiones($datos)
    {
        $html = '<table border="1" cellpadding="5"><tr><th>Fecha</th><th>Gimnasta</th><th>Entrenador</th><th>Aparato</th><th>Estado</th></tr>';
        foreach($datos as $s) {
            $html .= "<tr><td>{$s->Fecha}</td><td>{$s->gimnasta}</td><td>{$s->entrenador}</td><td>{$s->aparato}</td><td>{$s->Estado}</td></tr>";
        }
        $html .= '</table>';
        return $html;
    }

    private function generarPdf($titulo, $contenido)
    {
        $html = "<!DOCTYPE html><html><head><meta charset='UTF-8'><style>body{font-family:Arial;} table{width:100%;border-collapse:collapse;} th{background:#007bff;color:white;} td,th{padding:8px;text-align:left;}</style></head><body><h1>{$titulo}</h1><p>Fecha: " . now()->format('d/m/Y H:i') . "</p>{$contenido}</body></html>";
        
        $filename = 'reporte_' . now()->format('Y-m-d_His') . '.html';
        
        return Response::make($html, 200, [
            'Content-Type' => 'text/html',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ]);
    }
}
