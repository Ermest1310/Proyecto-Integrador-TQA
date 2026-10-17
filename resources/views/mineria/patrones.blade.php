@extends('layouts.admin')

@section('title', 'Patrones de Lesiones')

@section('content')
<div class="container-fluid py-4">
    <div class="alpha-hero-panel mb-4">
        <div class="hero-overlay-grid"></div>
        <div class="p-4 position-relative" style="z-index:2;">
            <h1 class="hero-welcome"><i class="fas fa-heartbeat text-gold me-2"></i>Patrones de Lesiones</h1>
            <p class="hero-subtitle mb-0">Análisis multivariable de la distribución de lesiones en gimnastas</p>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="small-box bg-danger"><div class="inner"><h3>{{ $patrones['totalLesiones'] }}</h3><p>Total Lesiones</p></div><div class="icon"><i class="fas fa-notes-medical"></i></div></div>
        </div>
        <div class="col-md-3">
            <div class="small-box bg-info"><div class="inner"><h3>{{ $patrones['porEstado']->firstWhere('Estado', 'Lesionado')->total ?? 0 }}</h3><p>Lesionados Actualmente</p></div><div class="icon"><i class="fas fa-user-injured"></i></div></div>
        </div>
        <div class="col-md-3">
            <div class="small-box bg-success"><div class="inner"><h3>{{ $patrones['porEstado']->firstWhere('Estado', 'Recuperada')->total ?? 0 }}</h3><p>Recuperadas</p></div><div class="icon"><i class="fas fa-check-circle"></i></div></div>
        </div>
        <div class="col-md-3">
            <div class="small-box bg-warning"><div class="inner"><h3>{{ $patrones['porEstado']->firstWhere('Estado', 'En Rehabilitación')->total ?? 0 }}</h3><p>En Rehabilitación</p></div><div class="icon"><i class="fas fa-procedures"></i></div></div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header"><i class="fas fa-notes-medical me-2"></i>Por Parte del Cuerpo</div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead><tr><th>Parte del Cuerpo</th><th>Casos</th><th>%</th></tr></thead>
                        <tbody>
                            @foreach($patrones['porParteCuerpo'] as $p)
                            <tr>
                                <td>{{ $p->ParteCuerpo }}</td>
                                <td>{{ $p->total }}</td>
                                <td>
                                    <div class="progress" style="height:6px;">
                                        <div class="progress-bar bg-danger" style="width:{{ $patrones['totalLesiones'] > 0 ? ($p->total / $patrones['totalLesiones'] * 100) : 0 }}%"></div>
                                    </div>
                                    <small>{{ $patrones['totalLesiones'] > 0 ? round($p->total / $patrones['totalLesiones'] * 100, 1) : 0 }}%</small>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header"><i class="fas fa-balance-scale me-2"></i>Por Gravedad</div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead><tr><th>Gravedad</th><th>Casos</th><th>%</th></tr></thead>
                        <tbody>
                            @foreach($patrones['porGravedad'] as $p)
                            <tr>
                                <td><span class="badge badge-{{ $p->Gravedad == 'Grave' ? 'danger' : ($p->Gravedad == 'Moderada' ? 'warning' : 'success') }}">{{ $p->Gravedad }}</span></td>
                                <td>{{ $p->total }}</td>
                                <td>
                                    <div class="progress" style="height:6px;">
                                        <div class="progress-bar bg-{{ $p->Gravedad == 'Grave' ? 'danger' : ($p->Gravedad == 'Moderada' ? 'warning' : 'success') }}" style="width:{{ $patrones['totalLesiones'] > 0 ? ($p->total / $patrones['totalLesiones'] * 100) : 0 }}%"></div>
                                    </div>
                                    <small>{{ $patrones['totalLesiones'] > 0 ? round($p->total / $patrones['totalLesiones'] * 100, 1) : 0 }}%</small>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header"><i class="fas fa-tag me-2"></i>Por Tipo de Lesión</div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead><tr><th>Tipo</th><th>Casos</th></tr></thead>
                        <tbody>
                            @foreach($patrones['porTipo'] as $p)
                            <tr><td>{{ $p->Tipo }}</td><td>{{ $p->total }}</td></tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header"><i class="fas fa-level-up-alt me-2"></i>Por Nivel del Gimnasta</div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead><tr><th>Nivel</th><th>Lesiones</th><th>%</th></tr></thead>
                        <tbody>
                            @foreach($patrones['porNivel'] as $p)
                            <tr>
                                <td><span class="badge badge-info">{{ $p->Nivel }}</span></td>
                                <td>{{ $p->total }}</td>
                                <td>
                                    <div class="progress" style="height:6px;">
                                        <div class="progress-bar bg-info" style="width:{{ $patrones['totalLesiones'] > 0 ? ($p->total / $patrones['totalLesiones'] * 100) : 0 }}%"></div>
                                    </div>
                                    <small>{{ $patrones['totalLesiones'] > 0 ? round($p->total / $patrones['totalLesiones'] * 100, 1) : 0 }}%</small>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header"><i class="fas fa-clock me-2"></i>Por Estado</div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead><tr><th>Estado</th><th>Casos</th></tr></thead>
                        <tbody>
                            @foreach($patrones['porEstado'] as $p)
                            <tr><td>{{ $p->Estado }}</td><td>{{ $p->total }}</td></tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header"><i class="fas fa-calendar-alt me-2"></i>Por Rango Etario</div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead><tr><th>Edad</th><th>Gimnastas</th><th>Lesiones</th><th>Promedio</th></tr></thead>
                        <tbody>
                            @foreach($patrones['porEdad'] as $rango => $d)
                            <tr>
                                <td>{{ $rango }} años</td>
                                <td>{{ $d['gimnastas'] }}</td>
                                <td>{{ $d['lesiones'] }}</td>
                                <td>{{ $d['gimnastas'] > 0 ? round($d['lesiones'] / $d['gimnastas'], 1) : 0 }} por gimnasta</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @if($patrones['porParteEspecifica']->isNotEmpty())
    <div class="card mb-4">
        <div class="card-header"><i class="fas fa-search me-2"></i>Partes Específicas más Afectadas</div>
        <div class="card-body">
            <table class="table table-sm">
                <thead><tr><th>Parte Específica</th><th>Casos</th></tr></thead>
                <tbody>
                    @foreach($patrones['porParteEspecifica'] as $p)
                    <tr><td>{{ $p->ParteEspecifica }}</td><td>{{ $p->total }}</td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <div class="card">
        <div class="card-header"><i class="fas fa-info-circle me-2"></i>Metodología</div>
        <div class="card-body">
            <p><strong>Fuente de datos:</strong> Tabla <code>lesiones</code> con JOIN a <code>usuarios</code> para nivel y edad.</p>
            <p><strong>Análisis:</strong> Distribución de frecuencias por variable categórica con porcentajes sobre el total de lesiones activas.</p>
        </div>
    </div>
</div>
@endsection
