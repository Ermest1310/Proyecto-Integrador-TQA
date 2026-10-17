@extends('layouts.admin')

@section('title', 'Editar Sesión de Entrenamiento')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header bg-warning">
                    <h3 class="card-title text-white"><i class="fas fa-edit"></i> Editar Sesión de Entrenamiento</h3>
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
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if($sesion->Estado !== 'Programada')
                        <div class="alert alert-danger">
                            <h6><i class="fas fa-lock"></i> <strong>Sesión no editable</strong></h6>
                            <p class="mb-0">Esta sesión está <strong>{{ $sesion->Estado == 'Completada' ? 'completada' : 'cancelada' }}</strong> y no puede ser modificada. Solo las sesiones programadas pueden editarse.</p>
                        </div>
                        <div class="text-center">
                            <a href="{{ route('sesiones.index') }}" class="btn btn-secondary btn-lg">
                                <i class="fas fa-arrow-left"></i> Volver al listado
                            </a>
                        </div>
                    @else
                    <form method="POST" action="{{ route('sesiones.update', $sesion->ID_Sesion) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><i class="fas fa-user"></i> Gimnasta *</label>
                                    <select name="ID_Gimnasta" class="form-control" required>
                                        <option value="">Seleccionar gimnasta...</option>
                                        @foreach($gimnastas as $gimnasta)
                                            <option value="{{ $gimnasta->ID_Usuario }}" 
                                                {{ $sesion->ID_Gimnasta == $gimnasta->ID_Usuario ? 'selected' : '' }}>
                                                {{ $gimnasta->Nombre }} {{ $gimnasta->Apellido }} ({{ $gimnasta->Nivel }})
                                            </option>
                                        @endforeach
                                    </select>
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
                                        @if($sesion->ID_Entrenador)
                                            <option value="{{ $sesion->ID_Entrenador }}" selected>
                                                {{ $sesion->entrenador->Nombre }} {{ $sesion->entrenador->Apellido }} (Actual)
                                            </option>
                                        @endif
                                    </select>
                                    <small class="text-muted">Solo se muestran entrenadores disponibles en el horario seleccionado</small>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle"></i> Información importante:</h6>
                            <ul class="mb-0">
                                <li><strong>Asignación automática:</strong> Se asignará el entrenador más disponible</li>
                                <li><strong>Duración mínima:</strong> 30 minutos por sesión</li>
                                <li><strong>Horario laboral:</strong> 6:00 AM - 10:00 PM únicamente</li>
                                <li><strong>Solo sesiones programadas</strong> pueden ser editadas</li>
                            </ul>
                        </div>

                        <div class="form-group text-center">
                            <button type="submit" class="btn btn-warning btn-lg">
                                <i class="fas fa-save"></i> Actualizar Sesión
                            </button>
                            <a href="{{ route('sesiones.index') }}" class="btn btn-secondary btn-lg">
                                <i class="fas fa-arrow-left"></i> Cancelar
                            </a>
                        </div>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
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

function checkAvailability() {
    const fecha = document.querySelector('input[name="Fecha"]').value;
    const horaInicio = document.querySelector('input[name="Hora_Inicio"]').value;
    const horaFin = document.querySelector('input[name="Hora_Fin"]').value;
    const checkbox = document.getElementById('asignacionManual');
    
    // Limpiar alertas previas
    const alertContainer = document.getElementById('validation-alerts');
    if (alertContainer) alertContainer.remove();
    
    // Validar duración mínima y horarios laborales en tiempo real
    if (horaInicio && horaFin) {
        const inicio = new Date(`2000-01-01T${horaInicio}`);
        const fin = new Date(`2000-01-01T${horaFin}`);
        const duracion = (fin - inicio) / (1000 * 60); // minutos
        
        const horaInicioMinutos = inicio.getHours() * 60 + inicio.getMinutes();
        const horaFinMinutos = fin.getHours() * 60 + fin.getMinutes();
        
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
    
    // Validar disponibilidad del gimnasta (excluyendo la sesión actual)
    const gimnastaId = document.querySelector('select[name="ID_Gimnasta"]').value;
    if (gimnastaId && fecha && horaInicio && horaFin) {
        fetch(`{{ route('sesiones.validar-disponibilidad') }}?id_gimnasta=${gimnastaId}&fecha=${fecha}&hora_inicio=${horaInicio}&hora_fin=${horaFin}&sesion_id={{ $sesion->ID_Sesion }}`)
            .then(response => response.json())
            .then(data => {
                if (!data.disponible) {
                    showValidationAlert(data.conflictos.join(', '), 'danger');
                }
            });
    }
    
    if (fecha && horaInicio && horaFin && checkbox.checked) {
        const select = document.getElementById('selectEntrenador');
        const currentValue = select.value;
        select.innerHTML = '<option value="">Cargando...</option>';
        
        fetch(`{{ route('sesiones.entrenadores-disponibles') }}?fecha=${fecha}&hora_inicio=${horaInicio}&hora_fin=${horaFin}&sesion_id={{ $sesion->ID_Sesion }}`)
            .then(response => response.json())
            .then(data => {
                select.innerHTML = '<option value="">Asignación automática</option>';
                data.forEach(entrenador => {
                    const isSelected = currentValue == entrenador.ID_Usuario ? 'selected' : '';
                    select.innerHTML += `<option value="${entrenador.ID_Usuario}" ${isSelected}>${entrenador.Nombre} ${entrenador.Apellido}</option>`;
                });
                
                if (data.length === 0) {
                    select.innerHTML = '<option value="">No hay entrenadores disponibles</option>';
                }
            })
            .catch(error => {
                select.innerHTML = '<option value="">Error al cargar entrenadores</option>';
            });
    }
}

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