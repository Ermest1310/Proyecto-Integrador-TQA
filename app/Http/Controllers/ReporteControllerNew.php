<?php

namespace App\Http\Controllers;

use App\Services\PdfService;
use Illuminate\Http\Request;
require_once(base_path('fpdf186/fpdf.php'));

class ReporteControllerNew extends Controller
{
    private function generarPdfCompleto($pdf, $datos)
    {
        // Header con diseño moderno
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
        $pdf->Cell(0, 4, 'Generado: ' . now()->format('d/m/Y H:i:s'), 0, 1, 'C');
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln(10);
        
        // Gimnastas y Rendimiento
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
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetDrawColor(40, 167, 69);
        $pdf->Cell(60, 8, 'GIMNASTA', 1, 0, 'C', true);
        $pdf->Cell(30, 8, 'NIVEL', 1, 0, 'C', true);
        $pdf->Cell(30, 8, 'TOTAL', 1, 0, 'C', true);
        $pdf->Cell(35, 8, 'COMPLETADAS', 1, 0, 'C', true);
        $pdf->Cell(35, 8, 'RENDIMIENTO', 1, 1, 'C', true);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetDrawColor(200, 200, 200);
        
        $pdf->SetFont('Arial', '', 8);
        $i = 0;
        foreach($datos['gimnastas_sesiones'] as $g) {
            $porc = $g->total_sesiones > 0 ? round(($g->sesiones_completadas / $g->total_sesiones) * 100) : 0;
            $fill = $i % 2 == 0;
            $pdf->SetFillColor(248, 249, 250);
            $pdf->Cell(60, 7, utf8_decode($g->Nombre . ' ' . $g->Apellido), 1, 0, 'L', $fill);
            
            if($g->Nivel == 'Elite') $pdf->SetFillColor(220, 53, 69);
            elseif($g->Nivel == 'Avanzado') $pdf->SetFillColor(255, 193, 7);
            elseif($g->Nivel == 'Intermedio') $pdf->SetFillColor(23, 162, 184);
            else $pdf->SetFillColor(108, 117, 125);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(30, 7, $g->Nivel, 1, 0, 'C', true);
            $pdf->SetTextColor(0, 0, 0);
            
            $pdf->SetFillColor(248, 249, 250);
            $pdf->Cell(30, 7, $g->total_sesiones, 1, 0, 'C', $fill);
            $pdf->Cell(35, 7, $g->sesiones_completadas, 1, 0, 'C', $fill);
            
            if($porc >= 80) $pdf->SetFillColor(40, 167, 69);
            elseif($porc >= 50) $pdf->SetFillColor(255, 193, 7);
            else $pdf->SetFillColor(220, 53, 69);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->Cell(35, 7, $porc . '%', 1, 1, 'C', true);
            $pdf->SetFont('Arial', '', 8);
            $pdf->SetTextColor(0, 0, 0);
            $i++;
        }
        
        $pdf->Ln(10);
        
        // Lesiones Activas
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
                $pdf->Cell(50, 7, utf8_decode($l->Nombre . ' ' . $l->Apellido), 1, 0, 'L', $fill);
                $pdf->Cell(38, 7, utf8_decode($l->Tipo), 1, 0, 'L', $fill);
                $pdf->Cell(42, 7, utf8_decode($l->ParteCuerpo), 1, 0, 'L', $fill);
                
                if($l->Gravedad == 'Grave') $pdf->SetFillColor(220, 53, 69);
                elseif($l->Gravedad == 'Moderada') $pdf->SetFillColor(255, 193, 7);
                else $pdf->SetFillColor(23, 162, 184);
                $pdf->SetTextColor(255, 255, 255);
                $pdf->SetFont('Arial', 'B', 7);
                $pdf->Cell(30, 7, $l->Gravedad, 1, 0, 'C', true);
                $pdf->SetFont('Arial', '', 7);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->SetFillColor(248, 249, 250);
                $pdf->Cell(30, 7, $l->Estado, 1, 1, 'C', $fill);
                $i++;
            }
        } else {
            $pdf->SetFillColor(212, 237, 218);
            $pdf->SetTextColor(21, 87, 36);
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(0, 10, chr(252) . ' No hay lesiones activas registradas', 1, 1, 'C', true);
            $pdf->SetTextColor(0, 0, 0);
        }
        
        $pdf->Ln(10);
        
        // Rendimiento por Nivel
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
        
        $pdf->SetY(-20);
        $pdf->SetFont('Arial', 'I', 7);
        $pdf->SetTextColor(128, 128, 128);
        $pdf->Cell(0, 5, 'Pagina ' . $pdf->PageNo(), 0, 1, 'C');
        $pdf->Cell(0, 3, 'Sistema de Gestion Deportiva - Confidencial', 0, 0, 'C');
    }
}
