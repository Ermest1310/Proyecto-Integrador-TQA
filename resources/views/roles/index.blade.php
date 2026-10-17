@extends('layouts.admin')

@section('title', 'Roles y Permisos')

@php
    // Agrupar permisos por módulo (prefijo antes del punto)
    $grupos = [];
    foreach ($permisos as $rol => $perms) {
        foreach ($perms as $p) {
            $parts = explode('.', $p, 2);
            $modulo = $parts[0];
            $accion = $parts[1] ?? $p;
            $grupos[$modulo][$p] = $accion;
        }
    }
    $nombresRoles = array_keys($permisos);
@endphp

@section('content')
<div class="row mb-4">
    <div class="col-lg-4 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $roles->count() }}</h3>
                <p>Roles Registrados</p>
            </div>
            <div class="icon"><i class="fas fa-user-tag"></i></div>
        </div>
    </div>
    <div class="col-lg-4 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $totalPermisos }}</h3>
                <p>Permisos Distintos</p>
            </div>
            <div class="icon"><i class="fas fa-shield-alt"></i></div>
        </div>
    </div>
    <div class="col-lg-4 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $totalUsuarios }}</h3>
                <p>Usuarios Activos</p>
            </div>
            <div class="icon"><i class="fas fa-users"></i></div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header p-0 pt-1">
        <ul class="nav nav-tabs" id="rolesTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="roles-tab" data-toggle="tab" href="#rolesTab" role="tab">
                    <i class="fas fa-list mr-1"></i> Roles
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="permisos-tab" data-toggle="tab" href="#permisosTab" role="tab">
                    <i class="fas fa-shield-alt mr-1"></i> Matriz de Permisos
                </a>
            </li>
        </ul>
    </div>
    <div class="card-body">
        <div class="tab-content">
            {{-- TAB 1: ROLES --}}
            <div class="tab-pane active" id="rolesTab" role="tabpanel">
                <div class="mb-3">
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createModal">
                        <i class="fas fa-plus"></i> Nuevo Rol
                    </button>
                </div>
                <div id="alertContainer"></div>
                <table class="table table-bordered table-hover" id="rolesTable">
                    <thead>
                        <tr>
                            <th>COD</th>
                            <th>Nombre del Rol</th>
                            <th>Usuarios Asignados</th>
                            <th>Estado</th>
                            <th width="120">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($roles as $rol)
                            <tr id="rol-{{ $rol->ID_Rol }}">
                                <td>{{ $rol->ID_Rol }}</td>
                                <td>
                                    <strong>{{ $rol->NombreRol }}</strong>
                                    @if(in_array($rol->NombreRol, ['Administrador', 'Gimnasta', 'Médico', 'Fisioterapeuta', 'Entrenador']))
                                        <span class="badge badge-info ml-1" title="Rol protegido del sistema"><i class="fas fa-lock"></i></span>
                                    @endif
                                </td>
                                <td>
                                    @php $count = $usuariosPorRol[$rol->ID_Rol] ?? 0; @endphp
                                    <span class="badge badge-{{ $count > 0 ? 'primary' : 'secondary' }}">
                                        {{ $count }} usuario{{ $count !== 1 ? 's' : '' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $rol->activo ? 'success' : 'danger' }}">
                                        {{ $rol->activo ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                                <td style="white-space: nowrap;">
                                    <button class="btn btn-warning btn-sm mr-1" onclick="editRol({{ $rol->ID_Rol }})" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    @if($rol->activo)
                                        <button class="btn btn-danger btn-sm" onclick="toggleRol({{ $rol->ID_Rol }}, false)" title="Desactivar">
                                            <i class="fas fa-ban"></i>
                                        </button>
                                    @else
                                        <button class="btn btn-success btn-sm" onclick="toggleRol({{ $rol->ID_Rol }}, true)" title="Activar">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- TAB 2: MATRIZ DE PERMISOS --}}
            <div class="tab-pane" id="permisosTab" role="tabpanel">
                <p class="text-muted mb-3">
                    <i class="fas fa-info-circle mr-1"></i>
                    Matriz de permisos del sistema. Cada <span class="badge badge-success">&check;</span> indica que el rol tiene ese permiso.
                </p>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="permisosTable">
                        <thead class="thead-dark">
                            <tr>
                                <th style="min-width:200px">Permiso</th>
                                @foreach($nombresRoles as $rol)
                                    <th class="text-center" style="min-width:100px">
                                        {{ $rol }}
                                        @php
                                            $rolModel = $roles->firstWhere('NombreRol', $rol);
                                            $ucount = $rolModel ? ($usuariosPorRol[$rolModel->ID_Rol] ?? 0) : 0;
                                        @endphp
                                        @if($rolModel)
                                            <br><small class="text-muted">{{ $ucount }} usuario{{ $ucount !== 1 ? 's' : '' }}</small>
                                        @endif
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($grupos as $modulo => $acciones)
                                <tr class="bg-light font-weight-bold">
                                    <td colspan="{{ count($nombresRoles) + 1 }}" class="text-uppercase">
                                        {{ ucfirst($modulo) }}
                                    </td>
                                </tr>
                                @foreach($acciones as $permisoCompleto => $accion)
                                    <tr>
                                        <td>
                                            <span class="permiso-nombre">{{ ucfirst(str_replace('_', ' ', $accion)) }}</span>
                                            <br><small class="text-muted">{{ $permisoCompleto }}</small>
                                        </td>
                                        @foreach($nombresRoles as $rol)
                                            @php $tiene = in_array($permisoCompleto, $permisos[$rol] ?? []); @endphp
                                            <td class="text-center">
                                                @if($tiene)
                                                    <span class="badge badge-success" style="font-size:1rem">&check;</span>
                                                @else
                                                    <span class="text-muted" style="font-size:1rem">&mdash;</span>
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@include('roles.create')
@include('roles.edit')
@endsection

@section('scripts')
@include('roles.show')
<script>
$(function() {
    // Persistir pestaña activa en localStorage
    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
        localStorage.setItem('activeRolesTab', $(e.target).attr('href'));
    });
    var activeTab = localStorage.getItem('activeRolesTab');
    if (activeTab) {
        $('#rolesTabs a[href="' + activeTab + '"]').tab('show');
    }
});
</script>
@endsection