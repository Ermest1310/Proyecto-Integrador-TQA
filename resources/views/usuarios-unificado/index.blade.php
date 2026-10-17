@extends('layouts.admin')

@section('title', 'Gestión de Usuarios')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-users"></i> Gestión de Usuarios</h3>
                    <button class="btn btn-primary float-right" onclick="showCreateModal()">
                        <i class="fas fa-plus"></i> Nuevo Usuario
                    </button>
                </div>
                <div class="card-body">
                    <div id="alertContainer"></div>
                    <!-- Filtros -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="input-group">
                                <input type="text" id="busquedaRapida" class="form-control" placeholder="Buscar por nombre, email..." value="{{ request('buscar') }}">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8 text-right">
                            <button class="btn btn-outline-secondary" type="button" data-toggle="collapse" data-target="#filtros">
                                <i class="fas fa-filter"></i> Filtros Avanzados
                            </button>
                        </div>
                    </div>
                    
                    <!-- Filtros Avanzados -->
                    <div class="collapse {{ request()->hasAny(['rol', 'estado', 'entrenador']) ? 'show' : '' }}" id="filtros">
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-filter"></i> Filtros Avanzados</h6>
                            </div>
                            <div class="card-body">
                                <form method="GET" action="{{ route('usuarios-unificado.index') }}">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label>Rol:</label>
                                            <select name="rol" class="form-control">
                                                <option value="">Todos los roles</option>
                                                @foreach($roles as $rol)
                                                    <option value="{{ $rol->ID_Rol }}" {{ request('rol') == $rol->ID_Rol ? 'selected' : '' }}>
                                                        {{ $rol->NombreRol }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label>Estado:</label>
                                            <select name="estado" class="form-control">
                                                <option value="">Todos los estados</option>
                                                <option value="activo" {{ request('estado') == 'activo' ? 'selected' : '' }}>Activos</option>
                                                <option value="inactivo" {{ request('estado') == 'inactivo' ? 'selected' : '' }}>Inactivos</option>
                                            </select>
                                        </div>

                                        <div class="col-md-3">
                                            <label>Búsqueda:</label>
                                            <input type="text" name="buscar" class="form-control" placeholder="Nombre, email..." value="{{ request('buscar') }}">
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary btn-sm">
                                                <i class="fas fa-search"></i> Filtrar
                                            </button>
                                            <a href="{{ route('usuarios-unificado.index') }}" class="btn btn-secondary btn-sm">
                                                <i class="fas fa-times"></i> Limpiar
                                            </a>
                                        </div>
                                    </div>
                                    
                                    <!-- Filtros Rápidos -->
                                    <div class="row mt-2">
                                        <div class="col-12">
                                            <small class="text-muted">Filtros rápidos:</small><br>
                                            <a href="{{ route('usuarios-unificado.index', ['rol' => $roles->where('NombreRol', 'Gimnasta')->first()->ID_Rol ?? '']) }}" class="btn btn-outline-primary btn-xs mr-1">
                                                <i class="fas fa-user"></i> Gimnastas
                                            </a>
                                            <a href="{{ route('usuarios-unificado.index', ['rol' => $roles->where('NombreRol', 'Entrenador')->first()->ID_Rol ?? '']) }}" class="btn btn-outline-success btn-xs mr-1">
                                                <i class="fas fa-user-tie"></i> Entrenadores
                                            </a>
                                            <a href="{{ route('usuarios-unificado.index', ['estado' => 'activo']) }}" class="btn btn-outline-success btn-xs mr-1">
                                                <i class="fas fa-check"></i> Activos
                                            </a>
                                            <a href="{{ route('usuarios-unificado.index', ['estado' => 'inactivo']) }}" class="btn btn-outline-secondary btn-xs">
                                                <i class="fas fa-times"></i> Inactivos
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
                            <i class="fas fa-users"></i> 
                            Mostrando {{ $usuarios->count() }} usuario{{ $usuarios->count() != 1 ? 's' : '' }}
                            @if(request()->hasAny(['rol', 'estado', 'entrenador', 'buscar']))
                                (filtrados)
                            @endif
                        </span>
                    </div>
                    
                    <table class="table table-striped" id="usuariosTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Usuario</th>
                                <th>Nombre Completo</th>
                                <th>Email</th>
                                <th>Rol</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($usuarios as $usuario)
                            <tr>
                                <td>{{ $usuario->ID_Usuario }}</td>
                                <td>{{ $usuario->NombreUsuario }}</td>
                                <td>{{ $usuario->Nombre }} {{ $usuario->Apellido }}</td>
                                <td>{{ $usuario->Email }}</td>
                                <td>
                                    <span class="badge badge-{{ $usuario->rol->NombreRol === 'Administrador' ? 'danger' : ($usuario->rol->NombreRol === 'Gimnasta' ? 'primary' : 'success') }}">
                                        {{ $usuario->rol->NombreRol }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $usuario->activo ? 'success' : 'secondary' }}">
                                        {{ $usuario->activo ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-info" onclick="editUsuario({{ $usuario->ID_Usuario }})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    @if($usuario->activo)
                                        <button class="btn btn-sm btn-danger" onclick="deleteUsuario({{ $usuario->ID_Usuario }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @else
                                        <button class="btn btn-sm btn-success" onclick="reactivateUsuario({{ $usuario->ID_Usuario }})">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="usuarioModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Nuevo Usuario</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="usuarioForm">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Email *</label>
                                <input type="email" name="Email" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Documento de Identidad *</label>
                                <input type="text" name="CedulaIdentidad" class="form-control" required placeholder="Según nacionalidad">
                                <small class="text-muted">CI, DNI, Cédula, RUT, etc.</small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nacionalidad *</label>
                                <select name="Nacionalidad" class="form-control" required>
                                    <option value="">Seleccionar país...</option>
                                    <option value="Argentina">Argentina</option>
                                    <option value="Bolivia">Bolivia</option>
                                    <option value="Brasil">Brasil</option>
                                    <option value="Chile">Chile</option>
                                    <option value="Colombia">Colombia</option>
                                    <option value="Ecuador">Ecuador</option>
                                    <option value="Paraguay">Paraguay</option>
                                    <option value="Perú">Perú</option>
                                    <option value="Uruguay">Uruguay</option>
                                    <option value="Venezuela">Venezuela</option>
                                    <option value="México">México</option>
                                    <option value="Guatemala">Guatemala</option>
                                    <option value="Honduras">Honduras</option>
                                    <option value="El Salvador">El Salvador</option>
                                    <option value="Nicaragua">Nicaragua</option>
                                    <option value="Costa Rica">Costa Rica</option>
                                    <option value="Panamá">Panamá</option>
                                    <option value="Cuba">Cuba</option>
                                    <option value="República Dominicana">República Dominicana</option>
                                    <option value="España">España</option>
                                    <option value="Estados Unidos">Estados Unidos</option>
                                    <option value="Canadá">Canadá</option>
                                    <option value="Otro">Otro</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Rol *</label>
                                <select name="ID_Rol" class="form-control" required onchange="toggleFields()">
                                    <option value="">Seleccionar...</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nombre *</label>
                                <input type="text" name="Nombre" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Apellido *</label>
                                <input type="text" name="Apellido" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Campos específicos para Gimnasta -->
                    <div id="gimnastaFields" style="display: none;">
                        <hr>
                        <h6>Datos Deportivos</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Fecha Nacimiento</label>
                                    <input type="date" name="FechaNacimiento" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Peso (kg)</label>
                                    <input type="number" name="Peso" class="form-control" step="0.01" min="25" max="150">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Altura (m)</label>
                                    <input type="number" name="Altura" class="form-control" step="0.01" min="1.00" max="2.20">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Nivel</label>
                                    <select name="Nivel" class="form-control">
                                        <option value="">Seleccionar...</option>
                                        <option value="Principiante">Principiante</option>
                                        <option value="Intermedio">Intermedio</option>
                                        <option value="Avanzado">Avanzado</option>
                                        <option value="Elite">Elite</option>
                                    </select>
                                </div>
                            </div>

                        </div>
                    </div>
                    
                    <!-- Campos específicos para Entrenador/Médico/Fisioterapeuta -->
                    <div id="entrenadorFields" style="display: none;">
                        <hr>
                        <h6>Datos de Contacto</h6>
                        <div class="form-group">
                            <label>Contacto *</label>
                            <input type="text" name="Contacto" class="form-control" placeholder="Teléfono, WhatsApp, etc.">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>


@endsection

@section('scripts')
<script>
let isEditing = false;
let editingId = null;
let roles = [];
let entrenadores = [];

$(document).ready(function() {
    loadRoles();
});

function loadRoles() {
    $.get('{{ route("usuarios-unificado.roles") }}', function(data) {
        roles = data.roles;
        entrenadores = data.entrenadores;
        
        let roleSelect = $('select[name="ID_Rol"]');
        roleSelect.empty().append('<option value="">Seleccionar...</option>');
        roles.forEach(rol => {
            roleSelect.append(`<option value="${rol.ID_Rol}">${rol.NombreRol}</option>`);
        });
        
        let entrenadorSelect = $('select[name="ID_Entrenador"]');
        entrenadorSelect.empty().append('<option value="">Seleccionar...</option>');
        entrenadores.forEach(entrenador => {
            entrenadorSelect.append(`<option value="${entrenador.ID_Usuario}">${entrenador.Nombre} ${entrenador.Apellido}</option>`);
        });
    });
}

function toggleFields() {
    const rolSelect = $('select[name="ID_Rol"]');
    const selectedRol = roles.find(r => r.ID_Rol == rolSelect.val());
    
    $('#gimnastaFields').hide();
    $('#entrenadorFields').hide();
    
    if (selectedRol) {
        if (selectedRol.NombreRol === 'Gimnasta') {
            $('#gimnastaFields').show();
        } else if (['Entrenador', 'Médico', 'Fisioterapeuta'].includes(selectedRol.NombreRol)) {
            $('#entrenadorFields').show();
        }
    }
}

function showCreateModal() {
    isEditing = false;
    editingId = null;
    $('#modalTitle').text('Nuevo Usuario');
    $('#usuarioForm')[0].reset();
    $('#gimnastaFields').hide();
    $('#entrenadorFields').hide();
    $('#usuarioModal').modal('show');
}

function editUsuario(id) {
    isEditing = true;
    editingId = id;
    $('#modalTitle').text('Editar Usuario');
    
    $.get(`{{ url('usuarios-unificado') }}/${id}/edit`, function(data) {
        Object.keys(data).forEach(key => {
            if (key === 'FechaNacimiento' && data[key]) {
                // Formatear fecha para input date (YYYY-MM-DD)
                const fecha = new Date(data[key]);
                const fechaFormateada = fecha.toISOString().split('T')[0];
                $(`[name="${key}"]`).val(fechaFormateada);
            } else {
                $(`[name="${key}"]`).val(data[key]);
            }
        });
        toggleFields();
        $('#usuarioModal').modal('show');
    });
}

$('#usuarioForm').submit(function(e) {
    e.preventDefault();
    
    const url = isEditing ? 
        `{{ url('usuarios-unificado') }}/${editingId}` : 
        '{{ route("usuarios-unificado.store") }}';
    
    const method = isEditing ? 'PUT' : 'POST';
    
    $.ajax({
        url: url,
        method: method,
        data: $(this).serialize(),
        success: function(response) {
            $('#usuarioModal').modal('hide');
            location.reload();
        },
        error: function(xhr) {
            if (xhr.status === 422) {
                // Errores de validación
                const errors = xhr.responseJSON.errors;
                let errorMsg = 'Errores de validación:\n';
                Object.keys(errors).forEach(key => {
                    errorMsg += `- ${errors[key][0]}\n`;
                });
                alert(errorMsg);
            } else {
                alert('Error: ' + (xhr.responseJSON?.message || 'Error desconocido'));
            }
        }
    });
});

function deleteUsuario(id) {
    showConfirmModal('¿Está seguro de desactivar este usuario?', function() {
        $.ajax({
            url: `{{ url('usuarios-unificado') }}/${id}`,
            method: 'DELETE',
            data: { _token: '{{ csrf_token() }}' },
            success: function(response) {
                showAlert('success', response.message || 'Usuario desactivado correctamente');
                location.reload();
            },
            error: function(xhr) {
                showAlert('error', xhr.responseJSON?.message || 'No se pudo desactivar el usuario');
            }
        });
    });
}

function reactivateUsuario(id) {
    showConfirmModal('¿Está seguro de reactivar este usuario?', function() {
        $.ajax({
            url: `{{ url('usuarios-unificado') }}/${id}/reactivar`,
            method: 'PATCH',
            data: { _token: '{{ csrf_token() }}' },
            success: function(response) {
                showAlert('success', response.message || 'Usuario reactivado correctamente');
                location.reload();
            },
            error: function(xhr) {
                showAlert('error', xhr.responseJSON?.message || 'No se pudo reactivar el usuario');
            }
        });
    });
}

// Búsqueda rápida
document.getElementById('busquedaRapida').addEventListener('input', function() {
    const filtro = this.value.toLowerCase();
    const filas = document.querySelectorAll('#usuariosTable tbody tr');
    
    filas.forEach(function(fila) {
        const texto = fila.textContent.toLowerCase();
        if (texto.includes(filtro)) {
            fila.style.display = '';
        } else {
            fila.style.display = 'none';
        }
    });
    
    // Actualizar contador
    const filasVisibles = document.querySelectorAll('#usuariosTable tbody tr[style=""]').length;
    const contador = document.querySelector('.text-muted');
    if (contador) {
        contador.innerHTML = `<i class="fas fa-users"></i> Mostrando ${filasVisibles} usuario${filasVisibles != 1 ? 's' : ''} ${filtro ? '(filtrados)' : ''}`;
    }
});



</script>
@endsection