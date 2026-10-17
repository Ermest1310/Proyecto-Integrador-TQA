@extends('layouts.gimnasta')

@section('title', 'Inicio - Gimnasta')

@section('content')
<!-- Dashboard actualizado -->
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            @if($gimnasta->tieneLesionActiva())
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <h5><i class="fas fa-exclamation-triangle"></i> Lesión Activa</h5>
                    <p class="mb-0">Tienes una lesión activa. No puedes crear nuevas sesiones de entrenamiento hasta que te recuperes.</p>
                </div>
            @endif
            <div class="card bg-gradient-success">
                <div class="card-body text-white">
                    <h2><i class="fas fa-medal"></i> ¡Hola, {{ $gimnasta->Nombre }}!</h2>
                    <p class="mb-0">Tu espacio personal de entrenamiento</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas principales -->
    <div class="row mb-4">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $estadisticas['sesiones_completadas'] }}</h3>
                    <p>Sesiones Completadas</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <a href="{{ route('gimnasta.historial', ['estado' => 'Completada']) }}" class="small-box-footer">
                    Ver historial <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $estadisticas['sesiones_programadas'] }}</h3>
                    <p>Sesiones Programadas</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <a href="{{ route('gimnasta.sesiones', ['estado' => 'Programada']) }}" class="small-box-footer">
                    Ver sesiones <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $estadisticas['sesiones_este_mes'] }}</h3>
                    <p>Este Mes</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <a href="{{ route('gimnasta.calendario') }}" class="small-box-footer">
                    Ver calendario <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-purple">
                <div class="inner">
                    <h3>{{ $gimnasta->Nivel }}</h3>
                    <p>Tu Nivel</p>
                </div>
                <div class="icon">
                    <i class="fas fa-star"></i>
                </div>
                <a href="{{ route('gimnasta.progreso') }}" class="small-box-footer">
                    Ver progreso <i class="fas fa-arrow-circle-right"></i>
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
                        <a href="{{ route('gimnasta.crear-sesion') }}" class="btn btn-success btn-sm">
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
                                            <i class="fas fa-dumbbell text-warning"></i>
                                            {{ $sesion->aparato ? $sesion->aparato->Nombre : 'Entrenamiento general' }}
                                        </h6>
                                        <p class="mb-1">
                                            <i class="fas fa-calendar text-primary"></i> {{ $sesion->Fecha->format('d/m/Y') }}
                                            <i class="fas fa-clock text-info ml-2"></i> {{ $sesion->Hora_Inicio->format('H:i') }} - {{ $sesion->Hora_Fin->format('H:i') }}
                                        </p>
                                        <small class="text-muted">
                                            <i class="fas fa-user-tie"></i> {{ $sesion->entrenador->Nombre }}
                                        </small>
                                    </div>
                                    <span class="badge badge-primary">{{ $sesion->Fecha->diffForHumans() }}</span>
                                </li>
                            @endforeach
                        </ul>
                        <div class="card-footer text-center">
                            <a href="{{ route('gimnasta.sesiones') }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i> Ver todas las sesiones
                            </a>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-calendar-times fa-2x text-muted mb-2"></i>
                            <p class="text-muted">No tienes sesiones programadas</p>
                            <a href="{{ route('gimnasta.crear-sesion') }}" class="btn btn-primary">
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
                        <a href="{{ route('gimnasta.historial') }}" class="btn btn-outline-secondary btn-sm">
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
                                                {{ $sesion->aparato ? $sesion->aparato->Nombre : 'Entrenamiento general' }}
                                            </h6>
                                            <p class="mb-1">
                                                <i class="fas fa-calendar text-primary"></i> {{ $sesion->Fecha->format('d/m/Y') }}
                                                <i class="fas fa-clock text-info ml-2"></i> {{ $sesion->Duracion_Minutos }} min
                                            </p>
                                            <small class="text-muted">
                                                <i class="fas fa-user-tie"></i> {{ $sesion->entrenador->Nombre }}
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
                            <p class="text-muted">Aún no tienes actividad</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Información Personal y Acciones -->
    <div class="row mt-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-user"></i> Mi Información</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-weight"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Peso</span>
                                    <span class="info-box-number">{{ $gimnasta->Peso }}kg</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning"><i class="fas fa-ruler-vertical"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Altura</span>
                                    <span class="info-box-number">{{ $gimnasta->Altura }}m</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-purple"><i class="fas fa-birthday-cake"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Edad</span>
                                    <span class="info-box-number">{{ \Carbon\Carbon::parse($gimnasta->FechaNacimiento)->age }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fas fa-flag"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">País</span>
                                    <span class="info-box-number" style="font-size: 12px;">{{ $gimnasta->Nacionalidad }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($gimnasta->entrenador)
                        <div class="alert alert-info">
                            <h5><i class="fas fa-user-tie"></i> Tu Entrenador</h5>
                            <p class="mb-1"><strong>{{ $gimnasta->entrenador->Nombre }} {{ $gimnasta->entrenador->Apellido }}</strong></p>
                            <p class="mb-0"><i class="fas fa-envelope"></i> {{ $gimnasta->entrenador->Email }}</p>
                            @if($gimnasta->entrenador->Contacto)
                                <p class="mb-0"><i class="fas fa-phone"></i> {{ $gimnasta->entrenador->Contacto }}</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-rocket"></i> Acciones Rápidas</h3>
                </div>
                <div class="card-body">
                    <a href="{{ route('gimnasta.crear-sesion') }}" class="btn btn-success btn-block mb-2">
                        <i class="fas fa-plus"></i> Nueva Sesión
                    </a>
                    <a href="{{ route('gimnasta.calendario') }}" class="btn btn-primary btn-block mb-2">
                        <i class="fas fa-calendar"></i> Mi Calendario
                    </a>
                    <a href="{{ route('gimnasta.progreso') }}" class="btn btn-info btn-block mb-2">
                        <i class="fas fa-chart-line"></i> Mi Progreso
                    </a>
                    <a href="{{ route('perfil.show') }}" class="btn btn-outline-secondary btn-block">
                        <i class="fas fa-user-edit"></i> Editar Perfil
                    </a>
                    
                    <div class="mt-3">
                        <h6><i class="fas fa-trophy"></i> Motivación</h6>
                        <div class="callout callout-success">
                            <p class="mb-0 small">"El éxito no es el final, el fracaso no es fatal: es el coraje de continuar lo que cuenta."</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection