@extends('layouts.admin')

@section('title', 'Reportes Estadísticos')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="fas fa-chart-bar text-primary"></i> Reportes Estadísticos</h1>
            </div>
            <div class="col-sm-6">
                <div class="float-right">
                    <a href="{{ route('admin.reportes.exportar-pdf', ['tipo' => 'estadisticas']) }}" class="btn btn-danger">
                        <i class="fas fa-file-pdf"></i> Descargar PDF
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Usuarios -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-primary">
                <h3 class="card-title"><i class="fas fa-users"></i> Usuarios</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <div class="info-box bg-info">
                            <span class="info-box-icon"><i class="fas fa-users"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Usuarios</span>
                                <span class="info-box-number">{{ $estadisticas['usuarios']['total'] }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="info-box bg-success">
                            <span class="info-box-icon"><i class="fas fa-check"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Activos</span>
                                <span class="info-box-number">{{ $estadisticas['usuarios']['activos'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <h5>Por Rol:</h5>
                <canvas id="chartUsuariosRol" height="200"></canvas>
            </div>
        </div>
    </div>

    <!-- Niveles -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-success">
                <h3 class="card-title"><i class="fas fa-level-up-alt"></i> Niveles de Gimnastas</h3>
            </div>
            <div class="card-body">
                <h5>Distribución por Nivel:</h5>
                <canvas id="chartNiveles" height="180"></canvas>
                <hr>
                <h5>Solicitudes de Nivel:</h5>
                <div class="row">
                    <div class="col-4">
                        <small class="d-block">Pendientes</small>
                        <span class="badge badge-warning">{{ $estadisticas['niveles']['solicitudes_pendientes'] }}</span>
                    </div>
                    <div class="col-4">
                        <small class="d-block">Aprobadas</small>
                        <span class="badge badge-success">{{ $estadisticas['niveles']['solicitudes_aprobadas'] }}</span>
                    </div>
                    <div class="col-4">
                        <small class="d-block">Rechazadas</small>
                        <span class="badge badge-danger">{{ $estadisticas['niveles']['solicitudes_rechazadas'] }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Sesiones -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-warning">
                <h3 class="card-title"><i class="fas fa-calendar-alt"></i> Sesiones de Entrenamiento</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <div class="info-box bg-primary">
                            <span class="info-box-icon"><i class="fas fa-calendar"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total</span>
                                <span class="info-box-number">{{ $estadisticas['sesiones']['total'] }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="info-box bg-info">
                            <span class="info-box-icon"><i class="fas fa-calendar-check"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Este Mes</span>
                                <span class="info-box-number">{{ $estadisticas['sesiones']['este_mes'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <h5>Por Estado:</h5>
                <canvas id="chartSesiones" height="200"></canvas>
            </div>
        </div>
    </div>

    <!-- Postulaciones -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-info">
                <h3 class="card-title"><i class="fas fa-user-clock"></i> Postulaciones</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <div class="info-box bg-secondary">
                            <span class="info-box-icon"><i class="fas fa-list"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total</span>
                                <span class="info-box-number">{{ $estadisticas['postulaciones']['total'] }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="info-box bg-warning">
                            <span class="info-box-icon"><i class="fas fa-clock"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Pendientes</span>
                                <span class="info-box-number">{{ $estadisticas['postulaciones']['pendientes'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <h5>Por Estado:</h5>
                <canvas id="chartPostulaciones" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Lesiones -->
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-danger">
                <h3 class="card-title"><i class="fas fa-band-aid"></i> Lesiones</h3>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <div class="info-box bg-danger">
                            <span class="info-box-icon"><i class="fas fa-exclamation-triangle"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Lesiones</span>
                                <span class="info-box-number">{{ $estadisticas['lesiones']['total'] }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box bg-warning">
                            <span class="info-box-icon"><i class="fas fa-heartbeat"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Activas</span>
                                <span class="info-box-number">{{ $estadisticas['lesiones']['activas'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <h5>Por Tipo:</h5>
                <canvas id="chartLesiones" height="100"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Gráfico de Usuarios por Rol
const ctxRol = document.getElementById('chartUsuariosRol').getContext('2d');
new Chart(ctxRol, {
    type: 'pie',
    data: {
        labels: {!! json_encode($estadisticas['usuarios']['por_rol']->keys()) !!},
        datasets: [{
            data: {!! json_encode($estadisticas['usuarios']['por_rol']->values()) !!},
            backgroundColor: ['#007bff', '#28a745', '#ffc107', '#dc3545', '#17a2b8']
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom' }
        }
    }
});

// Gráfico de Niveles
const ctxNiveles = document.getElementById('chartNiveles').getContext('2d');
new Chart(ctxNiveles, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($estadisticas['niveles']['por_nivel']->keys()) !!},
        datasets: [{
            data: {!! json_encode($estadisticas['niveles']['por_nivel']->values()) !!},
            backgroundColor: ['#6c757d', '#17a2b8', '#ffc107', '#dc3545']
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom' }
        }
    }
});

// Gráfico de Sesiones
const ctxSesiones = document.getElementById('chartSesiones').getContext('2d');
new Chart(ctxSesiones, {
    type: 'bar',
    data: {
        labels: ['Programadas', 'Completadas', 'Canceladas'],
        datasets: [{
            label: 'Sesiones',
            data: [
                {{ $estadisticas['sesiones']['programadas'] }},
                {{ $estadisticas['sesiones']['completadas'] }},
                {{ $estadisticas['sesiones']['canceladas'] }}
            ],
            backgroundColor: ['#007bff', '#28a745', '#dc3545']
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: { beginAtZero: true }
        }
    }
});

// Gráfico de Postulaciones
const ctxPostulaciones = document.getElementById('chartPostulaciones').getContext('2d');
new Chart(ctxPostulaciones, {
    type: 'pie',
    data: {
        labels: ['Pendientes', 'Aprobadas', 'Rechazadas'],
        datasets: [{
            data: [
                {{ $estadisticas['postulaciones']['pendientes'] }},
                {{ $estadisticas['postulaciones']['aprobadas'] }},
                {{ $estadisticas['postulaciones']['rechazadas'] }}
            ],
            backgroundColor: ['#ffc107', '#28a745', '#dc3545']
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom' }
        }
    }
});

// Gráfico de Lesiones
const ctxLesiones = document.getElementById('chartLesiones').getContext('2d');
new Chart(ctxLesiones, {
    type: 'bar',
    data: {
        labels: {!! json_encode($estadisticas['lesiones']['por_tipo']->keys()) !!},
        datasets: [{
            label: 'Cantidad',
            data: {!! json_encode($estadisticas['lesiones']['por_tipo']->values()) !!},
            backgroundColor: '#dc3545'
        }]
    },
    options: {
        responsive: true,
        indexAxis: 'y',
        plugins: {
            legend: { display: false }
        },
        scales: {
            x: { beginAtZero: true }
        }
    }
});
</script>
@endsection
