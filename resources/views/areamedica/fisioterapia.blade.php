@extends('layouts.medico')

@section('title', 'Fisioterapia Deportiva')

@section('content')
<div class="container-fluid">
    <!-- Título y descripción -->
    <div class="card card-info mb-3">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-running"></i> Fisioterapia Deportiva</h3>
        </div>
        <div class="card-body">
            <p>
                    En nuestra Área de Fisioterapia Deportiva, cada sesión está diseñada para impulsar tu potencial al máximo. 
    Aquí no solo tratamos lesiones, sino que fortalecemos tu cuerpo, mejoramos tu movilidad y prevenimos futuras lesiones. 
    Con la guía de especialistas calificados, cada ejercicio y cada tratamiento se adapta a tus necesidades, asegurando un rendimiento óptimo y un regreso seguro a tu entrenamiento.
        </div>
    </div>

    <!-- Especialistas -->
    <div class="row mb-3">
        <div class="col-12">
            <h4><i class="fas fa-user-md text-info"></i> Especialistas</h4>
        </div>

        @foreach($especialistas as $especialista)
        <div class="col-md-4">
            <div class="card card-outline card-info">
                <div class="card-body text-center">
                    <img src="{{ $especialista->foto ?? asset('img/default-user.png') }}"
                         class="img-fluid rounded-circle mb-2"
                         width="80"
                         alt="Foto del especialista">
                    <h5>{{ $especialista->Nombre }}</h5>
                    <p>{{ $especialista->Descripcion }}</p>
                    <p><strong>Horario:</strong> {{ $especialista->horario ?? 'No disponible' }}</p>
                    <a href="#" class="btn btn-primary btn-sm">
                        <i class="fas fa-calendar-check"></i> Agendar cita
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Tipos de consultas / tratamientos -->
    <div class="row mb-3">
        <div class="col-12">
            <h4><i class="fas fa-hand-holding-medical text-primary"></i> Tipos de Consultas</h4>
        </div>
        @forelse($tratamientos ?? [] as $tratamiento)
        <div class="col-md-3">
            <div class="card card-outline card-primary h-100">
                <div class="card-body text-center">
                    <i class="fas fa-notes-medical fa-2x mb-2 text-primary"></i>
                    <h6>{{ $tratamiento->nombre ?? 'Tratamiento' }}</h6>
                    <p><strong>Duración:</strong> {{ $tratamiento->duracion ?? 'N/A' }}</p>
                    <p><strong>Especialista:</strong> {{ $tratamiento->especialista ?? 'N/A' }}</p>
                    <a href="#" class="btn btn-outline-primary btn-sm">Solicitar</a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <p class="text-muted text-center">No hay tratamientos registrados.</p>
        </div>
        @endforelse
    </div>

    <!-- Historial de sesiones -->
    <div class="card card-outline card-secondary">
        <div class="card-header">
            <h4 class="card-title"><i class="fas fa-history"></i> Historial de Sesiones</h4>
        </div>
        <div class="card-body">
            @if(!empty($historial) && count($historial) > 0)
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Especialista</th>
                        <th>Tipo de consulta</th>
                        <th>Notas</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($historial as $sesion)
                    <tr>
                        <td>{{ $sesion->fecha ?? 'N/A' }}</td>
                        <td>{{ $sesion->especialista ?? 'N/A' }}</td>
                        <td>{{ $sesion->tipo_consulta ?? 'N/A' }}</td>
                        <td>{{ $sesion->notas ?? 'Sin observaciones' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <p class="text-muted text-center">Aún no tienes sesiones registradas.</p>
            @endif
        </div>
    </div>
</div>
@endsection
