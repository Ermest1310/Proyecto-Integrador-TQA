@extends('layouts.gimnasta')

@section('title', 'Mi Entrenador')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-info">
                <div class="card-body text-white">
                    <h2><i class="fas fa-user-tie"></i> Mis Entrenadores</h2>
                    <p class="mb-0">Estadísticas de entrenamiento por entrenador</p>
                </div>
            </div>
        </div>
    </div>

    @if($estadisticasEntrenadores->count() > 0)
        <div class="row">
            @foreach($estadisticasEntrenadores as $stat)
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header bg-primary">
                            <h5 class="card-title text-white mb-0">
                                <i class="fas fa-user-tie"></i> 
                                {{ $stat->entrenador->Nombre }} {{ $stat->entrenador->Apellido }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <div class="info-box bg-info">
                                        <span class="info-box-icon"><i class="fas fa-calendar"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Total Sesiones</span>
                                            <span class="info-box-number">{{ $stat->total_sesiones }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="info-box bg-success">
                                        <span class="info-box-icon"><i class="fas fa-check"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Completadas</span>
                                            <span class="info-box-number">{{ $stat->sesiones_completadas }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="info-box bg-warning">
                                        <span class="info-box-icon"><i class="fas fa-clock"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Programadas</span>
                                            <span class="info-box-number">{{ $stat->sesiones_programadas }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-3">
                                <h6><i class="fas fa-envelope"></i> Contacto</h6>
                                <p class="mb-1"><strong>Email:</strong> {{ $stat->entrenador->Email }}</p>
                                @if($stat->entrenador->Contacto)
                                    <p class="mb-0"><strong>Teléfono:</strong> {{ $stat->entrenador->Contacto }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="fas fa-calendar-times fa-5x text-muted mb-4"></i>
                        <h4>No tienes sesiones activas</h4>
                        <p class="text-muted">Programa tu primera sesión para ver estadísticas de entrenadores.</p>
                        <a href="{{ route('gimnasta.crear-sesion') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Programar Sesión
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection