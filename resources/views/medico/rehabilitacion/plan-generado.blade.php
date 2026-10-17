@extends('layouts.medico')

@section('title', 'Plan Generado - GAM')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h3 class="fw-bold text-navy-title mb-1">
                <i class="fas fa-clipboard-check text-gold me-2"></i>Plan de Rehabilitación Generado
            </h3>
            <p class="text-muted small mb-0">Ruta clínica óptima calculada con algoritmo A*</p>
        </div>
        <div class="col-md-6 text-md-end">
            <a href="{{ route('medico.rehabilitacion.index') }}" class="btn btn-alpha-action-secondary">
                <i class="fas fa-arrow-left me-1"></i> Volver
            </a>
        </div>
    </div>

    @if(!$resultado['success'])
        <div class="alert alert-danger border-0 rounded-3">
            <i class="fas fa-exclamation-triangle me-2"></i> {{ $resultado['mensaje'] }}
        </div>
    @else
        <div class="row">
            <div class="col-md-4">
                <div class="alpha-kpi-card mb-3">
                    <div class="d-flex align-items-center">
                        <div class="alpha-kpi-icon bg-gold">
                            <i class="fas fa-user-injured"></i>
                        </div>
                        <div class="ml-3">
                            <h6 class="text-muted small mb-0">Gimnasta</h6>
                            <h5 class="fw-bold mb-0">{{ $resultado['gimnasta']['nombre'] }}</h5>
                            <small class="text-muted">Nivel: {{ $resultado['gimnasta']['nivel'] }}</small>
                        </div>
                    </div>
                </div>

                <div class="alpha-kpi-card mb-3">
                    <div class="d-flex align-items-center">
                        <div class="alpha-kpi-icon bg-danger">
                            <i class="fas fa-procedures"></i>
                        </div>
                        <div class="ml-3">
                            <h6 class="text-muted small mb-0">Lesión</h6>
                            <h5 class="fw-bold mb-0">{{ $resultado['lesion']['tipo'] }}</h5>
                            <small class="text-muted">{{ $resultado['lesion']['parte_cuerpo'] }} - {{ $resultado['lesion']['gravedad'] }}</small>
                        </div>
                    </div>
                </div>

                <div class="alpha-kpi-card mb-3">
                    <div class="d-flex align-items-center">
                        <div class="alpha-kpi-icon bg-success">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div class="ml-3">
                            <h6 class="text-muted small mb-0">Duración Estimada</h6>
                            <h5 class="fw-bold mb-0">{{ $resultado['plan']['duracion_total_dias'] }} días</h5>
                            <small class="text-muted">Fin: {{ $resultado['plan']['fecha_fin_estimada'] }}</small>
                        </div>
                    </div>
                </div>

                <div class="alpha-kpi-card mb-3">
                    <div class="d-flex align-items-center">
                        <div class="alpha-kpi-icon bg-warning">
                            <i class="fas fa-exclamation-circle"></i>
                        </div>
                        <div class="ml-3">
                            <h6 class="text-muted small mb-0">Riesgo de Recaída</h6>
                            <h5 class="fw-bold mb-0">{{ round($resultado['plan']['riesgo_recaida_total'] * 100, 1) }}%</h5>
                            <small class="text-muted">Factor: {{ $resultado['plan']['factor_riesgo_aplicado'] }}x</small>
                        </div>
                    </div>
                </div>

                <div class="alpha-kpi-card">
                    <div class="d-flex align-items-center">
                        <div class="alpha-kpi-icon bg-info">
                            <i class="fas fa-layer-group"></i>
                        </div>
                        <div class="ml-3">
                            <h6 class="text-muted small mb-0">Fases del Plan</h6>
                            <h5 class="fw-bold mb-0">{{ $resultado['plan']['total_fases'] }}</h5>
                            <small class="text-muted">Ruta óptima A*</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="alpha-glass-list-card p-4">
                    <h5 class="fw-bold mb-4">
                        <i class="fas fa-route text-gold me-2"></i>Secuencia de Rehabilitación
                    </h5>

                    <div class="timeline">
                        @foreach($resultado['plan']['secuencia'] as $fase)
                            <div class="timeline-item">
                                <div class="timeline-marker {{ $fase['es_final'] ? 'bg-success' : 'bg-primary' }}">
                                    <i class="fas {{ $fase['es_final'] ? 'fa-check' : 'fa-step-forward' }}"></i>
                                </div>
                                <div class="timeline-content">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="fw-bold mb-1">
                                                Fase {{ $fase['orden'] }}: {{ $fase['nombre'] }}
                                            </h6>
                                            <p class="text-muted small mb-2">{{ $fase['descripcion'] }}</p>
                                        </div>
                                        @if($fase['es_final'])
                                            <span class="badge badge-success">ALTA</span>
                                        @endif
                                    </div>
                                    <div class="row small text-muted">
                                        <div class="col-md-4">
                                            <i class="fas fa-calendar-alt me-1"></i>
                                            {{ date('d/m/Y', strtotime($fase['fecha_inicio_estimada'])) }} - {{ date('d/m/Y', strtotime($fase['fecha_fin_estimada'])) }}
                                        </div>
                                        <div class="col-md-4">
                                            <i class="fas fa-hourglass-half me-1"></i>
                                            {{ $fase['duracion_dias'] }} días
                                        </div>
                                        <div class="col-md-4">
                                            <i class="fas fa-shield-alt me-1"></i>
                                            Riesgo: {{ round($fase['riesgo_recaida'] * 100, 1) }}%
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                @if(isset($resultado['plan_guardado']))
                    <div class="alert alert-success border-0 rounded-3 mt-3">
                        <i class="fas fa-check-circle me-2"></i>
                        Plan guardado exitosamente con ID: <strong>{{ $resultado['plan_guardado']['plan_id'] }}</strong>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}
.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: linear-gradient(180deg, #bfa15f 0%, #1e3e62 100%);
}
.timeline-item {
    position: relative;
    padding-bottom: 20px;
}
.timeline-marker {
    position: absolute;
    left: -23px;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 11px;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px rgba(191,161,95,0.3);
}
.timeline-content {
    background: rgba(248,250,252,0.5);
    border-radius: 8px;
    padding: 15px;
    border-left: 3px solid #bfa15f;
}
</style>
@endsection
