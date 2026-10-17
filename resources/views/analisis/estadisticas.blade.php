@extends('layouts.admin')

@section('title', 'Estadísticas Deportivas')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<div class="container-fluid py-4">
    <div class="alpha-hero-panel mb-4">
        <div class="hero-overlay-grid"></div>
        <div class="p-4 position-relative" style="z-index:2;">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h1 class="hero-welcome"><i class="fas fa-chart-pie text-gold me-2"></i>Estadísticas Deportivas</h1>
                    <p class="hero-subtitle mb-0">Métricas de rendimiento, evaluaciones y progreso de atletas</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('analisis.estadisticas.excel') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-file-excel"></i> Excel
                    </a>
                    <a href="{{ route('analisis.estadisticas.pdf') }}" class="btn btn-danger btn-sm">
                        <i class="fas fa-file-pdf"></i> PDF
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-2-4 col-sm-6">
            <div class="info-box bg-gradient-info">
                <span class="info-box-icon"><i class="fas fa-users"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Gimnastas</span>
                    <span class="info-box-number">{{ $totalGimnastas }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-2-4 col-sm-6">
            <div class="info-box bg-gradient-success">
                <span class="info-box-icon"><i class="fas fa-weight"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">IMC Promedio</span>
                    <span class="info-box-number">{{ $imcPromedio > 0 ? $imcPromedio : 'N/A' }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-2-4 col-sm-6">
            <div class="info-box bg-gradient-warning">
                <span class="info-box-icon"><i class="fas fa-chart-line"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Rendimiento Prom.</span>
                    <span class="info-box-number">{{ $rendimientoPromedio }}%</span>
                </div>
            </div>
        </div>
        <div class="col-md-2-4 col-sm-6">
            <div class="info-box bg-gradient-danger">
                <span class="info-box-icon"><i class="fas fa-bolt"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Lesiones Activas</span>
                    <span class="info-box-number">{{ $lesionesActivas }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-2-4 col-sm-6">
            <div class="info-box bg-gradient-secondary">
                <span class="info-box-icon"><i class="fas fa-check-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Sesiones Completadas</span>
                    <span class="info-box-number">{{ $completadas }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-primary"><h5 class="card-title mb-0 text-white"><i class="fas fa-chart-bar me-2"></i>IMC Promedio por Nivel</h5></div>
                <div class="card-body">
                    <canvas id="chartImcNivel" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-info"><h5 class="card-title mb-0 text-white"><i class="fas fa-users me-2"></i>Distribución por Edad</h5></div>
                <div class="card-body">
                    <canvas id="chartEdad" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-success"><h5 class="card-title mb-0 text-white"><i class="fas fa-arrows-alt me-2"></i>Peso vs Altura</h5></div>
                <div class="card-body">
                    <canvas id="chartPesoAltura" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    @if(count($alertas) > 0)
    <div class="card mb-4 border-danger">
        <div class="card-header bg-danger text-white">
            <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Alertas — Gimnastas que requieren atención</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-dark">
                        <tr><th>Gimnasta</th><th>Alerta</th></tr>
                    </thead>
                    <tbody>
                        @foreach($alertas as $a)
                        <tr>
                            <td class="fw-bold">{{ $a['gimnasta'] }}</td>
                            <td>
                                @foreach($a['alertas'] as $al)
                                <span class="badge me-1" style="background:{{ $al['color'] }}"><i class="fas {{ $al['icono'] }} me-1"></i>{{ $al['tipo'] }}:</span>
                                <small>{{ $al['texto'] }}</small><br>
                                @endforeach
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <div class="card mb-4">
        <div class="card-header bg-gradient-dark">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-table me-2"></i>Gimnastas</h5>
                <div>
                    <input type="text" id="filtroTabla" class="form-control form-control-sm" placeholder="Buscar gimnasta..." style="width:250px">
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="tablaGimnastas">
                    <thead class="table-light">
                        <tr>
                            <th>Gimnasta</th>
                            <th>Nivel</th>
                            <th>Peso (kg)</th>
                            <th>Altura (m)</th>
                            <th>IMC</th>
                            <th>Edad</th>
                            <th>Rendimiento</th>
                            <th>Lesiones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($gimnastasData as $g)
                        <tr>
                            <td class="fw-bold">{{ $g['nombre'] }}</td>
                            <td><span class="badge bg-info">{{ $g['nivel'] ?: '-' }}</span></td>
                            <td>{{ $g['peso'] > 0 ? $g['peso'] : '-' }}</td>
                            <td>{{ $g['altura'] > 0 ? $g['altura'] : '-' }}</td>
                            <td>
                                @if($g['imc'] > 0)
                                    <span class="badge {{ $g['imc'] >= 18.5 && $g['imc'] <= 25 ? 'bg-success' : 'bg-warning text-dark' }}">
                                        {{ $g['imc'] }}
                                    </span>
                                @else
                                    <span class="text-muted">Sin datos</span>
                                @endif
                            </td>
                            <td>{{ $g['edad'] ?: '-' }}</td>
                            <td>
                                <div class="progress" style="height:18px;min-width:80px">
                                    <div class="progress-bar {{ $g['rendimiento'] >= 70 ? 'bg-success' : ($g['rendimiento'] >= 40 ? 'bg-warning' : 'bg-danger') }}" 
                                         style="width:{{ $g['rendimiento'] }}%">
                                        {{ $g['rendimiento'] }}%
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($g['lesiones_activas'] > 0)
                                    <span class="badge bg-danger">{{ $g['lesiones_activas'] }}</span>
                                @else
                                    <span class="text-muted">0</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="row g-4">
        @foreach([
            ['r' => 'analisis.aptitud', 'i' => 'fa-heartbeat', 't' => 'Aptitud Médica', 'd' => 'Certificaciones, restricciones y diagnósticos'],
            ['r' => 'analisis.nutricional', 'i' => 'fa-apple-alt', 't' => 'Nutrición', 'd' => 'IMC, % graso, masa muscular, hidratación'],
            ['r' => 'analisis.respiratoria', 'i' => 'fa-lungs', 't' => 'Respiratorio', 'd' => 'Capacidad pulmonar, SatO2, diagnóstico'],
            ['r' => 'analisis.psicologica', 'i' => 'fa-brain', 't' => 'Psicología', 'd' => 'Ansiedad, estrés, motivación, sueño'],
            ['r' => 'analisis.fisiologica', 'i' => 'fa-heart-pulse', 't' => 'Fisiología', 'd' => 'FC, presión arterial, SatO2'],
        ] as $m)
        <div class="col-md-4">
            <a href="{{ route($m['r']) }}" class="text-decoration-none">
                <div class="alpha-module-card h-100">
                    <div class="module-glow"></div>
                    <div class="card-body-content p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="module-icon-box"><i class="fas {{ $m['i'] }}"></i></div>
                            <h5 class="module-title mb-0 ms-3">{{ $m['t'] }}</h5>
                        </div>
                        <p class="module-text mb-0">{{ $m['d'] }}</p>
                    </div>
                </div>
            </a>
        </div>
        @endforeach
    </div>
</div>
@endsection

@section('scripts')
<style>
.col-md-2-4 { flex: 0 0 20%; max-width: 20%; padding: 0 8px; }
@media (max-width: 768px) { .col-md-2-4 { flex: 0 0 50%; max-width: 50%; } }
@media (max-width: 576px) { .col-md-2-4 { flex: 0 0 100%; max-width: 100%; } }
.info-box .info-box-number { font-size: 1.5rem; }
.bg-gradient-info { background: linear-gradient(135deg,#17a2b8,#0d6efd) !important; }
.bg-gradient-success { background: linear-gradient(135deg,#28a745,#20c997) !important; }
.bg-gradient-warning { background: linear-gradient(135deg,#ffc107,#fd7e14) !important; }
.bg-gradient-danger { background: linear-gradient(135deg,#dc3545,#e83e8c) !important; }
.bg-gradient-secondary { background: linear-gradient(135deg,#6c757d,#495057) !important; }
.bg-gradient-dark { background: linear-gradient(135deg,#343a40,#212529) !important; color: #fff; }
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
    new Chart(document.getElementById('chartImcNivel'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($imcPorNivel->pluck('nivel')) !!},
            datasets: [{
                label: 'IMC Promedio',
                data: {!! json_encode($imcPorNivel->pluck('imc_promedio')) !!},
                backgroundColor: ['#0d6efd','#6610f2','#fd7e14','#20c997','#dc3545','#ffc107'],
                borderRadius: 4
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, title: { display: true, text: 'IMC' } } }
        }
    });

    new Chart(document.getElementById('chartEdad'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($rangos) !!},
            datasets: [{
                label: 'Gimnastas',
                data: {!! json_encode($distEdad) !!},
                backgroundColor: '#0d6efd',
                borderRadius: 4
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });

    var paData = {!! json_encode($pesoAlturaData->map(fn($g) => ['x' => $g['altura'], 'y' => $g['peso'], 'name' => $g['nombre']])) !!};
    new Chart(document.getElementById('chartPesoAltura'), {
        type: 'scatter',
        data: {
            datasets: [{
                label: 'Gimnastas',
                data: paData,
                backgroundColor: '#0d6efd',
                pointRadius: 6,
                pointHoverRadius: 9
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(ctx) {
                            var p = ctx.raw;
                            return p.name + ': ' + p.y + 'kg / ' + p.x + 'm';
                        }
                    }
                }
            },
            scales: {
                x: { title: { display: true, text: 'Altura (m)' } },
                y: { title: { display: true, text: 'Peso (kg)' } }
            }
        }
    });

    document.getElementById('filtroTabla').addEventListener('keyup', function() {
        var val = this.value.toLowerCase();
        document.querySelectorAll('#tablaGimnastas tbody tr').forEach(function(row) {
            row.style.display = row.cells[0].textContent.toLowerCase().includes(val) ? '' : 'none';
        });
    });
});
</script>
@endsection