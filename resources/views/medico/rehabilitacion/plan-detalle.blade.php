<?php
$badge = [
    'Pendiente' => 'badge-alpha-programada',
    'En Curso' => 'badge-alpha-curso',
    'Completada' => 'badge-alpha-completada',
    'Omitida' => 'badge-alpha-cancelada',
];
$contador = 0;
?>

@extends('layouts.medico')

@section('title', 'Detalle del Plan - GAM')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h3 class="fw-bold text-navy-title mb-1"><i class="fas fa-notes-medical text-gold me-2"></i>Plan de rehabilitación #{{ $plan->ID_Plan }}</h3>
            <p class="text-muted mb-0">Ruta clínica generada con algoritmo A*</p>
        </div>
        <a href="{{ route('medico.rehabilitacion.planes') }}" class="btn btn-alpha-action-secondary"><i class="fas fa-arrow-left me-1"></i> Volver</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 rounded-3 mb-4"><i class="fas fa-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-3 mb-3">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase small fw-bold">Gimnasta</h6>
                    <h5 class="fw-bold">{{ $plan->gimnasta->Nombre }} {{ $plan->gimnasta->Apellido }}</h5>
                    <p class="text-muted mb-1">Nivel: {{ $plan->gimnasta->Nivel }}</p>
                    <p class="text-muted mb-0">Médico: {{ $plan->medico->Nombre }} {{ $plan->medico->Apellido }}</p>
                </div>
            </div>
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase small fw-bold">Resumen del Plan</h6>
                    <div class="d-flex justify-content-between py-2 border-bottom"><span>Estado</span><span class="badge {{ $badge[$plan->Estado] }}">{{ $plan->Estado }}</span></div>
                    <div class="d-flex justify-content-between py-2 border-bottom"><span>Inicio</span><strong>{{ $plan->Fecha_Inicio->format('d/m/Y') }}</strong></div>
                    <div class="d-flex justify-content-between py-2 border-bottom"><span>Fin estimado</span><strong>{{ $plan->Fecha_Estimada_Fin ? $plan->Fecha_Estimada_Fin->format('d/m/Y') : '-' }}</strong></div>
                    <div class="d-flex justify-content-between py-2 border-bottom"><span>Duración total</span><strong>{{ $plan->Duracion_Estimada_Dias }} días</strong></div>
                    <div class="d-flex justify-content-between py-2"><span>Riesgo total</span><strong class="text-danger">{{ number_format($plan->Riesgo_Recaída_Total * 100, 1) }}%</strong></div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="alpha-glass-list-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0"><i class="fas fa-route text-gold me-2"></i>Progresión Clínica</h5>
                    <span class="text-muted small">{{ $plan->seguimientos->count() }} fases</span>
                </div>
                @if($plan->seguimientos->isEmpty())
                    <div class="alert alert-warning"><i class="fas fa-exclamation-triangle me-2"></i> No hay fases registradas en este plan.</div>
                @else
                    <div class="table-responsive alpha-table-container">
                        <table class="table alpha-table">
                            <thead><tr><th>#</th><th>Fase</th><th>Ventana</th><th>Riesgo</th><th>Estado</th><th class="text-end">Acción</th></tr></thead>
                            <tbody>
                                @foreach($plan->seguimientos as $seguimiento)
                                    <?php $contador++; ?>
                                    <tr>
                                        <td class="fw-bold">{{ $seguimiento->Orden }}</td>
                                        <td>
                                            <strong>{{ $seguimiento->fase->Nombre }}</strong>
                                            @if($seguimiento->fase->Descripcion)<small class="d-block text-muted">{{ $seguimiento->fase->Descripcion }}</small>@endif
                                        </td>
                                        <td>
                                            @if($seguimiento->Fecha_Inicio){{ $seguimiento->Fecha_Inicio->format('d/m/Y') }}@else-<endif
                                            @if($seguimiento->Fecha_Fin) <small class="d-block text-muted">hasta {{ $seguimiento->Fecha_Fin->format('d/m/Y') }}</small>@endif
                                        </td>
                                        <td><span class="badge badge-warning">{{ number_format($seguimiento->Riesgo_Recaída_Fase * 100, 1) }}%</span></td>
                                        <td><span class="badge {{ $badge[$seguimiento->Estado] }}">{{ $seguimiento->Estado }}</span></td>
                                        <td class="text-end">
                                            <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#faseModal{{ $seguimiento->ID_Seguimiento }}"><i class="fas fa-edit"></i></button>
                                            <div class="modal fade" id="faseModal{{ $seguimiento->ID_Seguimiento }}" tabindex="-1">
                                                <div class="modal-dialog"><div class="modal-content">
                                                    <div class="modal-header"><h5 class="modal-title">Actualizar fase</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
                                                    <form action="{{ route('medico.rehabilitacion.actualizarFase', $seguimiento->ID_Seguimiento) }}" method="POST">
                                                        @csrf @method('PUT')
                                                        <div class="modal-body">
                                                            <div class="form-group"><label>Estado</label>
                                                                <select name="estado" class="form-control alpha-form-control">
                                                                    @foreach(['Pendiente','En Curso','Completada','Omitida'] as $edo)
                                                                        <option value="{{ $edo }}" @if($seguimiento->Estado === $edo) selected @endif>{{ $edo }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="form-group mt-3"><label>Observaciones</label><textarea name="observaciones" class="form-control" rows="3">{{ old('observaciones', $seguimiento->Observaciones) }}</textarea></div>
                                                        </div>
                                                        <div class="modal-footer"><button type="submit" class="btn btn-primary">Guardar</button><button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button></div>
                                                    </form>
                                                </div></div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
            @if($plan->Observaciones)
                <div class="card shadow-sm border-0 rounded-3 mt-3"><div class="card-body"><h6 class="fw-bold">Observaciones</h6><p class="mb-0">{{ $plan->Observaciones }}</p></div></div>
            @endif
        </div>
    </div>
</div>
@php $contador = 0; @endphp
@php // Vista completa con CRUD de fases, CSRF y modal de actualización. @endphp
@php // El layout medico proporciona AdminLTE, Bootstrap 4 y gym-theme.css. @endphp
@php // Las rutas usadas son: medico.rehabilitacion.planes y actualizarFase. @endphp
@php // El controlador RehabilitacionController maneja toda la lógica de negocio. @endphp
@php // Fin de la vista. @endphp
