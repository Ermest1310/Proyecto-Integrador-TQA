@extends('layouts.medico')

@section('title', 'Nutrición Deportiva')

@section('content')
<div class="container-fluid">
    <!-- Título y descripción -->
    <div class="card card-success mb-3">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-apple-alt"></i> Nutrición Deportiva</h3>
        </div>
        <div class="card-body">
            <p>
                En nuestra Área de Nutrición Deportiva, cada plan está diseñado para potenciar tu rendimiento y bienestar. 
                No solo ofrecemos dietas, sino estrategias alimenticias personalizadas para tus objetivos, acompañadas 
                de especialistas que te guiarán en cada paso hacia un estilo de vida saludable y un rendimiento óptimo.
            </p>
        </div>
    </div>

    <!-- Especialistas -->
    <div class="row mb-3">
        <div class="col-12">
            <h4><i class="fas fa-user-md text-success"></i> Especialistas</h4>
        </div>

        @foreach($especialistas as $especialista)
        <div class="col-md-4">
            <div class="card card-outline card-success">
                <div class="card-body text-center">
                    <img src="{{ $especialista->foto ?? asset('img/default-user.png') }}" 
                         class="img-fluid rounded-circle mb-2" 
                         width="80" 
                         alt="Foto del especialista">
                    <h5>{{ $especialista->Nombre }}</h5>
                    <p>{{ $especialista->Descripcion ?? 'Sin descripción' }}</p>
                    <p><strong>Tipo de consulta:</strong> {{ $especialista->TipoConsulta ?? 'Nutrición' }}</p>
                    <a href="#" class="btn btn-primary btn-sm">
                        <i class="fas fa-calendar-check"></i> Agendar cita
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Planes Nutricionales -->
    <div class="row mb-3">
        <div class="col-12">
            <h4><i class="fas fa-book text-warning"></i> Planes Nutricionales</h4>
        </div>
        @forelse($planesNutricionales ?? [] as $plan)
        <div class="col-md-3">
            <div class="card card-outline card-warning h-100">
                <div class="card-body text-center">
                    <i class="fas fa-utensils fa-2x mb-2 text-warning"></i>
                    <h6>{{ $plan->nombre ?? 'Plan sin nombre' }}</h6>
                    <p>{{ $plan->detalle ?? 'Sin detalles' }}</p>
                    <p><strong>Fecha inicio:</strong> {{ $plan->fecha ?? 'N/A' }}</p>
                    <a href="#" class="btn btn-outline-warning btn-sm">Ver detalles</a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <p class="text-muted text-center">No hay planes nutricionales registrados.</p>
        </div>
        @endforelse
    </div>

    <!-- Historial de sesiones -->
    <div class="card card-outline card-secondary">
        <div class="card-header">
            <h4 class="card-title"><i class="fas fa-history"></i> Historial de seguimiento</h4>
        </div>
        <div class="card-body">
            @if(!empty($historial) && count($historial) > 0)
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Plan</th>
                        <th>Notas</th>
                        <th>Especialista</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($historial as $sesion)
                    <tr>
                        <td>{{ $sesion->fecha ?? 'N/A' }}</td>
                        <td>{{ $sesion->nombre ?? 'N/A' }}</td>
                        <td>{{ $sesion->notas ?? 'Sin observaciones' }}</td>
                        <td>{{ $sesion->especialista ?? 'N/A' }}</td>
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
