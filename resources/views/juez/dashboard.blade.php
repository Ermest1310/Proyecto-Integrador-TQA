@extends('layouts.admin')

@section('title', 'Panel del Juez')

@section('content')
<div class="container-fluid py-4">
    <div class="alpha-hero-panel mb-4">
        <div class="hero-overlay-grid"></div>
        <div class="p-4 position-relative" style="z-index: 2;">
            <h1 class="hero-welcome"><i class="fas fa-gavel text-gold me-2"></i>Panel del Juez</h1>
            <p class="hero-subtitle mb-0">Gestiona y realiza evaluaciones técnicas de rutinas gimnásticas</p>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $stats['pendientes'] }}</h3>
                    <p>Pendientes</p>
                </div>
                <div class="icon"><i class="fas fa-clock"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $stats['en_revision'] }}</h3>
                    <p>En Revisión</p>
                </div>
                <div class="icon"><i class="fas fa-search"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $stats['confirmados'] }}</h3>
                    <p>Confirmados</p>
                </div>
                <div class="icon"><i class="fas fa-check-circle"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ $stats['total'] }}</h3>
                    <p>Totales</p>
                </div>
                <div class="icon"><i class="fas fa-tasks"></i></div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-list me-2"></i>Evaluaciones Asignadas</span>
            <a href="{{ route('juez.crear') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus"></i> Nueva Evaluación
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Gimnasta</th>
                            <th>Competencia</th>
                            <th>Aparato</th>
                            <th>Dificultad</th>
                            <th>Ejecución</th>
                            <th>Final</th>
                            <th>Descuentos</th>
                            <th>Estado</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($evaluaciones as $eval)
                        <tr class="{{ $eval->estado === 'pendiente' ? 'table-warning' : ($eval->estado === 'confirmado' ? 'table-success' : '') }}">
                            <td>
                                <strong>{{ $eval->gimnasta->Nombre ?? 'N/A' }} {{ $eval->gimnasta->Apellido ?? '' }}</strong>
                            </td>
                            <td>{{ $eval->competencia->NombreCompetencia ?? 'N/A' }}</td>
                            <td>{{ $eval->aparato->Nombre ?? 'N/A' }}</td>
                            <td>{{ $eval->nota_dificultad ?? '-' }}</td>
                            <td>{{ $eval->nota_ejecucion ?? '-' }}</td>
                            <td><strong>{{ $eval->nota_final ?? '-' }}</strong></td>
                            <td>
                                @php $totalDesc = $eval->descuentos->sum('descuento_final'); @endphp
                                @if($totalDesc > 0)
                                    <span class="text-danger">-{{ number_format($totalDesc, 2) }}</span>
                                @else
                                    <span class="text-muted">0</span>
                                @endif
                            </td>
                            <td>
                                @switch($eval->estado)
                                    @case('pendiente')
                                        <span class="badge badge-warning">Pendiente</span>
                                        @break
                                    @case('en_revision')
                                        <span class="badge badge-info">En Revisión</span>
                                        @break
                                    @case('confirmado')
                                        <span class="badge badge-success">Confirmado</span>
                                        @break
                                    @default
                                        <span class="badge badge-secondary">{{ $eval->estado }}</span>
                                @endswitch
                            </td>
                            <td>
                                <a href="{{ route('juez.evaluar', $eval->ID_EvaluacionJuez) }}"
                                   class="btn btn-sm {{ $eval->estado === 'confirmado' ? 'btn-outline-secondary' : 'btn-primary' }}">
                                    <i class="fas {{ $eval->estado === 'confirmado' ? 'fa-eye' : 'fa-gavel' }}"></i>
                                    {{ $eval->estado === 'confirmado' ? 'Ver' : 'Evaluar' }}
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">
                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                No tienes evaluaciones asignadas aún.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
