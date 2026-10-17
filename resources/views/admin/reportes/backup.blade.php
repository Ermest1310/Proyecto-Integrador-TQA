@extends('layouts.admin')

@section('title', 'Gestión de Backups')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="fas fa-database text-success"></i> Gestión de Backups</h1>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-success">
                <h3 class="card-title">Crear Nuevo Backup</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.reportes.crear-backup') }}" method="POST" class="form-inline">
                    @csrf
                    <div class="form-group mr-3">
                        <label class="mr-2">Tipo de Backup:</label>
                        <select name="type" class="form-control">
                            <option value="completo">Completo</option>
                            <option value="incremental">Incremental</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Crear Backup
                    </button>
                </form>
                <div class="alert alert-info mt-3">
                    <i class="fas fa-info-circle"></i> 
                    <strong>Backup Completo:</strong> Copia toda la base de datos.<br>
                    <strong>Backup Incremental:</strong> Solo guarda los cambios desde el último backup.
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Backups Disponibles</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Nombre del Archivo</th>
                                <th>Tamaño</th>
                                <th>Fecha de Creación</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($backups as $backup)
                            <tr>
                                <td>
                                    <i class="fas fa-file-archive text-success"></i> 
                                    {{ $backup['nombre'] }}
                                </td>
                                <td>{{ $backup['tamano'] }} MB</td>
                                <td>{{ $backup['fecha'] }}</td>
                                <td>
                                    <a href="{{ route('admin.reportes.descargar-backup', $backup['nombre']) }}" 
                                       class="btn btn-sm btn-primary">
                                        <i class="fas fa-download"></i> Descargar
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">No hay backups disponibles</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
