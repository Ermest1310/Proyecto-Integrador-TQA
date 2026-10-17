@extends('layouts.admin')

@section('title', 'Listado de Lesiones')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Lista de Lesiones ({{ $lesiones->count() }} registros)</h3>
        <button type="button" class="btn btn-danger btn-sm float-right" data-toggle="modal" data-target="#createModal">
            <i class="fas fa-plus-circle"></i> Nueva Lesión
        </button>
    </div>
    <div class="card-body">
        <div id="alertContainer"></div>
        
        <!-- Filtros -->
        <div class="row mb-3">
            <div class="col-md-3">
                <input type="text" id="buscar" class="form-control" placeholder="Buscar lesión...">
            </div>
            <div class="col-md-3">
                <select id="filtroGimnasta" class="form-control">
                    <option value="">Todos los gimnastas</option>
                    @foreach($gimnastas as $gimnasta)
                        <option value="{{ $gimnasta->Nombre }} {{ $gimnasta->Apellido }}">
                            {{ $gimnasta->Nombre }} {{ $gimnasta->Apellido }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select id="filtroEstadoLesion" class="form-control">
                    <option value="">Todos los estados</option>
                    <option value="Lesionado">Lesionado</option>
                    <option value="En Rehabilitación">En Rehabilitación</option>
                    <option value="Recuperada">Recuperada</option>
                </select>
            </div>
            <div class="col-md-2">
                <select id="filtroActivo" class="form-control">
                    <option value="">Todos</option>
                    <option value="activo">Activos</option>
                    <option value="inactivo">Inactivos</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-secondary" onclick="limpiarFiltros()">
                    <i class="fas fa-times"></i> Limpiar
                </button>
            </div>
        </div>

        @if($lesiones->count() > 0)
            <table class="table table-bordered table-hover" id="lesionesTable">
                <thead>
                    <tr>
                        <th>COD</th>
                        <th>Gimnasta</th>
                        <th>Tipo</th>
                        <th>Parte del Cuerpo</th>
                        <th>Fecha Lesión</th>
                        <th>Gravedad</th>
                        <th>Estado</th>
                        <th>Fecha Recuperación</th>
                        <th>Activo</th>
                        <th width="120">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($lesiones as $lesion)
                        <tr id="lesion-{{ $lesion->ID_Lesion }}">
                            <td>{{ $lesion->ID_Lesion }}</td>
                            <td>{{ $lesion->usuario->Nombre ?? 'N/A' }} {{ $lesion->usuario->Apellido ?? '' }}</td>
                            <td>{{ $lesion->Tipo }}</td>
                            <td>{{ $lesion->ParteCuerpo ?? 'N/A' }}</td>
                            <td>{{ $lesion->FechaLesion ? $lesion->FechaLesion->format('d/m/Y') : 'N/A' }}</td>
                            <td>
                                <span class="badge badge-{{ $lesion->Gravedad === 'Leve' ? 'info' : ($lesion->Gravedad === 'Moderada' ? 'warning' : 'danger') }}">
                                    {{ $lesion->Gravedad ?? 'N/A' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-{{ $lesion->Estado === 'Recuperada' ? 'success' : ($lesion->Estado === 'En Rehabilitación' ? 'warning' : 'danger') }}">
                                    {{ $lesion->Estado ?? 'Lesionado' }}
                                </span>
                            </td>
                            <td>{{ $lesion->FechaRecuperacion ? $lesion->FechaRecuperacion->format('d/m/Y') : 'N/A' }}</td>
                            <td>
                                <span class="badge badge-{{ $lesion->activo ? 'success' : 'danger' }}">
                                    {{ $lesion->activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td style="white-space: nowrap;">
                                <button class="btn btn-info btn-sm mr-1" onclick="editLesion({{ $lesion->ID_Lesion }})" title="Editar">
                                    <i class="fas fa-stethoscope"></i>
                                </button>
                                @if($lesion->activo)
                                    <button class="btn btn-secondary btn-sm" onclick="toggleLesion({{ $lesion->ID_Lesion }}, false)" title="Desactivar">
                                        <i class="fas fa-archive"></i>
                                    </button>
                                @else
                                    <button class="btn btn-success btn-sm" onclick="toggleLesion({{ $lesion->ID_Lesion }}, true)" title="Activar">
                                        <i class="fas fa-redo"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> No hay lesiones registradas. Haz clic en "Nueva Lesión" para agregar una.
            </div>
        @endif
    </div>
</div>

@include('lesiones.create')
@include('lesiones.edit')
@endsection

@section('scripts')
@include('lesiones.show')

<script>
// Filtros
function aplicarFiltros() {
    const buscar = document.getElementById('buscar').value.toLowerCase();
    const gimnasta = document.getElementById('filtroGimnasta').value;
    const estadoLesion = document.getElementById('filtroEstadoLesion').value;
    const activo = document.getElementById('filtroActivo').value;
    
    const filas = document.querySelectorAll('#lesionesTable tbody tr');
    let visibles = 0;
    
    filas.forEach(function(fila) {
        const celdas = fila.querySelectorAll('td');
        const textoCompleto = fila.textContent.toLowerCase();
        const gimnastaTexto = celdas[1].textContent.trim();
        const estadoLesionTexto = celdas[6].textContent.trim();
        const activoTexto = celdas[8].textContent.toLowerCase();
        
        let mostrar = true;
        
        if (buscar && !textoCompleto.includes(buscar)) mostrar = false;
        if (gimnasta && gimnastaTexto !== gimnasta) mostrar = false;
        if (estadoLesion && !estadoLesionTexto.includes(estadoLesion)) mostrar = false;
        if (activo && !activoTexto.includes(activo)) mostrar = false;
        
        fila.style.display = mostrar ? '' : 'none';
        if (mostrar) visibles++;
    });
}

function limpiarFiltros() {
    document.getElementById('buscar').value = '';
    document.getElementById('filtroGimnasta').value = '';
    document.getElementById('filtroEstadoLesion').value = '';
    document.getElementById('filtroActivo').value = '';
    aplicarFiltros();
}

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('buscar').addEventListener('input', aplicarFiltros);
    document.getElementById('filtroGimnasta').addEventListener('change', aplicarFiltros);
    document.getElementById('filtroEstadoLesion').addEventListener('change', aplicarFiltros);
    document.getElementById('filtroActivo').addEventListener('change', aplicarFiltros);
    
    document.getElementById('buscar').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            aplicarFiltros();
        }
    });
});
</script>
@endsection