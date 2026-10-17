@extends('layouts.medico')

@section('title', 'Perfil Médico')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-info">
                    <h3 class="card-title text-white">
                        <i class="fas fa-user-md"></i> Perfil Médico - {{ $gimnasta->Nombre }} {{ $gimnasta->Apellido }}
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center mb-4">
                                <i class="fas fa-user-circle fa-5x text-muted"></i>
                                <h4 class="mt-2">{{ $gimnasta->Nombre }} {{ $gimnasta->Apellido }}</h4>
                                <span class="badge badge-lg 
                                    @if($gimnasta->Nivel == 'Elite') badge-success
                                    @elseif($gimnasta->Nivel == 'Avanzado') badge-warning  
                                    @elseif($gimnasta->Nivel == 'Intermedio') badge-info
                                    @else badge-secondary @endif">
                                    {{ $gimnasta->Nivel }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5><i class="fas fa-info-circle"></i> Datos Personales</h5>
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>Edad:</strong></td>
                                            <td>{{ \Carbon\Carbon::parse($gimnasta->FechaNacimiento)->age }} años</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Fecha Nacimiento:</strong></td>
                                            <td>{{ \Carbon\Carbon::parse($gimnasta->FechaNacimiento)->format('d/m/Y') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Nacionalidad:</strong></td>
                                            <td>{{ $gimnasta->Nacionalidad }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Email:</strong></td>
                                            <td>{{ $gimnasta->Email }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h5><i class="fas fa-heartbeat"></i> Datos Físicos</h5>
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>Peso:</strong></td>
                                            <td>{{ $gimnasta->Peso }} kg</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Altura:</strong></td>
                                            <td>{{ $gimnasta->Altura }} m</td>
                                        </tr>
                                        <tr>
                                            <td><strong>IMC:</strong></td>
                                            <td>
                                                @php
                                                    $imc = ($gimnasta->Altura > 0) ? round($gimnasta->Peso / ($gimnasta->Altura * $gimnasta->Altura), 2) : 0;
                                                @endphp
                                                {{ $imc }}
                                                @if($imc < 18.5 && $imc > 0)
                                                    <span class="badge badge-warning">Bajo peso</span>
                                                @elseif($imc >= 18.5 && $imc < 25)
                                                    <span class="badge badge-success">Normal</span>
                                                @elseif($imc >= 25 && $imc < 30)
                                                    <span class="badge badge-warning">Sobrepeso</span>
                                                @elseif($imc >= 30)
                                                    <span class="badge badge-danger">Obesidad</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Estado:</strong></td>
                                            <td>
                                                <span class="badge {{ $gimnasta->activo ? 'badge-success' : 'badge-danger' }}">
                                                    {{ $gimnasta->activo ? 'Activo' : 'Inactivo' }}
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($gimnasta->entrenador)
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <h5><i class="fas fa-user-tie"></i> Entrenador Asignado</h5>
                                    <p class="mb-1"><strong>{{ $gimnasta->entrenador->Nombre }} {{ $gimnasta->entrenador->Apellido }}</strong></p>
                                    <p class="mb-0"><i class="fas fa-envelope"></i> {{ $gimnasta->entrenador->Email }}</p>
                                    @if($gimnasta->entrenador->Contacto)
                                        <p class="mb-0"><i class="fas fa-phone"></i> {{ $gimnasta->entrenador->Contacto }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-success">
                                    <h5 class="card-title text-white mb-0"><i class="fas fa-chart-bar"></i> Estadísticas Deportivas</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3 col-6">
                                            <div class="info-box bg-primary">
                                                <span class="info-box-icon"><i class="fas fa-dumbbell"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Total Sesiones</span>
                                                    <span class="info-box-number">{{ $estadisticas['total_sesiones'] }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-6">
                                            <div class="info-box bg-success">
                                                <span class="info-box-icon"><i class="fas fa-check-circle"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Completadas</span>
                                                    <span class="info-box-number">{{ $estadisticas['sesiones_completadas'] }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-6">
                                            <div class="info-box bg-warning">
                                                <span class="info-box-icon"><i class="fas fa-calendar-alt"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Programadas</span>
                                                    <span class="info-box-number">{{ $estadisticas['sesiones_programadas'] }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-6">
                                            <div class="info-box {{ $estadisticas['lesiones_activas'] > 0 ? 'bg-danger' : 'bg-info' }}">
                                                <span class="info-box-icon"><i class="fas fa-heartbeat"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Lesiones Activas</span>
                                                    <span class="info-box-number">{{ $estadisticas['lesiones_activas'] }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-md-3 col-6">
                                            <div class="small-box bg-info">
                                                <div class="inner">
                                                    <h3>{{ $estadisticas['sesiones_este_mes'] }}</h3>
                                                    <p>Sesiones este Mes</p>
                                                </div>
                                                <div class="icon"><i class="fas fa-calendar-week"></i></div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-6">
                                            <div class="small-box bg-secondary">
                                                <div class="inner">
                                                    <h3>{{ $estadisticas['promedio_duracion'] ? round($estadisticas['promedio_duracion']) : 0 }} <small>min</small></h3>
                                                    <p>Duración Promedio</p>
                                                </div>
                                                <div class="icon"><i class="fas fa-clock"></i></div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-6">
                                            <div class="small-box bg-warning">
                                                <div class="inner">
                                                    <h3>{{ $gimnasta->Nivel }}</h3>
                                                    <p>Nivel Actual</p>
                                                </div>
                                                <div class="icon"><i class="fas fa-trophy"></i></div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-6">
                                            <div class="small-box bg-success">
                                                <div class="inner">
                                                    <h3>{{ \Carbon\Carbon::parse($gimnasta->FechaNacimiento)->age }}</h3>
                                                    <p>Edad</p>
                                                </div>
                                                <div class="icon"><i class="fas fa-birthday-cake"></i></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header bg-gradient-primary">
                                    <h5 class="card-title text-white mb-0"><i class="fas fa-chart-pie"></i> Rendimiento por Aparato</h5>
                                </div>
                                <div class="card-body">
                                    <div id="radarChart"></div>
                                    @if($aparatosSesiones->count() == 0)
                                        <p class="text-muted text-center mb-0">No hay sesiones registradas en aparatos</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header bg-gradient-warning">
                                    <h5 class="card-title text-white mb-0"><i class="fas fa-chart-line"></i> Tendencia Mensual (6 meses)</h5>
                                </div>
                                <div class="card-body">
                                    <div id="barChart"></div>
                                    @if($sesionesMensuales->count() == 0)
                                        <p class="text-muted text-center mb-0">No hay sesiones en los últimos 6 meses</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($evaluacionNutricional || $evaluacionPsicologica || $evaluacionFisiologica)
                    <div class="row mt-2">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-gradient-secondary">
                                    <h5 class="card-title text-white mb-0"><i class="fas fa-clipboard-check"></i> Evaluaciones de Aptitud</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        @if($evaluacionNutricional)
                                        <div class="col-md-4">
                                            <h6><i class="fas fa-apple-alt text-success"></i> Nutricional</h6>
                                            <table class="table table-sm table-borderless">
                                                <tr><td>IMC:</td><td><strong>{{ $evaluacionNutricional->IMC ?? '-' }}</strong></td></tr>
                                                <tr><td>% Grasa:</td><td><strong>{{ $evaluacionNutricional->PorcentajeGrasa ?? '-' }}%</strong></td></tr>
                                                <tr><td>Masa Muscular:</td><td><strong>{{ $evaluacionNutricional->MasaMuscular ?? '-' }}%</strong></td></tr>
                                                <tr><td>Hidratación:</td><td><strong>{{ $evaluacionNutricional->NivelHidratacion ?? '-' }}</strong></td></tr>
                                            </table>
                                        </div>
                                        @endif
                                        @if($evaluacionPsicologica)
                                        <div class="col-md-4">
                                            <h6><i class="fas fa-brain text-info"></i> Psicológica</h6>
                                            <table class="table table-sm table-borderless">
                                                <tr><td>Ansiedad:</td><td><strong>{{ $evaluacionPsicologica->NivelAnsiedad ?? '-' }}/10</strong></td></tr>
                                                <tr><td>Estrés:</td><td><strong>{{ $evaluacionPsicologica->NivelEstres ?? '-' }}/10</strong></td></tr>
                                                <tr><td>Motivación:</td><td><strong>{{ $evaluacionPsicologica->NivelMotivacion ?? '-' }}/10</strong></td></tr>
                                                <tr><td>Concentración:</td><td><strong>{{ $evaluacionPsicologica->Concentracion ?? '-' }}/10</strong></td></tr>
                                            </table>
                                        </div>
                                        @endif
                                        @if($evaluacionFisiologica)
                                        <div class="col-md-4">
                                            <h6><i class="fas fa-heart text-danger"></i> Fisiológica</h6>
                                            <table class="table table-sm table-borderless">
                                                <tr><td>FC Reposo:</td><td><strong>{{ $evaluacionFisiologica->FCReposo ?? '-' }} lpm</strong></td></tr>
                                                <tr><td>FC Esfuerzo:</td><td><strong>{{ $evaluacionFisiologica->FCEsfuerzo ?? '-' }} lpm</strong></td></tr>
                                                <tr><td>Presión:</td><td><strong>{{ $evaluacionFisiologica->PresionSistolica ?? '-' }}/{{ $evaluacionFisiologica->PresionDiastolica ?? '-' }}</strong></td></tr>
                                                <tr><td>Sat. O₂:</td><td><strong>{{ $evaluacionFisiologica->SaturacionOxigeno ?? '-' }}%</strong></td></tr>
                                            </table>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="text-center mt-4">
                        <a href="{{ route('medico.historial', $gimnasta->ID_Usuario) }}" class="btn btn-primary">
                            <i class="fas fa-clipboard-list"></i> Ver Historial Médico
                        </a>
                        <a href="{{ route('admin.reportes.exportar-gimnasta', $gimnasta->ID_Usuario) }}" class="btn btn-success" target="_blank">
                            <i class="fas fa-file-pdf"></i> Descargar Reporte PDF
                        </a>
                        <a href="{{ route('medico.dashboard') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if($aparatosSesiones->count() > 0)
    var radarOptions = {
        series: [{
            name: 'Sesiones',
            data: [{{ $aparatosSesiones->pluck('total')->implode(',') }}]
        }],
        chart: { type: 'radar', height: 300, toolbar: { show: false } },
        xaxis: {
            categories: [@foreach($aparatosSesiones as $a)'{{ $a->aparato->Nombre ?? "Sin aparato" }}',@endforeach]
        },
        yaxis: { tickAmount: 3 },
        stroke: { show: true, width: 2, colors: ['#0d6efd'] },
        fill: { opacity: 0.3, colors: ['#0d6efd'] },
        markers: { size: 4, colors: ['#0d6efd'] },
        plotOptions: {
            radar: {
                polygons: {
                    strokeColors: '#e8e8e8',
                    connectorColors: '#e8e8e8',
                    fill: { colors: ['#f8f9fa', '#e9ecef'] }
                }
            }
        }
    };
    new ApexCharts(document.querySelector("#radarChart"), radarOptions).render();
    @endif

    @if($sesionesMensuales->count() > 0)
    var meses = [@foreach($sesionesMensuales as $mes => $total)'{{ \Carbon\Carbon::parse($mes.'-01')->format('M Y') }}',@endforeach];
    var valores = [{{ $sesionesMensuales->values()->implode(',') }}];
    var barOptions = {
        series: [{ name: 'Sesiones', data: valores }],
        chart: { type: 'bar', height: 300, toolbar: { show: false } },
        xaxis: { categories: meses },
        yaxis: { tickAmount: 3, min: 0 },
        colors: ['#ffc107'],
        plotOptions: {
            bar: {
                borderRadius: 4,
                columnWidth: '60%',
                distributed: false
            }
        },
        dataLabels: { enabled: true, style: { fontSize: '11px' } }
    };
    new ApexCharts(document.querySelector("#barChart"), barOptions).render();
    @endif
});
</script>
@endsection