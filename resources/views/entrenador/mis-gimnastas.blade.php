@extends('layouts.entrenador')

@section('title', 'Mis Gimnastas')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-users"></i> Mis Gimnastas</h3>
                    <p class="text-muted mb-0">Gimnastas con sesiones programadas conmigo</p>
                </div>
                <div class="card-body">
                    @if($gimnastas->count() > 0)
                        <div class="row">
                            @foreach($gimnastas as $gimnasta)
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="avatar-circle bg-primary text-white mr-3">
                                                    {{ substr($gimnasta->Nombre, 0, 1) }}{{ substr($gimnasta->Apellido, 0, 1) }}
                                                </div>
                                                <div>
                                                    <h5 class="mb-1">{{ $gimnasta->Nombre }} {{ $gimnasta->Apellido }}</h5>
                                                    <p class="text-muted mb-0">{{ $gimnasta->Nivel ?? 'Sin nivel' }}</p>
                                                </div>
                                            </div>
                                            
                                            <div class="row text-center mb-3">
                                                <div class="col-4">
                                                    <small class="text-muted">Peso</small>
                                                    <div class="font-weight-bold">{{ $gimnasta->Peso ?? 'N/A' }}kg</div>
                                                </div>
                                                <div class="col-4">
                                                    <small class="text-muted">Altura</small>
                                                    <div class="font-weight-bold">{{ $gimnasta->Altura ?? 'N/A' }}m</div>
                                                </div>
                                                <div class="col-4">
                                                    <small class="text-muted">Edad</small>
                                                    <div class="font-weight-bold">
                                                        {{ $gimnasta->FechaNacimiento ? \Carbon\Carbon::parse($gimnasta->FechaNacimiento)->age : 'N/A' }}
                                                    </div>
                                                </div>
                                            </div>

                                            @if($gimnasta->lesiones->count() > 0)
                                                <div class="alert alert-warning alert-sm mb-3">
                                                    <i class="fas fa-exclamation-triangle"></i>
                                                    <strong>Lesión Activa</strong>
                                                    <br>
                                                    <small>{{ $gimnasta->lesiones->first()->Tipo }} - {{ $gimnasta->lesiones->first()->ParteCuerpo }}</small>
                                                </div>
                                            @endif

                                            <div class="text-center">
                                                <a href="{{ route('entrenador.gimnasta-dashboard', $gimnasta->ID_Usuario) }}" 
                                                   class="btn btn-primary btn-sm">
                                                    <i class="fas fa-tachometer-alt"></i> Ver Dashboard
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">No tienes gimnastas asignados</h4>
                            <p class="text-muted">Aún no tienes sesiones programadas con ningún gimnasta.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-circle {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 18px;
}

.alert-sm {
    padding: 0.5rem;
    font-size: 0.875rem;
}
</style>
@endsection