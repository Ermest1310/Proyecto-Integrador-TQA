@extends('layouts.medico')

@section('title', 'Rehabilitación - GAM')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h3 class="fw-bold text-navy-title mb-1">
                <i class="fas fa-heartbeat text-gold me-2"></i>Planificación de Rehabilitación
            </h3>
            <p class="text-muted small mb-0">Sistema A* de optimización de rutas clínicas</p>
        </div>
        <div class="col-md-6 text-md-end">
            <a href="{{ route('medico.rehabilitacion.planes') }}" class="btn btn-alpha-action-secondary">
                <i class="fas fa-list me-1"></i> Ver Planes
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="alpha-glass-list-card p-4">
                <h5 class="fw-bold mb-3">
                    <i class="fas fa-notes-medical text-gold me-2"></i>Lesiones Activas
                </h5>

                @if($lesiones->isEmpty())
                    <div class="alert alert-info border-0 rounded-3">
                        <i class="fas fa-info-circle me-2"></i> No hay lesiones activas registradas.
                    </div>
                @else
                    <div class="table-responsive alpha-table-container">
                        <table class="table alpha-table">
                            <thead>
                                <tr>
                                    <th>Gimnasta</th>
                                    <th>Tipo</th>
                                    <th>Parte del Cuerpo</th>
                                    <th>Gravedad</th>
                                    <th>Estado</th>
                                    <th>Fecha</th>
                                    <th class="text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($lesiones as $lesion)
                                    <tr>
                                        <td>
                                            <strong>{{ $lesion->gimnasta->Nombre }} {{ $lesion->gimnasta->Apellido }}</strong>
                                        </td>
                                        <td>{{ $lesion->Tipo }}</td>
                                        <td>{{ $lesion->ParteCuerpo }}</td>
                                        <td>
                                            @if($lesion->Gravedad === 'Leve')
                                                <span class="badge badge-success">Leve</span>
                                            @elseif($lesion->Gravedad === 'Moderada')
                                                <span class="badge badge-warning">Moderada</span>
                                            @else
                                                <span class="badge badge-danger">Grave</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($lesion->Estado === 'Lesionado')
                                                <span class="badge badge-alpha-programada">Lesionado</span>
                                            @elseif($lesion->Estado === 'En Rehabilitación')
                                                <span class="badge badge-alpha-curso">En Rehabilitación</span>
                                            @else
                                                <span class="badge badge-alpha-completada">Recuperada</span>
                                            @endif
                                        </td>
                                        <td>{{ $lesion->FechaDiagnostico ? $lesion->FechaDiagnostico->format('d/m/Y') : '-' }}</td>
                                        <td class="text-end">
                                            <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#planModal{{ $lesion->ID_Lesion }}">
                                                <i class="fas fa-plus-circle me-1"></i> Crear Plan
                                            </button>
                                        </td>
                                    </tr>

                                    <div class="modal fade" id="planModal{{ $lesion->ID_Lesion }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title">
                                                        <i class="fas fa-clipboard-list text-gold me-2"></i>
                                                        Nuevo Plan de Rehabilitación
                                                    </h4>
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                </div>
                                                <form action="{{ route('medico.rehabilitacion.crear') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="lesion_id" value="{{ $lesion->ID_Lesion }}">
                                                    <div class="modal-body">
                                                        <div class="form-group">
                                                            <label>Fecha de Inicio</label>
                                                            <input type="date" name="fecha_inicio" class="form-control alpha-form-control" value="{{ date('Y-m-d') }}" required>
                                                            @error('fecha_inicio')
                                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="form-group">
                                                            <label>
                                                                <input type="checkbox" name="guardar" value="1" checked>
                                                                Guardar plan automáticamente
                                                            </label>
                                                        </div>
                                                        <div class="alert alert-info small">
                                                            <i class="fas fa-robot me-1"></i>
                                                            El algoritmo A* calculará la ruta óptima considerando:
                                                            <ul class="mb-0 mt-1">
                                                                <li>Tipo y gravedad de la lesión</li>
                                                                <li>Historial de lesiones previas</li>
                                                                <li>Riesgo de recaída por fase</li>
                                                                <li>Duración estimada por etapa</li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-alpha-action-primary">
                                                            <i class="fas fa-magic me-1"></i> Generar Plan A*
                                                        </button>
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <div class="col-md-4">
            <div class="alpha-glass-list-card p-4">
                <h5 class="fw-bold mb-3">
                    <i class="fas fa-chart-line text-gold me-2"></i>Planes Recientes
                </h5>

                @if($planes->isEmpty())
                    <p class="text-muted small mb-0">No hay planes generados aún.</p>
                @else
                    <div class="list-group">
                        @foreach($planes as $plan)
                            <a href="{{ route('medico.rehabilitacion.verPlan', $plan->ID_Plan) }}" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">{{ $plan->gimnasta->Nombre }} {{ $plan->gimnasta->Apellido }}</h6>
                                    <small class="text-muted">{{ $plan->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="mb-1 small">{{ $plan->lesion->Tipo }} - {{ $plan->lesion->ParteCuerpo }}</p>
                                <small>
                                    @if($plan->Estado === 'En Curso')
                                        <span class="badge badge-alpha-curso">En Curso</span>
                                    @elseif($plan->Estado === 'Completado')
                                        <span class="badge badge-alpha-completada">Completado</span>
                                    @elseif($plan->Estado === 'Cancelado')
                                        <span class="badge badge-alpha-cancelada">Cancelado</span>
                                    @else
                                        <span class="badge badge-alpha-programada">Pendiente</span>
                                    @endif
                                    <span class="text-muted">{{ $plan->Duracion_Estimada_Dias }} días</span>
                                </small>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="alpha-glass-list-card p-4 mt-3">
                <h6 class="fw-bold mb-2">
                    <i class="fas fa-info-circle text-gold me-2"></i>Sobre el Algoritmo A*
                </h6>
                <p class="text-muted small mb-0">
                    El sistema utiliza búsqueda heurística A* para encontrar la ruta clínica óptima desde la lesión activa hasta el alta deportiva, minimizando tiempo total y riesgo de recaída.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
