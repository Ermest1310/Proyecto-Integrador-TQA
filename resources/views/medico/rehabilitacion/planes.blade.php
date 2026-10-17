<?php
$badge = [
    'Pendiente' => 'badge-alpha-programada',
    'En Curso' => 'badge-alpha-curso',
    'Completado' => 'badge-alpha-completada',
    'Cancelado' => 'badge-alpha-cancelada',
];
?>

@extends('layouts.medico')

@section('title', 'Planes de Rehabilitación - GAM')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h3 class="fw-bold text-navy-title mb-1"><i class="fas fa-clipboard-list text-gold me-2"></i>Planes de Rehabilitación</h3>
            <p class="text-muted mb-0">Historial de planes generados con algoritmo A*</p>
        </div>
        <a href="{{ route('medico.rehabilitacion.index') }}" class="btn btn-alpha-action-primary"><i class="fas fa-plus-circle me-1"></i> Nuevo Plan</a>
    </div>

    <div class="alpha-glass-list-card p-4">
        <form method="GET" action="{{ route('medico.rehabilitacion.planes') }}" class="row mb-4">
            <div class="col-md-3">
                <select name="estado" class="form-control alpha-form-control">
                    <option value="">Todos los estados</option>
                    @foreach(['Pendiente','En Curso','Completado','Cancelado'] as $edo)
                        <option value="{{ $edo }}" @if(request('estado') === $edo) selected @endif>{{ $edo }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-alpha-action-secondary"><i class="fas fa-filter me-1"></i> Filtrar</button>
            </div>
        </form>

        @if($planes->isEmpty())
            <div class="alert alert-info border-0 rounded-3"><i class="fas fa-info-circle me-2"></i> No se encontraron planes con los criterios seleccionados.</div>
        @else
            <div class="table-responsive alpha-table-container">
                <table class="table alpha-table">
                    <thead>
                        <tr>
                            <th>ID</th><th>Gimnasta</th><th>Lesión</th><th>Estado</th><th>Duración</th><th>Riesgo</th><th>Creado</th><th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($planes as $plan)
                            <tr>
                                <td class="fw-bold">#{{ $plan->ID_Plan }}</td>
                                <td>{{ $plan->gimnasta->Nombre }} {{ $plan->gimnasta->Apellido }}</td>
                                <td>
                                    <strong>{{ $plan->lesion->Tipo }}</strong>
                                    <small class="d-block text-muted">{{ $plan->lesion->ParteCuerpo }}</small>
                                </td>
                                <td><span class="badge {{ $badge[$plan->Estado] }}">{{ $plan->Estado }}</span></td>
                                <td>{{ $plan->Duracion_Estimada_Dias }} días</td>
                                <td><span class="badge badge-warning">{{ number_format($plan->Riesgo_Recaída_Total * 100, 1) }}%</span></td>
                                <td>{{ $plan->created_at->format('d/m/Y') }}</td>
                                <td class="text-end">
                                    <a href="{{ route('medico.rehabilitacion.verPlan', $plan->ID_Plan) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                    @if($plan->Estado !== 'Cancelado' && $plan->Estado !== 'Completado')
                                        <button class="btn btn-sm btn-danger" onclick="cancelarPlan({{ $plan->ID_Plan }})"><i class="fas fa-times-circle"></i></button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $planes->appends(request()->query())->links() }}</div>
        @endif
    </div>
</div>

<form id="cancelForm" method="POST" style="display:none;">@csrf @method('DELETE')</form>

<script>
function cancelarPlan(id) {
    if (confirm('¿Está seguro de cancelar este plan de rehabilitación?')) {
        const form = document.getElementById('cancelForm');
        form.action = '{{ route("medico.rehabilitacion.cancelar", "") }}/' + id;
        form.submit();
    }
}
</script>
@php // Vista completa con filtros, paginación y acciones. @endphp
