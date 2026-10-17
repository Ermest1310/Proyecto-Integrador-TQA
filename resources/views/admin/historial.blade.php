@extends('layouts.admin')

@section('title', 'Historial de Sesiones')

@section('content')
<style>
    /* Card Styles */
    .card {
        background: var(--alpha-card-bg);
        border: 1px solid rgba(11, 25, 44, 0.05);
        border-radius: var(--alpha-radius-lg);
        box-shadow: var(--alpha-shadow-sm);
        transition: var(--alpha-transition);
    }

    .card:hover {
        box-shadow: var(--alpha-shadow-md);
    }

    .card-header {
        background: linear-gradient(135deg, var(--alpha-navy-dark) 0%, var(--alpha-blue-light) 100%);
        border-bottom: 2px solid var(--alpha-gold);
        color: white;
        padding: 20px;
        border-radius: var(--alpha-radius-lg) var(--alpha-radius-lg) 0 0;
    }

    .card-header h3 {
        margin: 0;
        font-weight: 700;
        font-size: 1.1rem;
        letter-spacing: 0.3px;
    }

    .card-header i {
        color: var(--alpha-gold);
        margin-right: 10px;
    }

    .card-body {
        padding: 24px;
    }

    /* Form Controls */
    .form-control {
        border: 1px solid rgba(11, 25, 44, 0.1);
        border-radius: 8px;
        padding: 10px 14px;
        transition: var(--alpha-transition);
        font-size: 0.9rem;
    }

    .form-control:focus {
        border-color: var(--alpha-gold);
        box-shadow: 0 0 0 3px var(--alpha-gold-glow);
    }

    .form-control option {
        background-color: var(--alpha-card-bg);
        color: var(--alpha-text-main);
    }

    label {
        font-weight: 600;
        color: var(--alpha-text-main);
        font-size: 0.85rem;
        margin-bottom: 6px;
    }

    /* Buttons */
    .btn-primary {
        background: linear-gradient(135deg, var(--alpha-navy-dark) 0%, var(--alpha-blue-light) 100%);
        border: none;
        color: white;
        font-weight: 700;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 10px 20px;
        border-radius: 8px;
        transition: var(--alpha-transition);
    }

    .btn-primary:hover {
        box-shadow: 0 8px 16px rgba(11, 25, 44, 0.25);
        transform: translateY(-2px);
        color: white;
    }

    .btn-secondary {
        background: var(--alpha-gold);
        border: none;
        color: white;
        font-weight: 700;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 10px 20px;
        border-radius: 8px;
        transition: var(--alpha-transition);
    }

    .btn-secondary:hover {
        background: #a88d50;
        transform: translateY(-2px);
        color: white;
    }

    /* Table Styles */
    .table {
        margin-bottom: 0;
    }

    .table-striped tbody tr {
        background-color: var(--alpha-card-bg);
        border-bottom: 1px solid rgba(11, 25, 44, 0.05);
    }

    .table-striped tbody tr:nth-of-type(odd) {
        background-color: rgba(11, 25, 44, 0.02);
    }

    .table-striped tbody tr:hover {
        background-color: var(--alpha-gold-glow);
    }

    .table thead {
        background: linear-gradient(135deg, var(--alpha-navy-dark) 0%, var(--alpha-blue-light) 100%);
    }

    .table-dark th {
        background: linear-gradient(135deg, var(--alpha-navy-dark) 0%, var(--alpha-blue-light) 100%);
        border-color: var(--alpha-gold);
        color: white;
        font-weight: 700;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 12px 16px;
    }

    .table tbody td {
        color: var(--alpha-text-main);
        font-size: 0.9rem;
        padding: 12px 16px;
        vertical-align: middle;
    }

    /* Badges */
    .badge-success {
        background: rgba(16, 185, 129, 0.15);
        color: #10b981;
        font-weight: 700;
        border: 1px solid #10b981;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .badge-danger {
        background: rgba(239, 68, 68, 0.15);
        color: #ef4444;
        font-weight: 700;
        border: 1px solid #ef4444;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .badge-info {
        background: rgba(30, 62, 98, 0.15);
        color: var(--alpha-blue-light);
        font-weight: 700;
        border: 1px solid var(--alpha-blue-light);
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .badge-secondary {
        background: var(--alpha-text-muted);
        color: white;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 0.75rem;
    }

    /* Pagination */
    .pagination {
        margin-top: 20px;
        gap: 6px;
    }

    .page-link {
        color: var(--alpha-navy-dark);
        border: 1px solid rgba(11, 25, 44, 0.1);
        border-radius: 6px;
        padding: 8px 12px;
        font-weight: 600;
        transition: var(--alpha-transition);
    }

    .page-link:hover {
        background: var(--alpha-navy-dark);
        color: white;
        border-color: var(--alpha-navy-dark);
    }

    .page-item.active .page-link {
        background: linear-gradient(135deg, var(--alpha-navy-dark) 0%, var(--alpha-blue-light) 100%);
        border-color: var(--alpha-gold);
    }

    /* Text Utilities */
    .text-muted {
        color: var(--alpha-text-muted) !important;
        font-size: 0.85rem;
    }

    /* Section Title */
    .content-header {
        margin-bottom: 24px;
    }

    .content-header h1 {
        color: var(--alpha-navy-dark);
        font-weight: 800;
        font-size: 1.8rem;
        letter-spacing: -0.5px;
    }

    .content-header i {
        color: var(--alpha-gold);
    }
</style>

<div class="row mb-4 animate-up">
    <div class="col-12">
        <div style="background: linear-gradient(135deg, var(--alpha-navy-dark) 0%, var(--alpha-blue-light) 100%); border-radius: var(--alpha-radius-lg); padding: 24px; color: white; border-left: 5px solid var(--alpha-gold);">
            <h2 style="margin: 0; font-weight: 800; letter-spacing: -0.5px;">
                <i class="fas fa-history" style="color: var(--alpha-gold); margin-right: 12px;"></i>
                Historial de Sesiones Completadas
            </h2>
            <p style="margin-top: 8px; margin-bottom: 0; color: rgba(255,255,255,0.9); font-size: 0.9rem;">Registro completo de todas las sesiones de entrenamiento realizadas</p>
        </div>
    </div>
</div>

<div class="row animate-up delay-1">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-filter"></i>
                    Filtros de Búsqueda
                </h3>
            </div>
            <div class="card-body">
                <!-- Filtros -->
                <form method="GET" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-2">
                            <label>Estado</label>
                            <select name="estado" class="form-control">
                                <option value="">Todos los estados</option>
                                <option value="Completada" {{ request('estado') == 'Completada' ? 'selected' : '' }}>Completadas</option>
                                <option value="Cancelada" {{ request('estado') == 'Cancelada' ? 'selected' : '' }}>Canceladas</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Gimnasta</label>
                            <select name="gimnasta" class="form-control">
                                <option value="">Todos los gimnastas</option>
                                @foreach($gimnastas as $gimnasta)
                                    <option value="{{ $gimnasta->ID_Usuario }}" {{ request('gimnasta') == $gimnasta->ID_Usuario ? 'selected' : '' }}>
                                        {{ $gimnasta->Nombre }} {{ $gimnasta->Apellido }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>Entrenador</label>
                            <select name="entrenador" class="form-control">
                                <option value="">Todos</option>
                                @foreach($entrenadores as $entrenador)
                                    <option value="{{ $entrenador->ID_Usuario }}" {{ request('entrenador') == $entrenador->ID_Usuario ? 'selected' : '' }}>
                                        {{ $entrenador->Nombre }} {{ $entrenador->Apellido }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>Aparato</label>
                            <select name="aparato" class="form-control">
                                <option value="">Todos</option>
                                @foreach($aparatos as $aparato)
                                    <option value="{{ $aparato->ID_Aparato }}" {{ request('aparato') == $aparato->ID_Aparato ? 'selected' : '' }}>
                                        {{ $aparato->Nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-1">
                            <label>Desde</label>
                            <input type="date" name="fecha_inicio" class="form-control" value="{{ request('fecha_inicio') }}">
                        </div>
                        <div class="col-md-2">
                            <label>Hasta</label>
                            <input type="date" name="fecha_fin" class="form-control" value="{{ request('fecha_fin') }}">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12 d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Filtrar
                            </button>
                            <a href="{{ route('admin.historial') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Limpiar
                            </a>
                        </div>
                    </div>
                </form>

                
                <h5 style="color: var(--alpha-navy-dark); font-weight: 700; margin-top: 24px; margin-bottom: 16px;">
                    <i class="fas fa-table" style="color: var(--alpha-gold); margin-right: 10px;"></i>
                    Registro de Sesiones
                </h5>

                <!-- Tabla de sesiones -->
                @if($sesiones->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Estado</th>
                                    <th>Fecha</th>
                                    <th>Hora</th>
                                    <th>Gimnasta</th>
                                    <th>Entrenador</th>
                                    <th>Aparato</th>
                                    <th>Duración</th>
                                    <th>Observaciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sesiones as $sesion)
                                    <tr>
                                        <td>
                                            @if($sesion->Estado == 'Completada')
                                                <span class="badge badge-success">
                                                    <i class="fas fa-check"></i> Completada
                                                </span>
                                            @else
                                                <span class="badge badge-danger">
                                                    <i class="fas fa-times"></i> Cancelada
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($sesion->Fecha)->format('d/m/Y') }}
                                        </td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($sesion->Hora_Inicio)->format('H:i') }} - 
                                            {{ \Carbon\Carbon::parse($sesion->Hora_Fin)->format('H:i') }}
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-user" style="color: var(--alpha-blue-light); margin-right: 8px;"></i>
                                                <span>{{ $sesion->gimnasta->Nombre }} {{ $sesion->gimnasta->Apellido }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            @if($sesion->entrenador)
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-user-tie" style="color: var(--alpha-gold); margin-right: 8px;"></i>
                                                    <span>{{ $sesion->entrenador->Nombre }} {{ $sesion->entrenador->Apellido }}</span>
                                                </div>
                                            @else
                                                <span class="text-muted">Sin asignar</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($sesion->aparato)
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-dumbbell" style="color: var(--alpha-success); margin-right: 8px;"></i>
                                                    <span>{{ $sesion->aparato->Nombre }}</span>
                                                </div>
                                            @else
                                                <span class="text-muted">General</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($sesion->Estado == 'Completada')
                                                @php
                                                    $inicio = \Carbon\Carbon::parse($sesion->Hora_Inicio);
                                                    $fin = \Carbon\Carbon::parse($sesion->Hora_Fin);
                                                    $duracion = $inicio->diffInMinutes($fin);
                                                @endphp
                                                <span class="badge badge-info">{{ $duracion }} min</span>
                                            @else
                                                <span class="badge badge-secondary">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($sesion->Observaciones)
                                                <small class="text-muted">{{ Str::limit($sesion->Observaciones, 50) }}</small>
                                            @else
                                                <small class="text-muted">Sin observaciones</small>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div>
                            <small class="text-muted">
                                Mostrando {{ $sesiones->firstItem() }} a {{ $sesiones->lastItem() }} 
                                de {{ $sesiones->total() }} sesiones
                            </small>
                        </div>
                        <div>
                            {{ $sesiones->appends(request()->query())->links() }}
                        </div>
                    </div>
                @else
                    <div class="text-center py-5" style="background: var(--alpha-gold-glow); border-radius: var(--alpha-radius-md); padding: 40px;">
                        <i class="fas fa-history fa-3x" style="color: var(--alpha-text-muted); margin-bottom: 16px; display: block;"></i>
                        <h5 style="color: var(--alpha-text-main); margin-bottom: 8px;">No hay sesiones en el historial</h5>
                        <p style="color: var(--alpha-text-muted); margin: 0;">No se encontraron sesiones completadas o canceladas que coincidan con los filtros aplicados.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection