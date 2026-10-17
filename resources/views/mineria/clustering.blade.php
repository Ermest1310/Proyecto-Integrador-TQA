@extends('layouts.admin')

@section('title', 'Segmentación de Gimnastas')

@section('content')
<div class="container-fluid py-4">
    <div class="alpha-hero-panel mb-4">
        <div class="hero-overlay-grid"></div>
        <div class="p-4 position-relative" style="z-index:2;">
            <h1 class="hero-welcome"><i class="fas fa-layer-group text-gold me-2"></i>Segmentación de Gimnastas (K-Means)</h1>
            <p class="hero-subtitle mb-0">Agrupación inteligente por rendimiento, IMC, edad y carga lesional</p>
        </div>
    </div>

    @if(empty($clusters))
        <div class="alert alert-warning">No hay suficientes gimnastas (mínimo 3) para realizar la segmentación.</div>
    @else
    <div class="row mb-4">
        @foreach($clusters as $cIdx => $miembros)
            @php $info = $miembros[0]['info'] ?? ['nombre' => 'Cluster', 'icono' => 'fa-circle', 'color' => '#6366f1']; @endphp
            <div class="col-md-4 mb-3">
                <div class="card h-100" style="border-top: 4px solid {{ $info['color'] }};">
                    <div class="card-body text-center">
                        <div class="display-4 mb-2" style="color:{{ $info['color'] }};"><i class="fas {{ $info['icono'] }}"></i></div>
                        <h4 style="color:{{ $info['color'] }};">{{ $info['nombre'] }}</h4>
                        <h1 class="fw-bold">{{ count($miembros) }}</h1>
                        <p class="text-muted">gimnastas</p>
                        <hr>
                        @php
                            $promRend = collect($miembros)->avg('rendimiento');
                            $promLes = collect($miembros)->avg('lesiones');
                            $promEdad = collect($miembros)->avg('edad');
                            $promImc = collect($miembros)->avg('imc');
                        @endphp
                        <div class="row text-center small">
                            <div class="col-6"><span class="badge badge-info">Rend: {{ number_format($promRend, 1) }}%</span></div>
                            <div class="col-6"><span class="badge badge-warning">Les: {{ number_format($promLes, 1) }}</span></div>
                            <div class="col-6 mt-1"><span class="badge badge-secondary">Edad: {{ number_format($promEdad, 1) }}</span></div>
                            <div class="col-6 mt-1"><span class="badge badge-primary">IMC: {{ number_format($promImc, 1) }}</span></div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card">
        <div class="card-header"><i class="fas fa-list me-2"></i>Detalle de Gimnastas por Cluster</div>
        <div class="card-body">
            @foreach($clusters as $cIdx => $miembros)
                @php $info = $miembros[0]['info'] ?? ['nombre' => 'Cluster', 'icono' => 'fa-circle', 'color' => '#6366f1']; @endphp
                <h5 class="mt-3" style="color:{{ $info['color'] }};"><i class="fas {{ $info['icono'] }} me-2"></i>{{ $info['nombre'] }} ({{ count($miembros) }})</h5>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead><tr><th>Nombre</th><th>Nivel</th><th>Peso (kg)</th><th>Altura (cm)</th><th>IMC</th><th>Edad</th><th>Rendimiento</th><th>Lesiones</th></tr></thead>
                        <tbody>
                            @foreach($miembros as $m)
                            <tr>
                                <td>{{ $m['nombre'] }}</td>
                                <td><span class="badge badge-info">{{ $m['nivel'] }}</span></td>
                                <td>{{ $m['peso'] > 0 ? $m['peso'] : '-' }}</td>
                                <td>{{ $m['altura'] > 0 ? $m['altura'] : '-' }}</td>
                                <td>
                                    @if($m['peso'] > 0 && $m['altura'] > 0)
                                        <span class="badge badge-primary">{{ $m['imc'] }}</span>
                                        <small class="text-muted">({{ $m['peso'] }} / {{ $m['altura'] }}²)</small>
                                    @else
                                        <span class="badge badge-secondary">Sin datos</span>
                                    @endif
                                </td>
                                <td>{{ $m['edad'] }}</td>
                                <td>
                                    <div class="progress" style="height:6px;">
                                        <div class="progress-bar bg-{{ $m['rendimiento'] >= 70 ? 'success' : ($m['rendimiento'] >= 40 ? 'warning' : 'danger') }}" style="width:{{ $m['rendimiento'] }}%"></div>
                                    </div>
                                    <small>{{ $m['rendimiento'] }}%</small>
                                </td>
                                <td><span class="badge badge-{{ $m['lesiones'] > 3 ? 'danger' : ($m['lesiones'] > 0 ? 'warning' : 'success') }}">{{ $m['lesiones'] }}</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="card mt-4">
        <div class="card-header"><i class="fas fa-info-circle me-2"></i>Metodología</div>
        <div class="card-body">
            <p><strong>Algoritmo:</strong> K-Means clustering con k=3, inicialización aleatoria, distancia euclidiana ponderada.</p>
            <p><strong>Variables normalizadas:</strong> rendimiento (peso 2x), IMC/40, edad/40, lesiones/10.</p>
            <p><strong>Convergencia:</strong> 10 iteraciones máximas o hasta que los centroides no cambien.</p>
            <p><strong>Datos utilizados:</strong> {{ $gimnastas->count() }} gimnastas de la tabla <code>usuarios</code>, sesiones de <code>sesiones_entrenamiento</code> y lesiones de <code>lesiones</code>.</p>
        </div>
    </div>
</div>
@endsection
