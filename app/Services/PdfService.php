<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class PdfService
{
    public function generarReporteCompleto()
    {
        $gimnastasConSesiones = DB::table('usuarios as u')
            ->join('roles as r', 'u.ID_Rol', '=', 'r.ID_Rol')
            ->leftJoin('sesiones_entrenamiento as s', 'u.ID_Usuario', '=', 's.ID_Gimnasta')
            ->leftJoin('usuarios as e', 's.ID_Entrenador', '=', 'e.ID_Usuario')
            ->where('r.NombreRol', 'Gimnasta')
            ->select(
                'u.ID_Usuario',
                'u.Nombre',
                'u.Apellido',
                'u.Nivel',
                DB::raw('COUNT(s.ID_Sesion) as total_sesiones'),
                DB::raw('SUM(CASE WHEN s.Estado = "Completada" THEN 1 ELSE 0 END) as sesiones_completadas'),
                DB::raw('SUM(TIMESTAMPDIFF(MINUTE, s.Hora_Inicio, s.Hora_Fin)) as tiempo_total_minutos'),
                DB::raw('GROUP_CONCAT(DISTINCT CONCAT(e.Nombre, " ", e.Apellido) SEPARATOR ", ") as entrenadores')
            )
            ->groupBy('u.ID_Usuario', 'u.Nombre', 'u.Apellido', 'u.Nivel')
            ->get();

        $lesionesActivas = DB::table('lesiones as l')
            ->join('usuarios as u', 'l.ID_Gimnasta', '=', 'u.ID_Usuario')
            ->where('l.activo', true)
            ->select('u.Nombre', 'u.Apellido', 'l.Tipo', 'l.ParteCuerpo', 'l.Gravedad', 'l.Estado')
            ->get();

        $rendimientoPorNivel = DB::table('usuarios as u')
            ->join('sesiones_entrenamiento as s', 'u.ID_Usuario', '=', 's.ID_Gimnasta')
            ->where('u.Nivel', '!=', '')
            ->select(
                'u.Nivel',
                DB::raw('COUNT(s.ID_Sesion) as total_sesiones'),
                DB::raw('AVG(CASE WHEN s.Estado = "Completada" THEN 1 ELSE 0 END) * 100 as porcentaje_completadas')
            )
            ->groupBy('u.Nivel')
            ->get();

        $gimnastasConSesiones = $gimnastasConSesiones->map(function($g) {
            $g->tiempo_total_horas = $g->tiempo_total_minutos ? round($g->tiempo_total_minutos / 60, 1) : 0;
            return $g;
        });
        
        return [
            'gimnastas_sesiones' => $gimnastasConSesiones,
            'lesiones_activas' => $lesionesActivas,
            'rendimiento_nivel' => $rendimientoPorNivel,
            'fecha' => now()->format('d/m/Y H:i')
        ];
    }

    public function generarReporteGimnastas()
    {
        return DB::table('usuarios as u')
            ->join('roles as r', 'u.ID_Rol', '=', 'r.ID_Rol')
            ->leftJoin('lesiones as l', function($join) {
                $join->on('u.ID_Usuario', '=', 'l.ID_Gimnasta')
                     ->where('l.activo', '=', true);
            })
            ->where('r.NombreRol', 'Gimnasta')
            ->select(
                'u.Nombre',
                'u.Apellido',
                'u.Nivel',
                'u.Peso',
                'u.Altura',
                DB::raw('COUNT(l.ID_Lesion) as lesiones_activas')
            )
            ->groupBy('u.ID_Usuario', 'u.Nombre', 'u.Apellido', 'u.Nivel', 'u.Peso', 'u.Altura')
            ->get();
    }

    public function generarReporteSesiones()
    {
        return DB::table('sesiones_entrenamiento as s')
            ->join('usuarios as g', 's.ID_Gimnasta', '=', 'g.ID_Usuario')
            ->join('usuarios as e', 's.ID_Entrenador', '=', 'e.ID_Usuario')
            ->leftJoin('aparatos as a', 's.ID_Aparato', '=', 'a.ID_Aparato')
            ->select(
                's.Fecha',
                's.Hora_Inicio',
                's.Hora_Fin',
                DB::raw('CONCAT(g.Nombre, " ", g.Apellido) as gimnasta'),
                DB::raw('CONCAT(e.Nombre, " ", e.Apellido) as entrenador'),
                DB::raw('COALESCE(a.Nombre, "General") as aparato'),
                's.Estado'
            )
            ->orderBy('s.Fecha', 'desc')
            ->limit(50)
            ->get();
    }
}
