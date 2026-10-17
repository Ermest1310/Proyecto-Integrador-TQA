@extends('layouts.admin')

@section('title', 'Gestión de Niveles')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="fas fa-tasks text-danger"></i> Aprobación de Niveles</h1>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Estadísticas -->
    <div class="col-md-3">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $solicitudes->where('estado', 'pendiente')->count() }}</h3>
                <p>Pendientes</p>
            </div>
            <div class="icon">
                <i class="fas fa-clock"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $solicitudes->where('estado', 'aprobada')->count() }}</h3>
                <p>Aprobadas</p>
            </div>
            <div class="icon">
                <i class="fas fa-check"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $solicitudes->where('estado', 'rechazada')->count() }}</h3>
                <p>Rechazadas</p>
            </div>
            <div class="icon">
                <i class="fas fa-times"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $solicitudes->count() }}</h3>
                <p>Total</p>
            </div>
            <div class="icon">
                <i class="fas fa-list"></i>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Solicitudes de Cambio de Nivel</h3>
            </div>
            <div class="card-body">
                <!-- Filtros -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <input type="text" id="buscar" class="form-control" placeholder="Buscar gimnasta...">
                    </div>
                    <div class="col-md-3">
                        <select id="filtroEstado" class="form-control">
                            <option value="">Todos los estados</option>
                            <option value="pendiente">Pendiente</option>
                            <option value="aprobada">Aprobada</option>
                            <option value="rechazada">Rechazada</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select id="filtroNivel" class="form-control">
                            <option value="">Todos los niveles</option>
                            <option value="Principiante">Principiante</option>
                            <option value="Intermedio">Intermedio</option>
                            <option value="Avanzado">Avanzado</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-secondary btn-block" onclick="limpiarFiltros()">
                            <i class="fas fa-times"></i> Limpiar
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Fecha</th>
                                <th>Gimnasta</th>
                                <th>Cambio de Nivel</th>
                                <th>Tipo</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($solicitudes as $solicitud)
                            <tr class="{{ $solicitud->estado === 'pendiente' ? 'table-warning' : '' }}">
                                <td>{{ $solicitud->ID_Solicitud }}</td>
                                <td>{{ $solicitud->fecha_solicitud->format('d/m/Y H:i') }}</td>
                                <td>
                                    <strong>{{ $solicitud->gimnasta->Nombre }} {{ $solicitud->gimnasta->Apellido }}</strong>
                                    <br><small class="text-muted">{{ $solicitud->gimnasta->Email }}</small>
                                </td>
                                <td>
                                    @php
                                        $badgeActual = match($solicitud->nivel_actual) {
                                            'Principiante' => 'badge-secondary',
                                            'Intermedio' => 'badge-info',
                                            'Avanzado' => 'badge-warning',
                                            'Elite' => 'badge-danger',
                                            default => 'badge-secondary'
                                        };
                                        $badgeSolicitado = match($solicitud->nivel_solicitado) {
                                            'Principiante' => 'badge-secondary',
                                            'Intermedio' => 'badge-info',
                                            'Avanzado' => 'badge-warning',
                                            'Elite' => 'badge-danger',
                                            default => 'badge-secondary'
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeActual }}">{{ $solicitud->nivel_actual }}</span>
                                    <i class="fas fa-arrow-right"></i>
                                    <span class="badge {{ $badgeSolicitado }}">{{ $solicitud->nivel_solicitado }}</span>
                                </td>
                                <td>
                                    @if($solicitud->tipo === 'evaluacion')
                                        <span class="badge badge-primary">
                                            <i class="fas fa-user"></i> Evaluación
                                        </span>
                                    @else
                                        <span class="badge badge-success">
                                            <i class="fas fa-user-tie"></i> Recomendación
                                        </span>
                                        @if($solicitud->entrenador)
                                            <br><small>{{ $solicitud->entrenador->Nombre }}</small>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    @if($solicitud->estado === 'pendiente')
                                        <span class="badge badge-warning">
                                            <i class="fas fa-clock"></i> Pendiente
                                        </span>
                                    @elseif($solicitud->estado === 'aprobada')
                                        <span class="badge badge-success">
                                            <i class="fas fa-check"></i> Aprobada
                                        </span>
                                        @if($solicitud->fecha_respuesta)
                                            <br><small>{{ $solicitud->fecha_respuesta->format('d/m/Y') }}</small>
                                        @endif
                                    @else
                                        <span class="badge badge-danger">
                                            <i class="fas fa-times"></i> Rechazada
                                        </span>
                                        @if($solicitud->fecha_respuesta)
                                            <br><small>{{ $solicitud->fecha_respuesta->format('d/m/Y') }}</small>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-info" onclick="verDetalles({{ json_encode($solicitud) }})">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    
                                    @if($solicitud->estado === 'pendiente')
                                        <button class="btn btn-sm btn-success" onclick="aprobar({{ $solicitud->ID_Solicitud }})">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="rechazar({{ $solicitud->ID_Solicitud }})">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center">No hay solicitudes registradas</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detalles -->
