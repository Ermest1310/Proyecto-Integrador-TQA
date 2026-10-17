@extends('layouts.admin')

@section('title', 'Gestión de Postulaciones')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="fas fa-user-clock text-primary"></i> Postulaciones</h1>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Lista de Postulaciones</h3>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                
                <!-- Filtros -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <input type="text" id="busquedaRapida" class="form-control" placeholder="Buscar postulante..." value="{{ request('buscar') }}">
                    </div>
                    <div class="col-md-3">
                        <select id="filtroEstado" class="form-control">
                            <option value="">Todos los estados</option>
                            <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendientes</option>
                            <option value="aprobada" {{ request('estado') == 'aprobada' ? 'selected' : '' }}>Aprobadas</option>
                            <option value="rechazada" {{ request('estado') == 'rechazada' ? 'selected' : '' }}>Rechazadas</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-primary" onclick="aplicarFiltros()">Filtrar</button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('postulaciones.index') }}" class="btn btn-secondary">Limpiar</a>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Edad</th>
                                <th>IMC</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($postulaciones as $postulacion)
                            <tr>
                                <td>{{ $postulacion->created_at->format('d/m/Y H:i') }}</td>
                                <td>{{ $postulacion->Nombre }} {{ $postulacion->Apellido }}</td>
                                <td>{{ $postulacion->Email }}</td>
                                <td>
                                    <span class="badge {{ $postulacion->Edad >= 10 && $postulacion->Edad <= 35 ? 'badge-success' : 'badge-warning' }}">{{ $postulacion->Edad }} años</span>
                                </td>
                                <td>
                                    <span class="badge {{ $postulacion->IMC >= 18.5 && $postulacion->IMC <= 24.9 ? 'badge-success' : 'badge-warning' }}">{{ $postulacion->IMC }}</span>
                                </td>
                                <td>
                                    @if($postulacion->estado === 'pendiente')
                                        <span class="badge badge-warning">Pendiente</span>
                                    @elseif($postulacion->estado === 'aprobada')
                                        <span class="badge badge-success">Aprobada</span>
                                    @else
                                        <span class="badge badge-danger">Rechazada</span>
                                    @endif
                                </td>
                                <td>
                                    @if($postulacion->estado === 'pendiente')
                                        <button class="btn btn-success btn-sm" onclick="aprobar({{ $postulacion->ID_Postulacion }})">
                                            <i class="fas fa-check"></i> Aprobar
                                        </button>
                                        <button class="btn btn-danger btn-sm" onclick="rechazar({{ $postulacion->ID_Postulacion }})">
                                            <i class="fas fa-times"></i> Rechazar
                                        </button>
                                    @endif
                                    <button class="btn btn-info btn-sm" onclick="verDetalle({{ $postulacion->ID_Postulacion }})">
                                        <i class="fas fa-eye"></i> Ver
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center">No hay postulaciones registradas</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Paginación -->
                <div class="d-flex justify-content-center mt-3">
                    {{ $postulaciones->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detalle -->
<div class="modal fade" id="detalleModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Detalle de Postulación</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" id="detalleContent">
            </div>
        </div>
    </div>
</div>

<!-- Modal Aprobar -->
<div class="modal fade" id="aprobarModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h4 class="modal-title text-white">
                    <i class="fas fa-check-circle"></i> Aprobar Postulación
                </h4>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body text-center">
                <div class="mb-3">
                    <i class="fas fa-user-plus fa-3x text-success mb-3"></i>
                    <h5>Confirmar Aprobación</h5>
                    <p class="text-muted">Se creará un nuevo usuario gimnasta con los datos de la postulación.</p>
                </div>
                <div class="alert alert-info">
                    <strong>Credenciales del nuevo usuario:</strong><br>
                    <small>Email: Como nombre de usuario<br>
                    Contraseña: Su documento de identidad</small>
                </div>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="button" class="btn btn-success" id="confirmarAprobar">
                    <i class="fas fa-check"></i> Aprobar y Crear Usuario
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Rechazar -->
<div class="modal fade" id="rechazarModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="rechazarForm" method="POST">
                @csrf
                <div class="modal-header bg-danger">
                    <h4 class="modal-title text-white">
                        <i class="fas fa-times-circle"></i> Rechazar Postulación
                    </h4>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Comentarios (opcional):</label>
                        <textarea name="comentarios" class="form-control" rows="3" placeholder="Razón del rechazo..."></textarea>
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
let postulacionIdAprobar = null;

function aprobar(id) {
    postulacionIdAprobar = id;
    $('#aprobarModal').modal('show');
}

$('#confirmarAprobar').click(function() {
    if (postulacionIdAprobar) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/postulaciones/${postulacionIdAprobar}/aprobar`;
        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = '{{ csrf_token() }}';
        form.appendChild(csrf);
        document.body.appendChild(form);
        form.submit();
    }
});

function rechazar(id) {
    document.getElementById('rechazarForm').action = `/postulaciones/${id}/rechazar`;
    $('#rechazarModal').modal('show');
}

function verDetalle(id) {
    // Hacer petición AJAX para obtener datos seguros
    fetch(`/postulaciones/${id}/detalle`)
        .then(response => response.json())
        .then(postulacion => {
            const content = `
                <div class="row">
                    <div class="col-md-6">
                        <strong>Nombre:</strong> ${postulacion.Nombre} ${postulacion.Apellido}<br>
                        <strong>Email:</strong> ${postulacion.Email}<br>
                        <strong>Cédula:</strong> ${postulacion.CedulaIdentidad}<br>
                        <strong>Nacionalidad:</strong> ${postulacion.Nacionalidad}<br>
                    </div>
                    <div class="col-md-6">
                        <strong>Edad:</strong> ${postulacion.Edad} años<br>
                        <strong>Peso:</strong> ${postulacion.Peso} kg<br>
                        <strong>Altura:</strong> ${postulacion.Altura} m<br>
                        <strong>IMC:</strong> ${postulacion.IMC}<br>
                        <strong>Nivel:</strong> ${postulacion.Nivel}<br>
                    </div>
                </div>
                ${postulacion.recomendaciones_sistema ? `<hr><div class="alert alert-info"><strong>Recomendaciones del Sistema:</strong><br><pre>${postulacion.recomendaciones_sistema}</pre></div>` : ''}
                ${postulacion.comentarios ? `<hr><strong>Comentarios del Revisor:</strong><br>${postulacion.comentarios}` : ''}
                ${postulacion.revisor ? `<hr><strong>Revisado por:</strong> ${postulacion.revisor.Nombre} ${postulacion.revisor.Apellido}` : ''}
            `;
            document.getElementById('detalleContent').innerHTML = content;
            $('#detalleModal').modal('show');
        })
        .catch(error => {
            alert('Error al cargar los detalles');
        });
}

// Filtros
function aplicarFiltros() {
    const buscar = $('#busquedaRapida').val();
    const estado = $('#filtroEstado').val();
    const params = new URLSearchParams();
    
    if (buscar) params.append('buscar', buscar);
    if (estado) params.append('estado', estado);
    
    window.location.href = '{{ route("postulaciones.index") }}?' + params.toString();
}

$('#busquedaRapida').on('keypress', function(e) {
    if (e.which === 13) aplicarFiltros();
});
</script>
@endsection