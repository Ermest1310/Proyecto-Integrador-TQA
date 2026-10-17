@extends('layouts.admin')

@section('title', 'Correlaciones')

@section('content')
<div class="container-fluid py-4">
    <div class="alpha-hero-panel mb-4">
        <div class="hero-overlay-grid"></div>
        <div class="p-4 position-relative" style="z-index:2;">
            <h1 class="hero-welcome"><i class="fas fa-chart-scatter text-gold me-2"></i>Correlaciones y Estadísticas</h1>
            <p class="hero-subtitle mb-0">Coeficientes de Pearson entre variables clave del rendimiento deportivo</p>
        </div>
    </div>

    @if(empty($correlaciones) || !isset($correlaciones['total_gimnastas']))
        <div class="alert alert-warning">No hay suficientes datos (mínimo 3 gimnastas) para calcular correlaciones.</div>
    @else
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="small-box bg-info"><div class="inner"><h3>{{ $correlaciones['total_gimnastas'] }}</h3><p>Gimnastas Analizados</p></div><div class="icon"><i class="fas fa-users"></i></div></div>
        </div>
        <div class="col-md-3">
            @php $f = collect($correlaciones)->filter(fn($v, $k) => is_array($v) && isset($v['r']) && abs($v['r']) >= 0.6)->count(); @endphp
            <div class="small-box bg-success"><div class="inner"><h3>{{ $f }}</h3><p>Correlaciones Fuertes</p></div><div class="icon"><i class="fas fa-link"></i></div></div>
        </div>
        <div class="col-md-3">
            @php $m = collect($correlaciones)->filter(fn($v, $k) => is_array($v) && isset($v['r']) && abs($v['r']) >= 0.3 && abs($v['r']) < 0.6)->count(); @endphp
            <div class="small-box bg-warning"><div class="inner"><h3>{{ $m }}</h3><p>Correlaciones Moderadas</p></div><div class="icon"><i class="fas fa-minus"></i></div></div>
        </div>
        <div class="col-md-3">
            @php $d = collect($correlaciones)->filter(fn($v, $k) => is_array($v) && isset($v['r']) && abs($v['r']) < 0.3)->count(); @endphp
            <div class="small-box bg-secondary"><div class="inner"><h3>{{ $d }}</h3><p>Sin Correlación</p></div><div class="icon"><i class="fas fa-times"></i></div></div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header"><i class="fas fa-table me-2"></i>Matriz de Correlaciones</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>Variable X</th>
                            <th>Variable Y</th>
                            <th>Coeficiente r</th>
                            <th>Fuerza</th>
                            <th>Dirección</th>
                            <th>Visual</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(['imc_vs_rendimiento', 'edad_vs_lesiones', 'sesiones_vs_rendimiento', 'nivel_vs_lesiones', 'imc_vs_lesiones', 'edad_vs_rendimiento'] as $key)
                            @if(isset($correlaciones[$key]))
                            @php $c = $correlaciones[$key]; $r = $c['r']; @endphp
                            <tr>
                                <td><strong>{{ $c['label_x'] }}</strong></td>
                                <td><strong>{{ $c['label_y'] }}</strong></td>
                                <td><span class="badge badge-{{ abs($r) >= 0.6 ? 'success' : (abs($r) >= 0.3 ? 'warning' : 'secondary') }}" style="font-size:1rem;">{{ $r }}</span></td>
                                <td>{{ $c['interpretacion']['fuerza'] }}</td>
                                <td>
                                    @if($c['interpretacion']['direccion'] == 'positiva')
                                        <span class="text-success"><i class="fas fa-arrow-up"></i> Positiva</span>
                                    @else
                                        <span class="text-danger"><i class="fas fa-arrow-down"></i> Negativa</span>
                                    @endif
                                </td>
                                <td style="min-width:200px;">
                                    <div class="progress" style="height:20px; border-radius:10px;">
                                        @php $barWidth = abs($r) * 100; $isNeg = $r < 0; @endphp
                                        <div class="progress-bar {{ $isNeg ? 'bg-danger' : 'bg-success' }}" 
                                             style="width:{{ $barWidth }}%; height:20px; {{ $isNeg ? 'margin-left:auto;' : '' }}">
                                            {{ abs($r) >= 0.1 ? number_format(abs($r) * 100, 0) . '%' : '' }}
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
            <p class="text-muted small mt-2">
                <i class="fas fa-info-circle"></i>
                El coeficiente de Pearson (r) mide la relación lineal entre dos variables. 
                Rango: -1 (correlación negativa perfecta) a +1 (correlación positiva perfecta). 
                0 = sin correlación lineal. Datos de {{ $correlaciones['total_gimnastas'] }} gimnastas.
            </p>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><i class="fas fa-lightbulb me-2"></i>Interpretación de Resultados</div>
        <div class="card-body">
            <div class="row">
                @foreach(['imc_vs_rendimiento', 'edad_vs_lesiones', 'sesiones_vs_rendimiento', 'nivel_vs_lesiones', 'imc_vs_lesiones', 'edad_vs_rendimiento'] as $key)
                    @if(isset($correlaciones[$key]))
                    @php $c = $correlaciones[$key]; $r = $c['r']; @endphp
                    <div class="col-md-6 mb-3">
                        <div class="card h-100" style="border-left: 4px solid {{ abs($r) >= 0.6 ? '#10b981' : (abs($r) >= 0.3 ? '#f59e0b' : '#6c757d') }};">
                            <div class="card-body">
                                <h6>{{ $c['label_x'] }} vs {{ $c['label_y'] }}</h6>
                                <p class="small mb-0">
                                    r = <strong>{{ $r }}</strong> &mdash;
                                    Correlación {{ $c['interpretacion']['fuerza'] }} {{ $c['interpretacion']['direccion'] }}.
                                    @if(abs($r) >= 0.6)
                                        <span class="text-success">Relación significativa detectada.</span>
                                    @elseif(abs($r) >= 0.3)
                                        <span class="text-warning">Relación moderada, requiere más datos.</span>
                                    @else
                                        <span class="text-muted">No hay relación lineal significativa.</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
