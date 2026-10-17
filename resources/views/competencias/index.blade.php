@extends('layouts.admin')

@section('title', 'Listado de Competencias')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Lista de Competencias ({{ $competencias->count() }} registros)</h3>
        <button type="button" class="btn btn-warning btn-sm float-right" data-toggle="modal" data-target="#createModal">
            <i class="fas fa-medal"></i> Nueva Competencia
        </button>
    </div>
    <div class="card-body">
        <div id="alertContainer"></div>
        
        <!-- Filtros -->
        <div class="row mb-3">
            <div class="col-md-4">
                <input type="text" id="buscar" class="form-control" placeholder="Buscar competencia...">
            </div>
            <div class="col-md-3">
                <select id="filtroEstado" class="form-control">
                    <option value="">Todos los estados</option>
                    <option value="activo">Activos</option>
                    <option value="inactivo">Inactivos</option>
                </select>
            </div>
            <div class="col-md-3">
                <select id="filtroFecha" class="form-control">
                    <option value="">Todas las fechas</option>
                    <option value="pasadas">Competencias pasadas</option>
                    <option value="futuras">Competencias futuras</option>
                    <option value="este_mes">Este mes</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-secondary" onclick="limpiarFiltros()">
                    <i class="fas fa-times"></i> Limpiar
                </button>
            </div>
        </div>

        <table class="table table-bordered table-hover" id="competenciasTable">
            <thead>
                <tr>
                    <th>COD</th>
                    <th>Nombre</th>
                    <th>Fecha</th>
                    <th>Lugar</th>
                    <th>Estado</th>
                    <th width="120">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($competencias as $competencia)
                    <tr id="competencia-{{ $competencia->ID_Competencia }}">
                        <td>{{ $competencia->ID_Competencia }}</td>
                        <td>{{ $competencia->NombreCompetencia }}</td>
                        <td>{{ $competencia->Fecha->format('d/m/Y') }}</td>
                        <td>{{ $competencia->Ubicacion }}</td>
                        <td>
                            <span class="badge badge-{{ $competencia->activo ? 'success' : 'danger' }}">
                                {{ $competencia->activo ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td style="white-space: nowrap;">
                            <button class="btn btn-info btn-sm mr-1" onclick="editCompetencia({{ $competencia->ID_Competencia }})" title="Editar">
                                <i class="fas fa-edit"></i>
                            </button>
                            @if($competencia->activo)
                                <button class="btn btn-secondary btn-sm" onclick="toggleCompetencia({{ $competencia->ID_Competencia }}, false)" title="Desactivar">
                                    <i class="fas fa-pause-circle"></i>
                                </button>
                            @else
                                <button class="btn btn-warning btn-sm" onclick="toggleCompetencia({{ $competencia->ID_Competencia }}, true)" title="Activar">
                                    <i class="fas fa-play-circle"></i>
                                </button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@include('competencias.create')
@include('competencias.edit')
@endsection

@section('scripts')
@include('competencias.show')

<script>
// Filtros
function aplicarFiltros() {
    const buscar = document.getElementById('buscar').value.toLowerCase();
    const estado = document.getElementById('filtroEstado').value;
    const fecha = document.getElementById('filtroFecha').value;
    
    const filas = document.querySelectorAll('#competenciasTable tbody tr');
    let visibles = 0;
    
    filas.forEach(function(fila) {
        const celdas = fila.querySelectorAll('td');
        const textoCompleto = fila.textContent.toLowerCase();
        const estadoTexto = celdas[4].textContent.toLowerCase();
        const fechaTexto = celdas[2].textContent;
        
        let mostrar = true;
        
        if (buscar && !textoCompleto.includes(buscar)) mostrar = false;
        if (estado && !estadoTexto.includes(estado)) mostrar = false;
        
        if (fecha) {
            const fechaCompetencia = new Date(fechaTexto.split('/').reverse().join('-'));
            const hoy = new Date();
            const inicioMes = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
            const finMes = new Date(hoy.getFullYear(), hoy.getMonth() + 1, 0);
            
            if (fecha === 'pasadas' && fechaCompetencia >= hoy) mostrar = false;
            if (fecha === 'futuras' && fechaCompetencia < hoy) mostrar = false;
            if (fecha === 'este_mes' && (fechaCompetencia < inicioMes || fechaCompetencia > finMes)) mostrar = false;
        }
        
        fila.style.display = mostrar ? '' : 'none';
        if (mostrar) visibles++;
    });
}

function limpiarFiltros() {
    document.getElementById('buscar').value = '';
    document.getElementById('filtroEstado').value = '';
    document.getElementById('filtroFecha').value = '';
    aplicarFiltros();
}

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('buscar').addEventListener('input', aplicarFiltros);
    document.getElementById('filtroEstado').addEventListener('change', aplicarFiltros);
    document.getElementById('filtroFecha').addEventListener('change', aplicarFiltros);
    
    document.getElementById('buscar').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            aplicarFiltros();
        }
    });
});
</script>
@endsection