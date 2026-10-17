@extends('layouts.admin')

@section('title', 'Listado de Entrenamientos')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Lista de Entrenamientos ({{ $entrenamientos->count() }} registros)</h3>
        <button type="button" class="btn btn-info btn-sm float-right" data-toggle="modal" data-target="#createModal">
            <i class="fas fa-plus-circle"></i> Nuevo Entrenamiento
        </button>
    </div>
    <div class="card-body">
        <div id="alertContainer"></div>
        
        <!-- Filtros -->
        <div class="row mb-3">
            <div class="col-md-3">
                <input type="text" id="busquedaRapida" class="form-control" placeholder="Buscar gimnasta..." value="{{ request('buscar') }}">
            </div>
            <div class="col-md-3">
                <select id="filtroGimnasta" class="form-control">
                    <option value="">Todos los gimnastas</option>
                    @foreach($gimnastas as $gimnasta)
                        <option value="{{ $gimnasta->ID_Usuario }}" {{ request('gimnasta') == $gimnasta->ID_Usuario ? 'selected' : '' }}>
                            {{ $gimnasta->Nombre }} {{ $gimnasta->Apellido }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select id="filtroAparato" class="form-control">
                    <option value="">Todos los aparatos</option>
                    @foreach($aparatos as $aparato)
                        <option value="{{ $aparato->ID_Aparato }}" {{ request('aparato') == $aparato->ID_Aparato ? 'selected' : '' }}>
                            {{ $aparato->Nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-primary" onclick="aplicarFiltros()">Filtrar</button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('entrenamientos.index') }}" class="btn btn-secondary">Limpiar</a>
            </div>
        </div>

        <table class="table table-bordered table-hover" id="entrenamientosTable">
            <thead>
                <tr>
                    <th>COD</th>
                    <th>Gimnasta</th>
                    <th>Aparato</th>
                    <th>Fecha</th>
                    <th>Duración (min)</th>
                    <th>Observaciones</th>
                    <th width="120">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($entrenamientos as $entrenamiento)
                    <tr id="entrenamiento-{{ $entrenamiento->ID_Entrenamiento }}">
                        <td>{{ $entrenamiento->ID_Entrenamiento }}</td>
                        <td>{{ $entrenamiento->gimnasta->Nombre ?? 'N/A' }} {{ $entrenamiento->gimnasta->Apellido ?? '' }}</td>
                        <td>{{ $entrenamiento->aparato->Nombre ?? 'N/A' }}</td>
                        <td>{{ $entrenamiento->Fecha->format('d/m/Y') }}</td>
                        <td>{{ $entrenamiento->Duracion }} min</td>
                        <td>{{ $entrenamiento->Observaciones ?? '-' }}</td>
                        <td style="white-space: nowrap;">
                            <button class="btn btn-primary btn-sm mr-1" onclick="editEntrenamiento({{ $entrenamiento->ID_Entrenamiento }})" title="Editar">
                                <i class="fas fa-clock"></i>
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="deleteEntrenamiento({{ $entrenamiento->ID_Entrenamiento }})" title="Eliminar">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@include('entrenamientos.create')
@include('entrenamientos.edit')
@endsection

@section('scripts')
@include('entrenamientos.show')
@endsection