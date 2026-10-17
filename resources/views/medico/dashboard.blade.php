@extends('layouts.medico')

@section('title', 'Dashboard Médico')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-info">
                <div class="card-body text-white">
                    <h2><i class="fas fa-stethoscope"></i> Bienvenido, {{ session('usuario_nombre') }}</h2>
                    <p class="mb-0">Panel Médico - {{ session('usuario_rol') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="info-box bg-info">
                <span class="info-box-icon"><i class="fas fa-users"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Gimnastas</span>
                    <span class="info-box-number">{{ $gimnastas->count() }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box bg-success">
                <span class="info-box-icon"><i class="fas fa-heartbeat"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Elite</span>
                    <span class="info-box-number">{{ $gimnastas->where('Nivel', 'Elite')->count() }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box bg-warning">
                <span class="info-box-icon"><i class="fas fa-chart-line"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Avanzado</span>
                    <span class="info-box-number">{{ $gimnastas->where('Nivel', 'Avanzado')->count() }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box bg-secondary">
                <span class="info-box-icon"><i class="fas fa-clipboard-check"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">En Desarrollo</span>
                    <span class="info-box-number">{{ $gimnastas->whereIn('Nivel', ['Principiante', 'Intermedio'])->count() }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-users"></i> Gimnastas Registrados</h3>
                    <div class="card-tools">
                        <div class="input-group input-group-sm" style="width: 250px;">
                            <input type="text" id="searchInput" class="form-control" placeholder="Buscar gimnasta...">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($gimnastas->count() > 0)
                        <div class="row" id="gimnastasContainer">
                            @foreach($gimnastas as $gimnasta)
                                <div class="col-md-4 mb-3 gimnasta-card" data-name="{{ strtolower($gimnasta->Nombre . ' ' . $gimnasta->Apellido) }}">
                                    <div class="card card-outline 
                                        @if($gimnasta->Nivel == 'Elite') card-success
                                        @elseif($gimnasta->Nivel == 'Avanzado') card-warning  
                                        @elseif($gimnasta->Nivel == 'Intermedio') card-info
                                        @else card-secondary @endif">
                                        <div class="card-body">
                                            <div class="text-center mb-3">
                                                <i class="fas fa-user-circle fa-3x text-muted"></i>
                                            </div>
                                            <h5 class="text-center mb-2">{{ $gimnasta->Nombre }} {{ $gimnasta->Apellido }}</h5>
                                            <div class="text-center mb-2">
                                                <span class="badge 
                                                    @if($gimnasta->Nivel == 'Elite') badge-success
                                                    @elseif($gimnasta->Nivel == 'Avanzado') badge-warning  
                                                    @elseif($gimnasta->Nivel == 'Intermedio') badge-info
                                                    @else badge-secondary @endif">
                                                    {{ $gimnasta->Nivel }}
                                                </span>
                                            </div>
                                            <div class="row text-center small text-muted mb-3">
                                                <div class="col-6">
                                                    <strong>{{ $gimnasta->Peso }}kg</strong><br>Peso
                                                </div>
                                                <div class="col-6">
                                                    <strong>{{ $gimnasta->Altura }}m</strong><br>Altura
                                                </div>
                                            </div>

                                            <div class="text-center">
                                                <a href="{{ route('medico.ver-gimnasta', $gimnasta->ID_Usuario) }}" 
                                                   class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i> Ver Perfil
                                                </a>
                                                <a href="{{ route('medico.historial', $gimnasta->ID_Usuario) }}" 
                                                   class="btn btn-sm btn-info">
                                                    <i class="fas fa-clipboard-list"></i> Historial
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h5>No hay gimnastas registrados</h5>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('searchInput').addEventListener('keyup', function() {
    const searchTerm = this.value.toLowerCase();
    const cards = document.querySelectorAll('.gimnasta-card');
    
    cards.forEach(card => {
        const name = card.getAttribute('data-name');
        if (name.includes(searchTerm)) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
});
</script>
@endsection