<div class="modal fade" id="modalDetalles" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title">Detalles de la Solicitud</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" id="detallesContent"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Aprobar -->
<div class="modal fade" id="modalAprobar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formAprobar" method="POST">
                @csrf
                <div class="modal-header bg-success">
                    <h5 class="modal-title">Aprobar Solicitud</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro de aprobar esta solicitud?</p>
                    <div class="form-group">
                        <label>Comentarios (opcional)</label>
                        <textarea name="comentarios_admin" class="form-control" rows="3" placeholder="Agrega comentarios adicionales..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Aprobar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Rechazar -->
<div class="modal fade" id="modalRechazar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formRechazar" method="POST">
                @csrf
                <div class="modal-header bg-danger">
                    <h5 class="modal-title">Rechazar Solicitud</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro de rechazar esta solicitud?</p>
                    <div class="form-group">
                        <label>Motivo del Rechazo *</label>
                        <textarea name="comentarios_admin" class="form-control" rows="3" required placeholder="Explica el motivo del rechazo..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Rechazar</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
function verDetalles(solicitud) {
    let html = `
        <div class="row">
            <div class="col-md-6">
                <h5>Información General</h5>
                <p><strong>ID:</strong> ${solicitud.ID_Solicitud}</p>
                <p><strong>Gimnasta:</strong> ${solicitud.gimnasta.Nombre} ${solicitud.gimnasta.Apellido}</p>
                <p><strong>Email:</strong> ${solicitud.gimnasta.Email}</p>
                <p><strong>Fecha Solicitud:</strong> ${new Date(solicitud.fecha_solicitud).toLocaleDateString()}</p>
                <p><strong>Tipo:</strong> ${solicitud.tipo === 'evaluacion' ? 'Evaluación' : 'Recomendación'}</p>
            </div>
            <div class="col-md-6">
                <h5>Estado</h5>
                <p><strong>Cambio:</strong> ${solicitud.nivel_actual} → ${solicitud.nivel_solicitado}</p>
                <p><strong>Estado:</strong> ${solicitud.estado}</p>
                ${solicitud.fecha_respuesta ? `<p><strong>Fecha Respuesta:</strong> ${new Date(solicitud.fecha_respuesta).toLocaleDateString()}</p>` : ''}
                ${solicitud.aprobador ? `<p><strong>Procesado por:</strong> ${solicitud.aprobador.Nombre} ${solicitud.aprobador.Apellido}</p>` : ''}
            </div>
        </div>
        <hr>
        ${solicitud.comentarios_gimnasta ? `<div class="alert alert-info"><strong>Comentarios del gimnasta:</strong><br>${solicitud.comentarios_gimnasta}</div>` : ''}
        ${solicitud.comentarios_entrenador ? `<div class="alert alert-success"><strong>Comentarios del entrenador:</strong><br>${solicitud.comentarios_entrenador}</div>` : ''}
        ${solicitud.comentarios_admin ? `<div class="alert alert-${solicitud.estado === 'aprobada' ? 'success' : 'danger'}"><strong>Respuesta del administrador:</strong><br>${solicitud.comentarios_admin}</div>` : ''}
    `;
    
    document.getElementById('detallesContent').innerHTML = html;
    $('#modalDetalles').modal('show');
}

function aprobar(id) {
    document.getElementById('formAprobar').action = `/admin/solicitudes-nivel/${id}/aprobar`;
    $('#modalAprobar').modal('show');
}

function rechazar(id) {
    document.getElementById('formRechazar').action = `/admin/solicitudes-nivel/${id}/rechazar`;
    $('#modalRechazar').modal('show');
}

// Filtros
function aplicarFiltros() {
    const buscar = document.getElementById('buscar').value.toLowerCase();
    const estado = document.getElementById('filtroEstado').value;
    const nivel = document.getElementById('filtroNivel').value;
    
    const filas = document.querySelectorAll('tbody tr');
    
    filas.forEach(function(fila) {
        const textoCompleto = fila.textContent.toLowerCase();
        const celdas = fila.querySelectorAll('td');
        
        let mostrar = true;
        
        if (buscar && !textoCompleto.includes(buscar)) mostrar = false;
        if (estado && !celdas[5].textContent.toLowerCase().includes(estado)) mostrar = false;
        if (nivel && !celdas[3].textContent.includes(nivel)) mostrar = false;
        
        fila.style.display = mostrar ? '' : 'none';
    });
}

function limpiarFiltros() {
    document.getElementById('buscar').value = '';
    document.getElementById('filtroEstado').value = '';
    document.getElementById('filtroNivel').value = '';
    aplicarFiltros();
}

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('buscar').addEventListener('input', aplicarFiltros);
    document.getElementById('filtroEstado').addEventListener('change', aplicarFiltros);
    document.getElementById('filtroNivel').addEventListener('change', aplicarFiltros);
});
</script>
@endsection
