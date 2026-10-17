@extends('layouts.medico')

@section('title', 'Rehabilitación y Seguimiento de Lesiones')

@section('content')
<div class="container-fluid">

    <div class="card card-info mb-4">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-running"></i> Módulo de Rehabilitación Deportiva</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    {{-- Requiere la variable $atletas desde el controlador --}}
                    <label for="atleta-selector"><i class="fas fa-user-injured"></i> Seleccionar Gimnasta:</label>
                    <select id="atleta-selector" class="form-control" onchange="cargarEstadisticas(this.value)">
                        <option value="">-- Selecciona un atleta --</option>
                        {{-- Ejemplo de bucle: --}}
                        @foreach($atletas as $atleta)
                            <option value="{{ $atleta->id }}" {{ (isset($atletaSeleccionado) && $atletaSeleccionado->id == $atleta->id) ? 'selected' : '' }}>
                                {{ $atleta->nombre }} ({{ $atleta->lesion_actual ?? 'Sin lesión' }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-8 d-flex align-items-center">
                    <p class="mb-0 pt-3 pt-md-0 text-muted">Utiliza el selector para ver el plan de rehabilitación y el progreso de recuperación del atleta.</p>
                </div>
            </div>
        </div>
    </div>

    @if(isset($atletaSeleccionado))
    <div id="rehabilitacion-data">
        
        <div class="card card-outline card-primary mb-4">
            <div class="card-header">
                <h4 class="card-title"><i class="fas fa-user-tag"></i> Datos del Atleta: {{ $atletaSeleccionado->nombre }}</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Lesión Principal:</strong> <span class="badge bg-danger">{{ $atletaSeleccionado->lesion_actual ?? 'N/A' }}</span></p>
                        <p><strong>Fecha de Diagnóstico:</strong> {{ $atletaSeleccionado->fecha_diagnostico ?? 'N/A' }}</p>
                        <p><strong>Fisioterapeuta Asignado:</strong> {{ $atletaSeleccionado->terapeuta_asignado ?? 'Pendiente' }}</p>
                    </div>
                    <div class="col-md-6">
                        {{-- Requiere la variable $progresoGeneral desde el controlador --}}
                        <p><strong>Progreso General:</strong> 
                            <span class="text-info font-weight-bold">{{ $progresoGeneral ?? '0' }}% completado</span>
                        </p>
                        <div class="progress progress-sm mt-2">
                            <div class="progress-bar bg-info" style="width: {{ $progresoGeneral ?? 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-8">
                <div class="card card-outline card-success">
                    <div class="card-header">
                        <h4 class="card-title"><i class="fas fa-chart-line"></i> Progreso de Recuperación (Métricas Clave)</h4>
                    </div>
                    <div class="card-body">
                        {{-- Aquí se insertaría un gráfico generado con una librería JS (Chart.js, etc.) --}}
                        <p class="text-muted text-center">
                            [**Gráfico de Tendencia de Mejora:** Visualización de la fuerza, rango de movimiento o puntaje de dolor a lo largo de las sesiones.]
                        </p>
                        <div id="chart-container" style="height: 250px;">
                            {{-- Placeholder para el gráfico --}}
                            
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card card-outline card-warning h-100">
                    <div class="card-header">
                        <h4 class="card-title"><i class="fas fa-tachometer-alt"></i> Métrica Actual</h4>
                    </div>
                    <div class="card-body text-center d-flex flex-column justify-content-center">
                        {{-- Requiere la variable $ultimaMetrica --}}
                        <h4>Fuerza (última medición)</h4>
                        <p class="display-4 text-warning">{{ $ultimaMetrica->fuerza ?? 'N/A' }} Kg</p>
                        <p class="text-sm text-muted">Rango de Movimiento: {{ $ultimaMetrica->rom ?? 'N/A' }} grados</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-outline card-secondary mt-3">
            <div class="card-header">
                <h4 class="card-title"><i class="fas fa-notes-medical"></i> Registro de Sesiones de Fisioterapia</h4>
            </div>
            <div class="card-body">
                {{-- Requiere la variable $sesionesHistorial --}}
                @if(!empty($sesionesHistorial) && count($sesionesHistorial) > 0)
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Terapeuta</th>
                            <th>Foco de la Sesión</th>
                            <th>Puntaje de Dolor (1-10)</th>
                            <th>Notas de Progreso</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sesionesHistorial as $sesion)
                        <tr>
                            <td>{{ $sesion->fecha ?? 'N/A' }}</td>
                            <td>{{ $sesion->terapeuta ?? 'N/A' }}</td>
                            <td>{{ $sesion->foco ?? 'Ejercicios de fortalecimiento' }}</td>
                            <td>
                                <span class="badge {{ $sesion->dolor <= 3 ? 'bg-success' : ($sesion->dolor <= 6 ? 'bg-warning' : 'bg-danger') }}">
                                    {{ $sesion->dolor ?? 'N/A' }}
                                </span>
                            </td>
                            <td>{{ $sesion->notas_progreso ?? 'Sin observaciones.' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <p class="text-muted text-center">No hay sesiones de rehabilitación registradas para este atleta.</p>
                @endif
            </div>
        </div>

    </div>
    @else
        <div class="alert alert-secondary text-center">
            <i class="fas fa-hand-point-up"></i> Por favor, selecciona un gimnasta de la lista desplegable para ver su progreso detallado de rehabilitación.
        </div>
    @endif

</div>

@endsection

@section('scripts')
{{-- Placeholder para el script de JavaScript/AJAX --}}
<script>
    function cargarEstadisticas(atletaId) {
        if (atletaId) {
            // Aquí iría el código AJAX para hacer una solicitud al backend de Laravel
            // y cargar dinámicamente el contenido de #rehabilitacion-data
            // Por ejemplo:
            // window.location.href = '/area-medica/rehabilitacion?atleta=' + atletaId;
        } else {
            document.getElementById('rehabilitacion-data').innerHTML = ''; // Limpiar si no hay selección
        }
    }
</script>
@endsection