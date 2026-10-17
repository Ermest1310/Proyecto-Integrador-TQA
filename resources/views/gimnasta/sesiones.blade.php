@extends('layouts.gimnasta')

@section('title', 'Mis Sesiones de Entrenamiento')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary">
                    <h3 class="card-title text-white">
                        <i class="fas fa-dumbbell"></i> Mis Sesiones de Entrenamiento
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('gimnasta.crear-sesion') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-plus"></i> Nueva Sesión
                        </a>
                    </div>
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
                                <form method="GET" action="{{ route('gimnasta.sesiones') }}">
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
                                                <option value="Programada" {{ request('estado') == 'Programada' ? 'selected' : '' }}>Programada</option>
                                                <option value="Completada" {{ request('estado') == 'Completada' ? 'selected' : '' }}>Completada</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary btn-sm">
                                                <i class="fas fa-search"></i> Filtrar
                                            </button>
                                            <a href="{{ route('gimnasta.sesiones') }}" class="btn btn-secondary btn-sm">
                                                <i class="fas fa-times"></i> Limpiar
                                            </a>
                                        </div>
                                    </div>
                                    
                                    <!-- Filtros Rápidos -->
                                    <div class="row mt-2">
                                        <div class="col-12">
                                            <small class="text-muted">Filtros rápidos:</small><br>
                                            <a href="{{ route('gimnasta.sesiones', ['fecha_desde' => '2025-01-02', 'fecha_hasta' => '2025-01-02']) }}" class="btn btn-outline-info btn-xs mr-1">
                                                <i class="fas fa-calendar-day"></i> Hoy (2/1)
                                            </a>
                                            <a href="{{ route('gimnasta.sesiones', ['fecha_desde' => '2024-12-30', 'fecha_hasta' => '2025-01-05']) }}" class="btn btn-outline-info btn-xs mr-1">
                                                <i class="fas fa-calendar-week"></i> Esta semana
                                            </a>
                                            <a href="{{ route('gimnasta.sesiones', ['estado' => 'Programada']) }}" class="btn btn-outline-primary btn-xs mr-1">
                                                <i class="fas fa-clock"></i> Programadas
                                            </a>
                                            <a href="{{ route('gimnasta.sesiones', ['estado' => 'Completada']) }}" class="btn btn-outline-success btn-xs">
                                                <i class="fas fa-check"></i> Completadas
                                            </a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div id="alertContainer"></div>
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <h6><i class="fas fa-exclamation-circle"></i> <strong>Hay algunos problemas:</strong></h6>
                            <hr class="my-2">
                            <ul class="mb-0 list-unstyled">
                                @foreach($errors->all() as $error)
                                    <li class="mb-1"><i class="fas fa-arrow-right text-danger mr-1"></i> {{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="close" data-dismiss="alert">
                                <span>&times;</span>
                            </button>
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert">
                                <span>&times;</span>
                            </button>
                        </div>
                    @endif

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
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sesiones as $sesion)
                                        <tr>
                                            <td>
                                                <i class="fas fa-calendar text-primary"></i>
                                                {{ $sesion->Fecha->format('d/m/Y') }}
                                                <br>
                                                <small class="text-muted">
                                                    @php
                                                        $fechaCompleta = \Carbon\Carbon::parse($sesion->Fecha->format('Y-m-d') . ' ' . $sesion->Hora_Inicio->format('H:i:s'));
                                                    @endphp
                                                    {{ $fechaCompleta->diffForHumans() }}
                                                </small>
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
                                                @if($sesion->Estado == 'Programada')
                                                    <span class="badge badge-primary">📅 Programada</span>
                                                @elseif($sesion->Estado == 'En_Curso')
                                                    <span class="badge badge-warning">⏳ En Curso</span>
                                                @elseif($sesion->Estado == 'Completada')
                                                    <span class="badge badge-success">✅ Completada</span>
                                                @else
                                                    <span class="badge badge-danger">❌ Cancelada</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($sesion->Estado == 'Programada')
                                                    <button class="btn btn-danger btn-sm" 
                                                            onclick="cancelarSesion({{ $sesion->ID_Sesion }})" 
                                                            title="Cancelar">
                                                        <i class="fas fa-times"></i> Cancelar
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">No tienes sesiones programadas</h4>
                            <p class="text-muted">¡Programa tu primera sesión de entrenamiento!</p>
                            <a href="{{ route('gimnasta.crear-sesion') }}" class="btn btn-primary btn-lg">
                                <i class="fas fa-plus"></i> Programar Sesión
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function cancelarSesion(id) {
    const modal = `
        <div class="modal fade" id="cancelModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger">
                        <h5 class="modal-title text-white"><i class="fas fa-exclamation-triangle"></i> Cancelar Sesión</h5>
                        <button type="button" class="close text-white" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p><i class="fas fa-question-circle text-warning"></i> ¿Estás seguro de que deseas cancelar esta sesión?</p>
                        <p class="text-muted small">Esta acción no se puede deshacer.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times"></i> No, mantener
                        </button>
                        <button type="button" class="btn btn-danger" onclick="confirmarCancelacion(${id})">
                            <i class="fas fa-trash"></i> Sí, cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', modal);
    $('#cancelModal').modal('show');
    $('#cancelModal').on('hidden.bs.modal', function() {
        this.remove();
    });
}

function confirmarCancelacion(id) {
    fetch(`{{ route('gimnasta.cancelar-sesion', '') }}/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        $('#cancelModal').modal('hide');
        if (data.success) {
            const alert = `
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> ${data.message}
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
            `;
            document.getElementById('alertContainer').innerHTML = alert;
            setTimeout(() => location.reload(), 1500);
        } else {
            const alert = `
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle"></i> ${data.error || 'Error al cancelar la sesión'}
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
            `;
            document.getElementById('alertContainer').innerHTML = alert;
        }
    })
    .catch(error => {
        $('#cancelModal').modal('hide');
        const alert = `
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i> Error al cancelar la sesión
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        `;
        document.getElementById('alertContainer').innerHTML = alert;
    });
}

// Búsqueda rápida
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

</script>
@endsection