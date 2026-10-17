@extends('layouts.admin')

@section('title', 'Área Médica Deportiva')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <h3><i class="fas fa-notes-medical text-info"></i> Área Médica Deportiva</h3>
            <p class="text-muted">Selecciona un área para ver los especialistas y consultas disponibles.</p>
        </div>
    </div>

    <div class="row">

        <!-- Tarjeta de Fisioterapia -->
        <div class="col-md-4">
            <a href="{{ route('areamedica.fisioterapia') }}" class="text-decoration-none">
                <div class="card shadow-sm border-info h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-running fa-3x text-info mb-3"></i>
                        <h5 class="card-title">Fisioterapia Deportiva</h5>
                        <p class="card-text text-muted">Tratamiento de lesiones, recuperación muscular y rehabilitación física.</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Tarjeta de Nutrición -->
        <div class="col-md-4">
            <a href="{{ route('areamedica.nutricion') }}" class="text-decoration-none">
                <div class="card shadow-sm border-success h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-apple-alt fa-3x text-success mb-2"></i>
                        <h5 class="card-title">Nutrición Deportiva</h5>
                        <p class="card-text text-muted">Planes alimenticios para rendimiento y recuperación.</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Tarjeta de Psicología -->
        <div class="col-md-4">
            <a href="{{ route('areamedica.psicologia') }}" class="text-decoration-none">
                <div class="card shadow-sm border-warning h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-brain fa-3x text-warning mb-2"></i>
                        <h5 class="card-title">Psicología Deportiva</h5>
                        <p class="card-text text-muted">Apoyo mental, motivación y manejo del estrés competitivo.</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Tarjeta de Medicina General -->
        <div class="col-md-4 mt-3">
            <a href="{{ route('areamedica.medicina') }}" class="text-decoration-none">
                <div class="card shadow-sm border-danger h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-user-md fa-3x text-danger mb-2"></i>
                        <h5 class="card-title">Medicina General</h5>
                        <p class="card-text text-muted">Control de salud general y atención de lesiones leves.</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Tarjeta de Rehabilitación -->
        <div class="col-md-4 mt-3">
            <a href="{{ route('areamedica.rehabilitacion') }}" class="text-decoration-none">
                <div class="card shadow-sm border-primary h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-dumbbell fa-3x text-primary mb-2"></i>
                        <h5 class="card-title">Rehabilitación Física</h5>
                        <p class="card-text text-muted">Recuperación de lesiones graves y entrenamientos guiados.</p>
                    </div>
                </div>
            </a>
        </div>

    </div>
</div>
@endsection
