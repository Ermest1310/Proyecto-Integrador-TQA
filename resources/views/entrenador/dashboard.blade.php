@extends('layouts.entrenador')

@section('title', 'Dashboard Entrenador')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-primary">
                <div class="card-body text-white">
                    <h2><i class="fas fa-dumbbell"></i> Bienvenido, {{ session('usuario_nombre') }}</h2>
                    <p class="mb-0">Panel de Control - Entrenador</p>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="info-box bg-success">
                <span class="info-box-icon"><i class="fas fa-check-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Completadas</span>
                    <span class="info-box-number">{{ $estadisticas['sesiones_completadas'] }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-box bg-warning">
                <span class="info-box-icon"><i class="fas fa-calendar"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Programadas</span>
                    <span class="info-box-number">{{ $estadisticas['sesiones_programadas'] }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-box bg-primary">
                <span class="info-box-icon"><i class="fas fa-dumbbell"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Sesiones</span>
                    <span class="info-box-number">{{ $estadisticas['total_sesiones'] }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-calendar-alt"></i> Próximas Sesiones</h3>
                </div>
                <div class="card-body">
                    @if($proximasSesiones->count() > 0)
                        <div class="timeline">
                            @foreach($proximasSesiones as $sesion)
                                <div class="time-label">
                                    <span class="bg-primary">{{ $sesion->Fecha->format('d/m/Y') }}</span>
                                </div>
                                <div>
                                    <i class="fas fa-dumbbell bg-info"></i>
                                    <div class="timeline-item">
                                        <span class="time"><i class="fas fa-clock"></i> {{ $sesion->Hora_Inicio->format('H:i') }} - {{ $sesion->Hora_Fin->format('H:i') }}</span>
                                        <h3 class="timeline-header">
                                            <strong>{{ $sesion->gimnasta->Nombre }} {{ $sesion->gimnasta->Apellido }}</strong>
                                        </h3>
                                        <div class="timeline-body">
                                            <i class="fas fa-dumbbell"></i> {{ $sesion->aparato->Nombre ?? 'Entrenamiento general' }}
                                            <br>
                                            <i class="fas fa-clock"></i> Duración: {{ $sesion->Duracion_Minutos }} minutos
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                            <h5>No tienes sesiones programadas</h5>
                            <p class="text-muted">Tus próximas sesiones aparecerán aquí</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-plus"></i> Acciones Rápidas</h3>
                </div>
                <div class="card-body">

                    <a href="{{ route('perfil.show') }}" class="btn btn-info btn-block mb-3">
                        <i class="fas fa-user"></i> Mi Perfil
                    </a>
                    <div class="mt-4">
                        <h6><i class="fas fa-chart-bar"></i> Resumen de Actividad</h6>
                        <div class="progress-group">
                            <span class="float-right"><b>{{ $estadisticas['sesiones_completadas'] }}</b>/{{ $estadisticas['total_sesiones'] }}</span>
                            <span class="progress-text">Sesiones Completadas</span>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-success" style="width: {{ $estadisticas['total_sesiones'] > 0 ? ($estadisticas['sesiones_completadas'] / $estadisticas['total_sesiones']) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                        <div class="progress-group">
                            <span class="float-right"><b>{{ $estadisticas['sesiones_programadas'] }}</b></span>
                            <span class="progress-text">Sesiones Pendientes</span>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-warning" style="width: {{ $estadisticas['sesiones_programadas'] > 0 ? min(($estadisticas['sesiones_programadas'] / 10) * 100, 100) : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection