@extends('layouts.entrenador')

@section('title', 'Dashboard - ' . $gimnasta->Nombre)

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-info">
                <div class="card-body text-white">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2><i class="fas fa-user-athlete"></i> {{ $gimnasta->Nombre }} {{ $gimnasta->Apellido }}</h2>
                            <p class="mb-0">Dashboard del Gimnasta - Nivel: {{ $gimnasta->Nivel ?? 'Sin definir' }}</p>
                        </div>
                        <div class="col-md-4 text-right">
                            <a href="{{ route('admin.reportes.exportar-gimnasta', $gimnasta->ID_Usuario) }}" class="btn btn-light mr-2" target="_blank">
                                <i class="fas fa-file-pdf"></i> Reporte PDF
                            </a>
                            <a href="{{ route('entrenador.gimnastas') }}" class="btn btn-light">
                                <i class="fas fa-arrow-left"></i> Volver a Mis Gimnastas
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="info-box bg-primary">
                <span class="info-box-icon"><i class="fas fa-dumbbell"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Sesiones</span>
                    <span class="info-box-number">{{ $estadisticas['total_sesiones'] }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box bg-success">
                <span class="info-box-icon"><i class="fas fa-check-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Completadas</span>
                    <span class="info-box-number">{{ $estadisticas['sesiones_completadas'] }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box bg-warning">
                <span class="info-box-icon"><i class="fas fa-calendar"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Programadas</span>
                    <span class="info-box-number">{{ $estadisticas['sesiones_programadas'] }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box {{ $estadisticas['lesiones_activas'] > 0 ? 'bg-danger' : 'bg-success' }}">
                <span class="info-box-icon"><i class="fas fa-heartbeat"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Lesiones Activas</span>
                    <span class="info-box-number">{{ $estadisticas['lesiones_activas'] }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-calendar-check"></i> Próximas Sesiones</h3>
                </div>
                <div class="card-body">
                    @if($proximasSesiones->count() > 0)
                        @foreach($proximasSesiones as $sesion)
                            <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                                <div>
                                    <strong>{{ $sesion->Fecha->format('d/m/Y') }}</strong>
                                    <br>
                                    <small class="text-muted">
                                        <i class="fas fa-clock"></i> {{ $sesion->Hora_Inicio->format('H:i') }} - {{ $sesion->Hora_Fin->format('H:i') }}
                                    </small>
                                </div>
                                <div class="text-right">
                                    <span class="badge badge-info">{{ $sesion->aparato->Nombre ?? 'General' }}</span>
                                    <br>
                                    <small>{{ $sesion->Duracion_Minutos }} min</small>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-calendar-times fa-2x text-muted mb-2"></i>
                            <p class="text-muted">No hay sesiones programadas</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-history"></i> Últimas Sesiones</h3>
                </div>
                <div class="card-body">
                    @if($ultimasSesiones->count() > 0)
                        @foreach($ultimasSesiones as $sesion)
                            <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                                <div>
                                    <strong>{{ $sesion->Fecha->format('d/m/Y') }}</strong>
                                    <br>
                                    <small class="text-muted">
                                        <i class="fas fa-clock"></i> {{ $sesion->Hora_Inicio->format('H:i') }} - {{ $sesion->Hora_Fin->format('H:i') }}
                                    </small>
                                </div>
                                <div class="text-right">
                                    <span class="badge badge-{{ $sesion->Estado == 'Completada' ? 'success' : ($sesion->Estado == 'Programada' ? 'warning' : 'danger') }}">
                                        {{ $sesion->Estado }}
                                    </span>
                                    <br>
                                    <small>{{ $sesion->aparato->Nombre ?? 'General' }}</small>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-history fa-2x text-muted mb-2"></i>
                            <p class="text-muted">No hay sesiones registradas</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($gimnasta->lesiones->count() > 0)
        <div class="row mt-4">
            <div class="col-12">
                <div class="card border-warning">
                    <div class="card-header bg-warning">
                        <h3 class="card-title"><i class="fas fa-exclamation-triangle"></i> Lesiones Activas</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($gimnasta->lesiones as $lesion)
                                <div class="col-md-6 mb-3">
                                    <div class="alert alert-warning">
                                        <strong>{{ $lesion->Tipo }}</strong> - {{ $lesion->ParteCuerpo }}
                                        <br>
                                        <small>Fecha: {{ $lesion->FechaLesion->format('d/m/Y') }}</small>
                                        @if($lesion->Descripcion)
                                            <br>
                                            <small>{{ $lesion->Descripcion }}</small>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection