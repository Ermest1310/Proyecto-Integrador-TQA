@extends('layouts.gimnasta')

@section('title', 'Editar Mi Sesión')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header bg-warning">
                    <h3 class="card-title text-white"><i class="fas fa-edit"></i> Editar Mi Sesión</h3>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <h6><i class="fas fa-exclamation-circle"></i> <strong>Hay algunos problemas que debes corregir:</strong></h6>
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

                    <form method="POST" action="{{ route('gimnasta.actualizar-sesion', $sesion->ID_Sesion) }}">
                        @csrf
                        @method('PUT')
                        
                        <!-- Campo oculto para el gimnasta -->
                        <input type="hidden" name="ID_Gimnasta" value="{{ $gimnasta->ID_Usuario }}">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><i class="fas fa-user"></i> Gimnasta</label>
                                    <input type="text" class="form-control" 
                                           value="{{ $gimnasta->Nombre }} {{ $gimnasta->Apellido }} ({{ $gimnasta->Nivel }})" 
                                           readonly>
                                    <small class="text-muted">Tu información se mantiene automáticamente</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><i class="fas fa-dumbbell"></i> Aparato</label>
                                    <select name="ID_Aparato" class="form-control">
                                        <option value="">Entrenamiento general</option>
                                        @foreach($aparatos as $aparato)
                                            <option value="{{ $aparato->ID_Aparato }}" 
                                                {{ $sesion->ID_Aparato == $aparato->ID_Aparato ? 'selected' : '' }}>
                                                {{ $aparato->Nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><i class="fas fa-calendar"></i> Fecha *</label>
                                    <input type="date" name="Fecha" class="form-control" 
                                           value="{{ $sesion->Fecha->format('Y-m-d') }}" 
                                           min="{{ date('Y-m-d') }}" required onchange="checkAvailability()">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><i class="fas fa-clock"></i> Hora Inicio *</label>
                                    <input type="time" name="Hora_Inicio" class="form-control" 
                                           value="{{ $sesion->Hora_Inicio->format('H:i') }}" 
                                           min="06:00" max="21:00" required onchange="checkAvailability()">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><i class="fas fa-clock"></i> Hora Fin *</label>
                                    <input type="time" name="Hora_Fin" class="form-control" 
                                           value="{{ $sesion->Hora_Fin->format('H:i') }}" 
                                           min="07:00" max="22:00" required onchange="checkAvailability()">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="asignacionManual" 
                                               {{ $sesion->Tipo_Asignacion == 'Manual' ? 'checked' : '' }} 
                                               onchange="toggleManualAssignment()">
                                        <label class="form-check-label" for="asignacionManual">
                                            Seleccionar entrenador específico (opcional)
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row" id="entrenadorManual" style="display: {{ $sesion->Tipo_Asignacion == 'Manual' ? 'block' : 'none' }};">
                            <div class="col-12">
                                <div class="form-group">
                                    <label><i class="fas fa-user-tie"></i> Entrenador</label>
                                    <select name="ID_Entrenador" class="form-control" id="selectEntrenador">
                                        <option value="">Asignación automática</option>
                                        <option value="{{ $sesion->ID_Entrenador }}" selected>
                                            {{ $sesion->entrenador->Nombre }} {{ $sesion->entrenador->Apellido }} (Actual)
                                        </option>
                                    </select>
                                    <small class="text-muted">Solo se muestran entrenadores disponibles en el horario seleccionado</small>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle"></i> Información sobre tu sesión:</h6>
                            <ul class="mb-0">
                                <li><strong>Entrenador actual:</strong> {{ $sesion->entrenador->Nombre }} {{ $sesion->entrenador->Apellido }}</li>
                                <li><strong>Tipo de asignación:</strong> {{ $sesion->Tipo_Asignacion == 'Manual' ? 'Manual' : 'Automática' }}</li>
                                <li><strong>Duración mínima:</strong> 30 minutos por sesión</li>
                                <li><strong>Horario laboral:</strong> 6:00 AM - 10:00 PM únicamente</li>
                            </ul>
                        </div>

                        <div class="form-group text-center">
                            <button type="submit" class="btn btn-warning btn-lg">
                                <i class="fas fa-save"></i> Actualizar Mi Sesión
                            </button>
                            <a href="{{ route('gimnasta.sesiones') }}" class="btn btn-secondary btn-lg">
                                <i class="fas fa-arrow-left"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function checkAvailability() {
    const horaInicio = document.querySelector('input[name="Hora_Inicio"]').value;
    const horaFin = document.querySelector('input[name="Hora_Fin"]').value;
    
    // Limpiar alertas previas
    const alertContainer = document.getElementById('validation-alerts');
    if (alertContainer) alertContainer.remove();
    
    // Validar duración mínima y horarios laborales en tiempo real
    if (horaInicio && horaFin) {
        const inicio = new Date(`2000-01-01T${horaInicio}`);
        const fin = new Date(`2000-01-01T${horaFin}`);
        const duracion = (fin - inicio) / (1000 * 60); // minutos
        
        const horaInicioMinutos = inicio.getHours() * 60 + inicio.getMinutes();
        const horaFinMinutos = fin.getHours() * 60 + fin.getMinutos();
        
        let mensaje = '';
        if (duracion < 30) {
            mensaje = `⏱️ La sesión debe durar al menos 30 minutos. Duración actual: ${duracion} minutos`;
        } else if (horaInicioMinutos < 360) {
            mensaje = '🌅 Las sesiones no pueden iniciar antes de las 6:00 AM';
        } else if (horaFinMinutos > 1320) {
            mensaje = '🌙 Las sesiones deben terminar antes de las 10:00 PM';
        }
        
        if (mensaje) {
            showValidationAlert(mensaje, 'warning');
            document.querySelector('input[name="Hora_Fin"]').setCustomValidity(mensaje);
        } else {
            document.querySelector('input[name="Hora_Fin"]').setCustomValidity('');
        }
    }
    
    // Validar disponibilidad del gimnasta
    const gimnastaId = document.querySelector('input[name="ID_Gimnasta"]').value;
    const fecha = document.querySelector('input[name="Fecha"]').value;
    const checkbox = document.getElementById('asignacionManual');
    
    if (gimnastaId && fecha && horaInicio && horaFin) {
        fetch(`{{ route('sesiones.validar-disponibilidad') }}?id_gimnasta=${gimnastaId}&fecha=${fecha}&hora_inicio=${horaInicio}&hora_fin=${horaFin}&sesion_id={{ $sesion->ID_Sesion }}`)
            .then(response => response.json())
            .then(data => {
                if (!data.disponible) {
                    showValidationAlert(data.conflictos.join(', '), 'danger');
                }
            });
    }
    
    // Verificar disponibilidad de entrenadores si está habilitada la selección manual
    if (fecha && horaInicio && horaFin && checkbox.checked) {
        const select = document.getElementById('selectEntrenador');
        const currentValue = select.value;
        select.innerHTML = '<option value="">Cargando...</option>';
        
        fetch(`{{ route('gimnasta.entrenadores-disponibles') }}?fecha=${fecha}&hora_inicio=${horaInicio}&hora_fin=${horaFin}`)
            .then(response => response.json())
            .then(data => {
                select.innerHTML = '<option value="">Asignación automática</option>';
                
                // Agregar entrenador actual siempre
                const currentTrainerId = {{ $sesion->ID_Entrenador }};
                const isCurrentSelected = currentValue == currentTrainerId ? 'selected' : '';
                select.innerHTML += `<option value="${currentTrainerId}" ${isCurrentSelected}>{{ $sesion->entrenador->Nombre }} {{ $sesion->entrenador->Apellido }} (Actual)</option>`;
                
                // Agregar otros entrenadores disponibles
                data.forEach(entrenador => {
                    if (entrenador.ID_Usuario != currentTrainerId) {
                        const isSelected = currentValue == entrenador.ID_Usuario ? 'selected' : '';
                        select.innerHTML += `<option value="${entrenador.ID_Usuario}" ${isSelected}>${entrenador.Nombre} ${entrenador.Apellido}</option>`;
                    }
                });
                
                if (data.length === 0) {
                    select.innerHTML += '<option value="" disabled>No hay otros entrenadores disponibles</option>';
                }
            })
            .catch(error => {
                select.innerHTML = '<option value="">Error al cargar entrenadores</option>';
            });
    }
}

function toggleManualAssignment() {
    const checkbox = document.getElementById('asignacionManual');
    const entrenadorDiv = document.getElementById('entrenadorManual');
    
    if (checkbox.checked) {
        entrenadorDiv.style.display = 'block';
        checkAvailability();
    } else {
        entrenadorDiv.style.display = 'none';
        document.querySelector('select[name="ID_Entrenador"]').value = '';
    }
}

// Cargar entrenadores al cargar la página si ya está marcado como manual
document.addEventListener('DOMContentLoaded', function() {
    const checkbox = document.getElementById('asignacionManual');
    if (checkbox && checkbox.checked) {
        checkAvailability();
    }
});

function showValidationAlert(message, type = 'warning') {
    const form = document.querySelector('form');
    const alertDiv = document.createElement('div');
    alertDiv.id = 'validation-alerts';
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        <i class="fas fa-exclamation-triangle"></i> ${message}
        <button type="button" class="close" onclick="this.parentElement.remove()">
            <span>&times;</span>
        </button>
    `;
    form.insertBefore(alertDiv, form.firstChild);
}
</script>
@endsection