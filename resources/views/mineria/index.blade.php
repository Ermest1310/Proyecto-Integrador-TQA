@extends('layouts.admin')

@section('title', 'Minería de Datos')

@section('content')
<div class="container-fluid py-4">
    <div class="alpha-hero-panel mb-4">
        <div class="hero-overlay-grid"></div>
        <div class="p-4 position-relative" style="z-index:2;">
            <h1 class="hero-welcome"><i class="fas fa-database text-gold me-2"></i>Minería de Datos Deportivos</h1>
            <p class="hero-subtitle mb-0">Análisis inteligente de rendimiento, patrones de lesiones y predicciones con IA</p>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info"><div class="inner"><h3>{{ $totalGimnastas }}</h3><p>Gimnastas</p></div><div class="icon"><i class="fas fa-users"></i></div></div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success"><div class="inner"><h3>{{ $totalSesiones }}</h3><p>Sesiones Registradas</p></div><div class="icon"><i class="fas fa-calendar-check"></i></div></div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning"><div class="inner"><h3>{{ $totalLesiones }}</h3><p>Lesiones Activas</p></div><div class="icon"><i class="fas fa-notes-medical"></i></div></div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger"><div class="inner"><h3>{{ $totalEntrenamientos }}</h3><p>Entrenamientos</p></div><div class="icon"><i class="fas fa-dumbbell"></i></div></div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <a href="{{ route('mineria.clustering') }}" class="text-decoration-none">
                <div class="alpha-module-card p-4 mb-4">
                    <div class="module-glow"></div>
                    <div class="d-flex align-items-center mb-3">
                        <div class="module-icon-box me-3"><i class="fas fa-layer-group"></i></div>
                        <h5 class="module-title mb-0">Segmentación de Gimnastas</h5>
                    </div>
                    <p class="module-text">Algoritmo K-Means que agrupa gimnastas por IMC, edad, rendimiento y lesiones. Identifica clusters de rendimiento óptimo, moderado y requiere atención.</p>
                    <span class="module-footer-arrow">Ver segmentación →</span>
                </div>
            </a>
        </div>
        <div class="col-lg-6">
            <a href="{{ route('mineria.patrones') }}" class="text-decoration-none">
                <div class="alpha-module-card p-4 mb-4">
                    <div class="module-glow"></div>
                    <div class="d-flex align-items-center mb-3">
                        <div class="module-icon-box me-3"><i class="fas fa-heartbeat"></i></div>
                        <h5 class="module-title mb-0">Patrones de Lesiones</h5>
                    </div>
                    <p class="module-text">Análisis de distribución de lesiones por parte del cuerpo, gravedad, tipo, nivel del gimnasta y rango etario. Datos extraídos del histórico clínico.</p>
                    <span class="module-footer-arrow">Ver patrones →</span>
                </div>
            </a>
        </div>
        <div class="col-lg-6">
            <a href="{{ route('mineria.correlaciones') }}" class="text-decoration-none">
                <div class="alpha-module-card p-4 mb-4">
                    <div class="module-glow"></div>
                    <div class="d-flex align-items-center mb-3">
                        <div class="module-icon-box me-3"><i class="fas fa-chart-scatter"></i></div>
                        <h5 class="module-title mb-0">Correlaciones y Estadísticas</h5>
                    </div>
                    <p class="module-text">Coeficientes de Pearson entre variables clave: IMC vs rendimiento, edad vs lesiones, sesiones vs rendimiento. Identifica relaciones significativas.</p>
                    <span class="module-footer-arrow">Ver correlaciones →</span>
                </div>
            </a>
        </div>
        <div class="col-lg-6">
            <a href="{{ route('mineria.prediccion') }}" class="text-decoration-none">
                <div class="alpha-module-card p-4 mb-4">
                    <div class="module-glow"></div>
                    <div class="d-flex align-items-center mb-3">
                        <div class="module-icon-box me-3"><i class="fas fa-brain"></i></div>
                        <h5 class="module-title mb-0">Predicción con IA</h5>
                    </div>
                    <p class="module-text">Utiliza OpenAI GPT-3.5 para analizar los datos minados y generar predicciones de rendimiento, perfiles de riesgo de lesiones y recomendaciones personalizadas.</p>
                    <span class="module-footer-arrow">Ir a predicciones →</span>
                </div>
            </a>
        </div>
    </div>

    @if(!empty($clusters))
    <div class="card mb-4">
        <div class="card-header"><i class="fas fa-layer-group me-2"></i>Resultado de Segmentación (K-Means)</div>
        <div class="card-body">
            <div class="row">
                @foreach($clusters as $cIdx => $miembros)
                    @php $info = $miembros[0]['info'] ?? ['nombre' => 'Cluster', 'color' => '#6366f1']; @endphp
                    <div class="col-md-4 mb-3">
                        <div class="card h-100" style="border-left: 4px solid {{ $info['color'] }};">
                            <div class="card-body">
                                <h5><i class="fas {{ $info['icono'] }} me-2" style="color:{{ $info['color'] }}"></i>{{ $info['nombre'] }}</h5>
                                <h2 class="fw-bold" style="color:{{ $info['color'] }}">{{ count($miembros) }}</h2>
                                <p class="text-muted small">gimnastas</p>
                                <ul class="list-unstyled small mb-0">
                                    @foreach($miembros as $m)
                                        <li class="py-1 border-bottom">{{ $m['nombre'] }} <span class="badge badge-info float-right">{{ $m['nivel'] }}</span></li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    @if(!empty($correlaciones) && isset($correlaciones['total_gimnastas']))
    <div class="card mb-4">
        <div class="card-header"><i class="fas fa-chart-scatter me-2"></i>Correlaciones Detectadas</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead><tr><th>Variables</th><th>Coeficiente r</th><th>Fuerza</th><th>Dirección</th><th>Interpretación</th></tr></thead>
                    <tbody>
                        @foreach(['imc_vs_rendimiento', 'edad_vs_lesiones', 'sesiones_vs_rendimiento', 'nivel_vs_lesiones', 'imc_vs_lesiones', 'edad_vs_rendimiento'] as $key)
                            @if(isset($correlaciones[$key]))
                            <tr>
                                <td>{{ $correlaciones[$key]['label_x'] }} ↔ {{ $correlaciones[$key]['label_y'] }}</td>
                                <td><strong>{{ $correlaciones[$key]['r'] }}</strong></td>
                                <td>{{ $correlaciones[$key]['interpretacion']['fuerza'] }}</td>
                                <td>{{ $correlaciones[$key]['interpretacion']['direccion'] }}</td>
                                <td>
                                    @php $r = $correlaciones[$key]['r']; @endphp
                                    @if(abs($r) >= 0.6) <span class="badge badge-success">Relación significativa</span>
                                    @elseif(abs($r) >= 0.3) <span class="badge badge-warning">Relación moderada</span>
                                    @else <span class="badge badge-secondary">Sin relación significativa</span>
                                    @endif
                                </td>
                            </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
            <small class="text-muted">Basado en {{ $correlaciones['total_gimnastas'] }} gimnastas activos. Coeficiente de Pearson (r) entre -1 y +1.</small>
        </div>
    </div>
    @endif
</div>
@endsection
