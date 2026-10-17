@extends('layouts.admin')

@section('title', 'Reporte Completo')

@section('content')
<style>
.section-divider {
    border-left: 4px solid;
    padding-left: 15px;
    margin: 20px 0 15px 0;
}
.section-divider.green { border-color: #28a745; }
.section-divider.red { border-color: #dc3545; }
.section-divider.blue { border-color: #17a2b8; }
.badge-nivel {
    font-size: 0.85rem;
    padding: 5px 10px;
}
.table-modern {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.table-modern thead {
    font-weight: 600;
}
.table-modern tbody tr:hover {
    background-color: #f8f9fa;
    transition: all 0.2s;
}
</style>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-chart-line text-primary"></i> Reportes Detallados</h1>
            </div>
            <div class="col-sm-6">
                <div class="float-right">
                    <div class="btn-group">
                        <button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown">
                            <i class="fas fa-file-pdf"></i> Exportar PDF
                        </button>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="{{ route('admin.reportes.exportar-pdf', ['tipo' => 'completo']) }}">
                                <i class="fas fa-file-alt"></i> Reporte Completo
                            </a>
                            <a class="dropdown-item" href="{{ route('admin.reportes.exportar-pdf', ['tipo' => 'gimnastas']) }}">
                                <i class="fas fa-users"></i> Solo Gimnastas
                            </a>
                            <a class="dropdown-item" href="{{ route('admin.reportes.exportar-pdf', ['tipo' => 'sesiones']) }}">
                                <i class="fas fa-calendar"></i> Solo Sesiones
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="section-divider green">
            <h3 class="text-success"><i class="fas fa-users"></i> GIMNASTAS Y RENDIMIENTO EN SESIONES</h3>
        </div>
        <div class="card card-success card-outline">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-modern">
                        <thead class="thead-light">
                            <tr>
                                <th><i class="fas fa-user"></i> Gimnasta</th>
                                <th><i class="fas fa-layer-group"></i> Nivel</th>
                                <th><i class="fas fa-list-ol"></i> Total</th>
                                <th><i class="fas fa-check-circle"></i> Completadas</th>
                                <th><i class="fas fa-clock"></i> Tiempo (h)</th>
                                <th><i class="fas fa-user-tie"></i> Entrenadores</th>
                                <th><i class="fas fa-chart-line"></i> Rendimiento</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($datos['gimnastas_sesiones'] as $gimnasta)
                            <tr>
                                <td><strong>{{ $gimnasta->Nombre }} {{ $gimnasta->Apellido }}</strong></td>
                                <td class="text-center">
                                    <span class="badge badge-nivel badge-{{ 
                                        $gimnasta->Nivel == 'Elite' ? 'danger' : 
                                        ($gimnasta->Nivel == 'Avanzado' ? 'warning' : 
                                        ($gimnasta->Nivel == 'Intermedio' ? 'info' : 'secondary')) 
                                    }}">{{ $gimnasta->Nivel }}</span>
                                </td>
                                <td class="text-center"><strong>{{ $gimnasta->total_sesiones }}</strong></td>
                                <td class="text-center"><strong>{{ $gimnasta->sesiones_completadas }}</strong></td>
                                <td class="text-center">
                                    <span class="badge badge-info badge-pill" style="font-size: 0.9rem;">
                                        <i class="fas fa-hourglass-half"></i> {{ $gimnasta->tiempo_total_horas }}h
                                    </span>
                                </td>
                                <td><small class="text-muted">{{ $gimnasta->entrenadores ?: 'N/A' }}</small></td>
                                <td>
                                    @php
                                        $porcentaje = $gimnasta->total_sesiones > 0 ? 
                                            round(($gimnasta->sesiones_completadas / $gimnasta->total_sesiones) * 100) : 0;
                                    @endphp
                                    <div class="progress" style="height: 25px;">
                                        <div class="progress-bar bg-{{ $porcentaje >= 80 ? 'success' : ($porcentaje >= 50 ? 'warning' : 'danger') }}" 
                                             style="width: {{ $porcentaje }}%">
                                            <strong>{{ $porcentaje }}%</strong>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="section-divider red">
            <h3 class="text-danger"><i class="fas fa-exclamation-triangle"></i> LESIONES ACTIVAS</h3>
        </div>
        <div class="card card-danger card-outline">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-hover table-modern">
                        <thead class="thead-light">
                            <tr>
                                <th><i class="fas fa-user"></i> Gimnasta</th>
                                <th><i class="fas fa-notes-medical"></i> Tipo</th>
                                <th><i class="fas fa-hand-paper"></i> Parte</th>
                                <th><i class="fas fa-heartbeat"></i> Gravedad</th>
                                <th><i class="fas fa-info-circle"></i> Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($datos['lesiones_activas'] as $lesion)
                            <tr>
                                <td><strong>{{ $lesion->Nombre }} {{ $lesion->Apellido }}</strong></td>
                                <td>{{ $lesion->Tipo }}</td>
                                <td>{{ $lesion->ParteCuerpo }}</td>
                                <td class="text-center">
                                    <span class="badge badge-{{ 
                                        $lesion->Gravedad == 'Grave' ? 'danger' : 
                                        ($lesion->Gravedad == 'Moderada' ? 'warning' : 'info') 
                                    }}" style="font-size: 0.85rem; padding: 5px 10px;">
                                        {{ $lesion->Gravedad }}
                                    </span>
                                </td>
                                <td><small class="text-muted">{{ $lesion->Estado }}</small></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">
                                    <div class="alert alert-success mb-0">
                                        <i class="fas fa-check-circle"></i> No hay lesiones activas
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="section-divider blue">
            <h3 class="text-info"><i class="fas fa-chart-bar"></i> RENDIMIENTO POR NIVEL</h3>
        </div>
        <div class="card card-info card-outline">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-hover table-modern">
                        <thead class="thead-light">
                            <tr>
                                <th><i class="fas fa-layer-group"></i> Nivel</th>
                                <th class="text-center"><i class="fas fa-list-ol"></i> Total Sesiones</th>
                                <th class="text-center"><i class="fas fa-percentage"></i> Tasa de Éxito</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($datos['rendimiento_nivel'] as $nivel)
                            <tr>
                                <td>
                                    <span class="badge badge-nivel badge-{{ 
                                        $nivel->Nivel == 'Elite' ? 'danger' : 
                                        ($nivel->Nivel == 'Avanzado' ? 'warning' : 
                                        ($nivel->Nivel == 'Intermedio' ? 'info' : 'secondary')) 
                                    }}">{{ $nivel->Nivel }}</span>
                                </td>
                                <td class="text-center"><strong>{{ $nivel->total_sesiones }}</strong></td>
                                <td class="text-center">
                                    @php
                                        $porc = round($nivel->porcentaje_completadas);
                                    @endphp
                                    <span class="badge badge-{{ $porc >= 80 ? 'success' : ($porc >= 50 ? 'warning' : 'danger') }}" 
                                          style="font-size: 1rem; padding: 8px 15px;">
                                        {{ $porc }}%
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
