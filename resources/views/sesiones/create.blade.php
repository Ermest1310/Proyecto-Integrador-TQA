@extends('layouts.admin')

@section('title', 'Nueva Sesión de Entrenamiento')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header bg-success">
                    <h3 class="card-title text-white"><i class="fas fa-plus"></i> Programar Nueva Sesión</h3>
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

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert">
                                <span>&times;</span>
                            </button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('sesiones.store') }}">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><i class="fas fa-user"></i> Gimnasta *</label>
                                    <select name="ID_Gimnasta" class="form-control" required onchange="checkAvailability()">
                                        <option value="">Seleccionar gimnasta...</option>
                                        @foreach($gimnastas as $gimnasta)
                                            <option value="{{ $gimnasta->ID_Usuario }}" {{ old('ID_Gimnasta') == $gimnasta->ID_Usuario ? 'selected' : '' }}>
                                                {{ $gimnasta->Nombre }} {{ $gimnasta->Apellido }} ({{ $gimnasta->Nivel }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Selecciona el gimnasta para la sesión</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><i class="fas fa-dumbbell"></i> Aparato</label>
                                    <select name="ID_Aparato" class="form-control">
                                        <option value="">Entrenamiento general</option>
                                        @foreach($aparatos as $aparato)
                                            <option value="{{ $aparato->ID_Aparato }}" {{ old('ID_Aparato') == $aparato->ID_Aparato ? 'selected' : '' }}>
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
                                    <input type="date" name="Fecha" class="form-control" value="{{ old('Fecha') }}" 
                                           min="{{ date('Y-m-d') }}" required onchange="checkAvailability()">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><i class="fas fa-clock"></i> Hora Inicio *</label>
                                    <input type="time" name="Hora_Inicio" class="form-control" value="{{ old('Hora_Inicio') }}" 
                                           min="06:00" max="21:00" required onchange="checkAvailability()">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><i class="fas fa-clock"></i> Hora Fin *</label>
                                    <input type="time" name="Hora_Fin" class="form-control" value="{{ old('Hora_Fin') }}" 
                                           min="07:00" max="22:00" required onchange="checkAvailability()">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="asignacionManual" onchange="toggleManualAssignment()">
                                        <label class="form-check-label" for="asignacionManual">
                                            Seleccionar entrenador específico (opcional)
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row" id="entrenadorManual" style="display: none;">
                            <div class="col-12">
                                <div class="form-group">
                                    <label><i class="fas fa-user-tie"></i> Entrenador</label>
                                    <select name="ID_Entrenador" class="form-control" id="selectEntrenador">
                                        <option value="">Verificando disponibilidad...</option>
                                    </select>
                                    <small class="text-muted">Solo se muestran entrenadores disponibles en el horario seleccionado</small>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info alert-dismissible fade show">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <h6><i class="fas fa-info-circle"></i> Información importante:</h6>
                            <ul class="mb-0">
                                <li><strong>Asignación automática:</strong> Se asignará el entrenador más disponible</li>
                                <li><strong>Duración mínima:</strong> 30 minutos por sesión</li>
                                <li><strong>Horario laboral:</strong> 6:00 AM - 10:00 PM únicamente</li>
                                <li><strong>Fechas futuras:</strong> Solo puedes programar para hoy o fechas futuras</li>
                            </ul>
                        </div>

                        <div class="form-group text-center">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-save"></i> Programar Sesión
                            </button>
                            <a href="{{ route('sesiones.index') }}" class="btn btn-secondary btn-lg">
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
    
    // Validar disponibilidad del gimnasta
    const gimnastaId = document.querySelector('select[name="ID_Gimnasta"]').value;
    if (gimnastaId && fecha && horaInicio && horaFin) {
        fetch(`{{ route('sesiones.validar-disponibilidad') }}?id_gimnasta=${gimnastaId}&fecha=${fecha}&hora_inicio=${horaInicio}&hora_fin=${horaFin}`)
            .then(response => response.json())
            .then(data => {
                if (!data.disponible) {
                    showValidationAlert(data.conflictos.join(', '), 'danger');
                }
            });
    }
    
    if (fecha && horaInicio && horaFin && checkbox.checked) {
        const select = document.getElementById('selectEntrenador');
        select.innerHTML = '<option value="">Cargando...</option>';
        
        fetch(`{{ route('sesiones.entrenadores-disponibles') }}?fecha=${fecha}&hora_inicio=${horaInicio}&hora_fin=${horaFin}`)
            .then(response => response.json())
            .then(data => {
                select.innerHTML = '<option value="">Asignación automática</option>';
                data.forEach(entrenador => {
                    select.innerHTML += `<option value="${entrenador.ID_Usuario}">${entrenador.Nombre} ${entrenador.Apellido}</option>`;
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