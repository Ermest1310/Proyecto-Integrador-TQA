<?php

namespace App\Http\Controllers;

use App\Models\Lesion;
use App\Models\PlanRehabilitacion;
use App\Models\PlanFaseSeguimiento;
use App\Models\Usuario;
use App\Services\RehabilitacionAStarService;
use App\Services\PermissionService;
use Illuminate\Http\Request;

class RehabilitacionController extends Controller
{
    public function index()
    {
        PermissionService::abort('lesiones.view');

        $lesiones = Lesion::with(['gimnasta', 'medico'])
            ->whereIn('Estado', ['Lesionado', 'En Rehabilitación'])
            ->orderBy('FechaDiagnostico', 'desc')
            ->get();

        $planes = PlanRehabilitacion::with(['lesion', 'gimnasta', 'medico'])
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return view('medico.rehabilitacion.index', compact('lesiones', 'planes'));
    }

    public function crearPlan(Request $request)
    {
        PermissionService::abort('lesiones.edit');

        $request->validate([
            'lesion_id' => 'required|exists:lesiones,ID_Lesion',
            'fecha_inicio' => 'required|date|after_or_equal:today',
            'guardar' => 'nullable|boolean',
        ]);

        try {
            $servicio = new RehabilitacionAStarService();
            $resultado = $servicio->planificar(
                $request->input('lesion_id'),
                PermissionService::isAdmin() ? null : (int) session('usuario_id'),
                $request->input('fecha_inicio')
            );

            if ($request->input('guardar', false) && $resultado['success']) {
                return redirect()
                    ->route('medico.rehabilitacion.verPlan', ['id' => $resultado['plan_guardado']['plan_id']])
                    ->with('success', 'Plan de rehabilitación generado y guardado correctamente');
            }

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json($resultado);
            }

            return view('medico.rehabilitacion.plan-generado', compact('resultado'));

        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al generar el plan: ' . $e->getMessage()]);
        }
    }

    public function verPlan(int $id)
    {
        PermissionService::abort('lesiones.view');

        $plan = PlanRehabilitacion::with([
            'lesion',
            'gimnasta',
            'medico',
            'seguimientos' => function ($q) {
                $q->orderBy('Orden');
            },
            'seguimientos.fase',
        ])->findOrFail($id);

        return view('medico.rehabilitacion.plan-detalle', compact('plan'));
    }

    public function actualizarFase(Request $request, int $seguimientoId)
    {
        PermissionService::abort('lesiones.edit');

        $request->validate([
            'estado' => 'required|in:Pendiente,En Curso,Completada,Omitida',
            'observaciones' => 'nullable|string|max:1000',
        ]);

        $seguimiento = PlanFaseSeguimiento::with(['plan'])->findOrFail($seguimientoId);

        $esAdmin = PermissionService::isAdmin();
        $esMedicoResponsable = $seguimiento->plan->ID_Medico === session('usuario_id');

        if (!$esAdmin && !$esMedicoResponsable) {
            abort(403, 'Solo el médico responsable puede actualizar este plan');
        }

        $seguimiento->update([
            'Estado' => $request->input('estado'),
            'Observaciones' => $request->input('observaciones'),
        ]);

        if ($request->input('estado') === 'Completada' && !$seguimiento->Fecha_Inicio) {
            $seguimiento->update(['Fecha_Inicio' => now()->subDays(7)]);
            $seguimiento->update(['Fecha_Fin' => now()]);
        }

        $todasCompletadas = $seguimiento->plan->seguimientos()
            ->where('Estado', '!=', 'Completada')
            ->where('Estado', '!=', 'Omitida')
            ->count() === 0;

        if ($todasCompletadas) {
            $seguimiento->plan->update(['Estado' => 'Completado']);
            $seguimiento->plan->lesion->update(['Estado' => 'Recuperada']);
        }

        return response()->json([
            'success' => true,
            'mensaje' => 'Fase actualizada correctamente',
        ]);
    }

    public function listarPlanes(Request $request)
    {
        PermissionService::abort('lesiones.view');

        $query = PlanRehabilitacion::with(['lesion', 'gimnasta', 'medico']);

        if ($request->filled('estado')) {
            $query->where('Estado', $request->input('estado'));
        }

        if ($request->filled('gimnasta_id')) {
            $query->where('ID_Gimnasta', $request->input('gimnasta_id'));
        }

        if (!PermissionService::isAdmin()) {
            $query->where('ID_Medico', session('usuario_id'));
        }

        $planes = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('medico.rehabilitacion.planes', compact('planes'));
    }

    public function cancelarPlan(int $id)
    {
        PermissionService::abort('lesiones.edit');

        $plan = PlanRehabilitacion::with(['lesion'])->findOrFail($id);

        $esAdmin = PermissionService::isAdmin();
        $esMedicoResponsable = $plan->ID_Medico === session('usuario_id');

        if (!$esAdmin && !$esMedicoResponsable) {
            abort(403, 'No tienes permisos para cancelar este plan');
        }

        $plan->update(['Estado' => 'Cancelado']);

        if ($plan->lesion->Estado === 'En Rehabilitación') {
            $plan->lesion->update(['Estado' => 'Lesionado']);
        }

        return response()->json([
            'success' => true,
            'mensaje' => 'Plan cancelado correctamente',
        ]);
    }
}
