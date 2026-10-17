@extends('layouts.admin')

@section('title', 'Dashboard Administrativo')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-primary">
                <div class="card-body text-white">
                    <h2><i class="fas fa-user-shield"></i> ¡Bienvenido, Administrador!</h2>
                    <p class="mb-0">Panel de control del sistema de gestión deportiva</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas principales -->
    <div class="row mb-4">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $estadisticas['total_usuarios'] }}</h3>
                    <p>Usuarios Activos</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <a href="{{ route('usuarios-unificado.index') }}" class="small-box-footer">
                    Ver usuarios <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $estadisticas['sesiones_completadas'] }}</h3>
                    <p>Sesiones Completadas</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <a href="{{ route('sesiones.historial', ['estado' => 'Completada']) }}" class="small-box-footer">
                    Ver historial <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $estadisticas['sesiones_programadas'] }}</h3>
                    <p>Sesiones Programadas</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <a href="{{ route('sesiones.index', ['estado' => 'Programada']) }}" class="small-box-footer">
                    Ver sesiones <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $estadisticas['sesiones_hoy'] }}</h3>
                    <p>Sesiones Hoy</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <a href="{{ route('sesiones.index', ['fecha_desde' => '2025-01-02', 'fecha_hasta' => '2025-01-02']) }}" class="small-box-footer">
                    Ver de hoy <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Próximas Sesiones -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-calendar-check"></i> Próximas Sesiones</h3>
                    <div class="card-tools">
                        <a href="{{ route('sesiones.create') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-plus"></i> Nueva
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($proximasSesiones->count() > 0)
                        <ul class="list-group list-group-flush">
                            @foreach($proximasSesiones as $sesion)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">
                                            <i class="fas fa-user text-primary"></i>
                                            {{ $sesion->gimnasta->Nombre }} {{ $sesion->gimnasta->Apellido }}
                                        </h6>
                                        <p class="mb-1">
                                            <i class="fas fa-dumbbell text-warning"></i> {{ $sesion->aparato ? $sesion->aparato->Nombre : 'General' }}
                                            <i class="fas fa-user-tie text-success ml-2"></i> {{ $sesion->entrenador->Nombre }}
                                        </p>
                                        <small class="text-muted">
                                            <i class="fas fa-calendar"></i> {{ $sesion->Fecha->format('d/m/Y') }}
                                            <i class="fas fa-clock ml-2"></i> {{ $sesion->Hora_Inicio->format('H:i') }} - {{ $sesion->Hora_Fin->format('H:i') }}
                                        </small>
                                    </div>
                                    <span class="badge badge-primary">{{ $sesion->Fecha->diffForHumans() }}</span>
                                </li>
                            @endforeach
                        </ul>
                        <div class="card-footer text-center">
                            <a href="{{ route('sesiones.index') }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i> Ver todas las sesiones
                            </a>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-calendar-times fa-2x text-muted mb-2"></i>
                            <p class="text-muted">No hay sesiones programadas</p>
                            <a href="{{ route('sesiones.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Programar Sesión
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Actividad Reciente -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-history"></i> Actividad Reciente</h3>
                    <div class="card-tools">
                        <a href="{{ route('sesiones.historial') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-eye"></i> Ver todo
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($actividadReciente->count() > 0)
                        <ul class="list-group list-group-flush">
                            @foreach($actividadReciente as $sesion)
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">
                                                <i class="fas fa-check-circle text-success"></i>
                                                {{ $sesion->gimnasta->Nombre }} {{ $sesion->gimnasta->Apellido }}
                                            </h6>
                                            <p class="mb-1">
                                                <i class="fas fa-dumbbell text-warning"></i> {{ $sesion->aparato ? $sesion->aparato->Nombre : 'General' }}
                                                <i class="fas fa-user-tie text-success ml-2"></i> {{ $sesion->entrenador->Nombre }}
                                            </p>
                                            <small class="text-muted">
                                                <i class="fas fa-calendar"></i> {{ $sesion->Fecha->format('d/m/Y') }}
                                                <i class="fas fa-clock ml-2"></i> {{ $sesion->Duracion_Minutos }} min
                                            </small>
                                        </div>
                                        <span class="badge badge-success">Completada</span>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-history fa-2x text-muted mb-2"></i>
                            <p class="text-muted">No hay actividad reciente</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas adicionales -->
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-primary"><i class="fas fa-user-friends"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Gimnastas</span>
                    <span class="info-box-number">{{ $estadisticas['total_gimnastas'] }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-success"><i class="fas fa-user-tie"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Entrenadores</span>
                    <span class="info-box-number">{{ $estadisticas['total_entrenadores'] }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-warning"><i class="fas fa-dumbbell"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Aparatos Operativos</span>
                    <span class="info-box-number">{{ $estadisticas['aparatos_operativos'] }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-danger"><i class="fas fa-heartbeat"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Lesiones Activas</span>
                    <span class="info-box-number">{{ $estadisticas['lesiones_activas'] }}</span>
                </div>
            </div>
        </div>
    </div>


</div>
@endsection

@section('scripts')
@endsection
