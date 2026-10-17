@extends('layouts.gimnasta')

@section('title', 'Historial de Sesiones')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-secondary">
                    <h3 class="card-title text-white">
                        <i class="fas fa-history"></i> Historial de Sesiones
                    </h3>
                </div>
                <div class="card-body">
                    <!-- Búsqueda Rápida -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <input type="text" id="busquedaRapida" class="form-control" placeholder="Buscar por entrenador, aparato o fecha...">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 text-right">
                            <button class="btn btn-outline-secondary" type="button" data-toggle="collapse" data-target="#filtros">
                                <i class="fas fa-filter"></i> Filtros Avanzados
                            </button>
                        </div>
                    </div>
                    
                    <!-- Filtros -->
                    <div class="card mb-3">
                        <div class="collapse" id="filtros">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="fas fa-filter"></i> Filtros Avanzados
                                </h6>
                            </div>
                        <div class="collapse" id="filtros">
                            <div class="card-body">
                                <form method="GET" action="{{ route('gimnasta.historial') }}">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label>Fecha Desde:</label>
                                            <input type="date" name="fecha_desde" class="form-control" value="{{ request('fecha_desde') }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label>Fecha Hasta:</label>
                                            <input type="date" name="fecha_hasta" class="form-control" value="{{ request('fecha_hasta') }}">
                                        </div>
                                        <div class="col-md-2">
                                            <label>Entrenador:</label>
                                            <select name="entrenador" class="form-control">
                                                <option value="">Todos</option>
                                                @foreach($entrenadores as $entrenador)
                                                    <option value="{{ $entrenador->ID_Usuario }}" {{ request('entrenador') == $entrenador->ID_Usuario ? 'selected' : '' }}>
                                                        {{ $entrenador->Nombre }} {{ $entrenador->Apellido }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label>Aparato:</label>
                                            <select name="aparato" class="form-control">
                                                <option value="">Todos</option>
                                                @foreach($aparatos as $aparato)
                                                    <option value="{{ $aparato->ID_Aparato }}" {{ request('aparato') == $aparato->ID_Aparato ? 'selected' : '' }}>
                                                        {{ $aparato->Nombre }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label>Estado:</label>
                                            <select name="estado" class="form-control">
                                                <option value="">Todos</option>
                                                <option value="Completada" {{ request('estado') == 'Completada' ? 'selected' : '' }}>Completada</option>
                                                <option value="Cancelada" {{ request('estado') == 'Cancelada' ? 'selected' : '' }}>Cancelada</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary btn-sm">
                                                <i class="fas fa-search"></i> Filtrar
                                            </button>
                                            <a href="{{ route('gimnasta.historial') }}" class="btn btn-secondary btn-sm">
                                                <i class="fas fa-times"></i> Limpiar
                                            </a>
                                        </div>
                                    </div>
                                    
                                    <!-- Filtros Rápidos -->
                                    <div class="row mt-2">
                                        <div class="col-12">
                                            <small class="text-muted">Filtros rápidos:</small><br>
                                            <a href="{{ route('gimnasta.historial', ['fecha_desde' => '2024-12-26', 'fecha_hasta' => '2025-01-02']) }}" class="btn btn-outline-info btn-xs mr-1">
                                                <i class="fas fa-calendar-week"></i> Última semana
                                            </a>
                                            <a href="{{ route('gimnasta.historial', ['fecha_desde' => '2024-12-02']) }}" class="btn btn-outline-info btn-xs mr-1">
                                                <i class="fas fa-calendar-alt"></i> Último mes
                                            </a>
                                            <a href="{{ route('gimnasta.historial', ['estado' => 'Completada']) }}" class="btn btn-outline-success btn-xs mr-1">
                                                <i class="fas fa-check"></i> Completadas
                                            </a>
                                            <a href="{{ route('gimnasta.historial', ['estado' => 'Cancelada']) }}" class="btn btn-outline-danger btn-xs">
                                                <i class="fas fa-times"></i> Canceladas
                                            </a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- Contador de resultados -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted">
                            <i class="fas fa-list"></i> 
                            Mostrando {{ $sesiones->count() }} sesion{{ $sesiones->count() != 1 ? 'es' : '' }}
                            @if(request()->hasAny(['fecha_desde', 'fecha_hasta', 'entrenador', 'aparato', 'estado']))
                                (filtradas)
                            @endif
                        </span>
                    </div>
                    
                    @if($sesiones->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Horario</th>
                                        <th>Duración</th>
                                        <th>Entrenador</th>
                                        <th>Aparato</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sesiones as $sesion)
                                        <tr>
                                            <td>
                                                <i class="fas fa-calendar text-primary"></i>
                                                {{ $sesion->Fecha->format('d/m/Y') }}
                                                <br>
                                                <small class="text-muted">{{ $sesion->Fecha->diffForHumans() }}</small>
                                            </td>
                                            <td>
                                                <i class="fas fa-clock text-info"></i>
                                                {{ $sesion->Hora_Inicio->format('H:i') }} - {{ $sesion->Hora_Fin->format('H:i') }}
                                            </td>
                                            <td>
                                                <span class="badge badge-info">
                                                    {{ $sesion->Duracion_Minutos }} min
                                                </span>
                                            </td>
                                            <td>
                                                <i class="fas fa-user-tie text-success"></i>
                                                {{ $sesion->entrenador->Nombre }} {{ $sesion->entrenador->Apellido }}
                                            </td>
                                            <td>
                                                @if($sesion->aparato)
                                                    <i class="fas fa-dumbbell text-warning"></i>
                                                    {{ $sesion->aparato->Nombre }}
                                                @else
                                                    <span class="text-muted">Entrenamiento general</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($sesion->Estado == 'Completada')
                                                    <span class="badge badge-success">✅ Completada</span>
                                                @else
                                                    <span class="badge badge-danger">❌ Cancelada</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-history fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">No tienes historial de sesiones</h4>
                            <p class="text-muted">Las sesiones completadas y canceladas aparecerán aquí.</p>
                            <a href="{{ route('gimnasta.sesiones') }}" class="btn btn-primary">
                                <i class="fas fa-dumbbell"></i> Ver Mis Sesiones
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Búsqueda rápida
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('busquedaRapida').addEventListener('input', function() {
        const filtro = this.value.toLowerCase();
        const filas = document.querySelectorAll('tbody tr');
        
        filas.forEach(function(fila) {
            const texto = fila.textContent.toLowerCase();
            if (texto.includes(filtro)) {
                fila.style.display = '';
            } else {
                fila.style.display = 'none';
            }
        });
        
        // Actualizar contador
        const filasVisibles = document.querySelectorAll('tbody tr[style=""]').length;
        const contador = document.querySelector('.text-muted');
        if (contador) {
            contador.innerHTML = `<i class="fas fa-list"></i> Mostrando ${filasVisibles} sesión${filasVisibles != 1 ? 'es' : ''} ${filtro ? '(filtradas)' : ''}`;
        }
    });
});
</script>
@endsection