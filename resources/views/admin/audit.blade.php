@extends('layouts.admin')

@section('title', 'Logs de Auditoría')

@section('content')
<style>
    /* Card Styles */
    .card {
        background: var(--alpha-card-bg);
        border: 1px solid rgba(11, 25, 44, 0.05);
        border-radius: var(--alpha-radius-lg);
        box-shadow: var(--alpha-shadow-sm);
        transition: var(--alpha-transition);
        margin-bottom: 24px;
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

    .card-header .card-tools {
        margin-top: 12px;
    }

    .card-body {
        padding: 24px;
    }

    /* Badge Styles */
    .badge {
        padding: 8px 14px;
        border-radius: 6px;
        font-weight: 700;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        margin-right: 8px;
        margin-bottom: 8px;
    }

    .badge-info {
        background: rgba(30, 62, 98, 0.15);
        color: var(--alpha-blue-light);
        border: 1px solid var(--alpha-blue-light);
    }

    .badge-danger {
        background: rgba(239, 68, 68, 0.15);
        color: #ef4444;
        border: 1px solid #ef4444;
    }

    .badge-warning {
        background: rgba(245, 158, 11, 0.15);
        color: var(--alpha-warning);
        border: 1px solid var(--alpha-warning);
    }

    .badge-dark {
        background: var(--alpha-navy-dark);
        color: white;
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
        color: var(--alpha-text-main);
    }

    /* Buttons */
    .btn-primary {
        background: linear-gradient(135deg, var(--alpha-navy-dark) 0%, var(--alpha-blue-light) 100%);
        border: none;
        color: white;
        font-weight: 700;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 8px 16px;
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
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 8px 16px;
        border-radius: 8px;
        transition: var(--alpha-transition);
    }

    .btn-secondary:hover {
        background: #a88d50;
        transform: translateY(-2px);
        color: white;
    }

    .btn-xs {
        padding: 6px 10px;
        font-size: 0.7rem;
        border-radius: 6px;
    }

    .btn-info {
        background: var(--alpha-blue-light);
        border: none;
        color: white;
    }

    .btn-info:hover {
        background: #1e3e62;
        color: white;
    }

    /* Table Styles */
    .table {
        margin-bottom: 0;
        font-size: 0.9rem;
    }

    .table-bordered {
        border: 1px solid rgba(11, 25, 44, 0.1);
    }

    .table-bordered td, .table-bordered th {
        border: 1px solid rgba(11, 25, 44, 0.1);
        padding: 12px;
    }

    .table thead {
        background: linear-gradient(135deg, var(--alpha-navy-dark) 0%, var(--alpha-blue-light) 100%);
    }

    .table thead th {
        border-color: var(--alpha-gold);
        color: white;
        font-weight: 700;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 12px 16px;
    }

    .table tbody tr {
        background-color: var(--alpha-card-bg);
        border-bottom: 1px solid rgba(11, 25, 44, 0.05);
    }

    .table tbody tr:hover {
        background-color: var(--alpha-gold-glow);
    }

    .table tbody td {
        color: var(--alpha-text-main);
        padding: 12px 16px;
        vertical-align: middle;
    }

    .table-danger {
        background: rgba(239, 68, 68, 0.08) !important;
    }

    .table-warning {
        background: rgba(245, 158, 11, 0.08) !important;
    }

    .table-info {
        background: rgba(30, 62, 98, 0.08) !important;
    }

    /* Pagination */
    .pagination {
        margin-top: 20px;
        gap: 6px;
        justify-content: center;
    }

    .page-item .page-link {
        color: var(--alpha-navy-dark);
        border: 1px solid rgba(11, 25, 44, 0.1);
        border-radius: 6px;
        padding: 8px 12px;
        font-weight: 600;
        transition: var(--alpha-transition);
    }

    .page-item .page-link:hover {
        background: var(--alpha-navy-dark);
        color: white;
        border-color: var(--alpha-navy-dark);
    }

    .page-item.active .page-link {
        background: linear-gradient(135deg, var(--alpha-navy-dark) 0%, var(--alpha-blue-light) 100%);
        border-color: var(--alpha-gold);
    }

    /* Modal Styles */
    .modal-content {
        border-radius: var(--alpha-radius-lg);
        border: 1px solid rgba(11, 25, 44, 0.05);
        box-shadow: var(--alpha-shadow-lg);
    }

    .modal-header {
        background: linear-gradient(135deg, var(--alpha-navy-dark) 0%, var(--alpha-blue-light) 100%);
        color: white;
        border-bottom: 2px solid var(--alpha-gold);
    }

    .modal-header .close {
        color: white;
    }

    .modal-body pre {
        background: var(--alpha-bg-main);
        border: 1px solid rgba(11, 25, 44, 0.1);
        border-radius: 8px;
        padding: 16px;
        color: var(--alpha-text-main);
        font-size: 0.85rem;
    }

    /* Text Utilities */
    .text-muted {
        color: var(--alpha-text-muted) !important;
        font-size: 0.85rem;
    }

    small.text-muted {
        color: var(--alpha-text-muted) !important;
    }

    /* Content Header */
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
        color: var(--alpha-warning);
    }
</style>

<div class="row mb-4 animate-up">
    <div class="col-12">
        <div style="background: linear-gradient(135deg, var(--alpha-navy-dark) 0%, var(--alpha-blue-light) 100%); border-radius: var(--alpha-radius-lg); padding: 24px; color: white; border-left: 5px solid var(--alpha-gold);">
            <h2 style="margin: 0; font-weight: 800; letter-spacing: -0.5px;">
                <i class="fas fa-shield-alt" style="color: var(--alpha-gold); margin-right: 12px;"></i>
                Logs de Auditoría del Sistema
            </h2>
            <p style="margin-top: 8px; margin-bottom: 0; color: rgba(255,255,255,0.9); font-size: 0.9rem;">Registro completo de todas las actividades y eventos del sistema</p>
        </div>
    </div>
</div>

<div class="row animate-up delay-1">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h3 class="card-title">
                            <i class="fas fa-list" style="margin-right: 10px;"></i>
                            Registro de Actividades del Sistema
                        </h3>
                        <p style="color: rgba(255,255,255,0.8); margin-top: 8px; margin-bottom: 0; font-size: 0.85rem;">Seguimiento de eventos críticos, intentos de acceso y cambios en el sistema</p>
                    </div>
                    <div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                            <div style="background: rgba(255,255,255,0.1); padding: 12px 16px; border-radius: 8px;">
                                <small style="color: rgba(255,255,255,0.7); display: block;">Total Eventos</small>
                                <strong style="font-size: 1.3rem; color: white;">{{ $stats['total_events'] }}</strong>
                            </div>
                            <div style="background: rgba(239, 68, 68, 0.15); padding: 12px 16px; border-radius: 8px;">
                                <small style="color: #ef4444; display: block;">Fallos Login</small>
                                <strong style="font-size: 1.3rem; color: #ef4444;">{{ $stats['login_failures'] }}</strong>
                            </div>
                            <div style="background: rgba(245, 158, 11, 0.15); padding: 12px 16px; border-radius: 8px;">
                                <small style="color: var(--alpha-warning); display: block;">Seguridad</small>
                                <strong style="font-size: 1.3rem; color: var(--alpha-warning);">{{ $stats['security_events'] }}</strong>
                            </div>
                            <div style="background: rgba(11, 25, 44, 0.3); padding: 12px 16px; border-radius: 8px;">
                                <small style="color: rgba(255,255,255,0.7); display: block;">Críticos</small>
                                <strong style="font-size: 1.3rem; color: white;">{{ $stats['critical_events'] }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <h5 style="color: var(--alpha-navy-dark); font-weight: 700; margin-bottom: 16px;">
                    <i class="fas fa-filter" style="color: var(--alpha-gold); margin-right: 10px;"></i>
                    Filtros de Búsqueda
                </h5>
                <!-- Filtros -->
                <form method="GET" class="mb-3">
                    <div class="row g-2">
                        <div class="col-md-2">
                            <input type="text" name="action" class="form-control" placeholder="Acción..." value="{{ request('action') }}">
                        </div>
                        <div class="col-md-2">
                            <input type="text" name="user" class="form-control" placeholder="Usuario..." value="{{ request('user') }}">
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                        </div>
                        <div class="col-md-2">
                            <select name="severity" class="form-control">
                                <option value="">Todas las severidades</option>
                                <option value="critical" {{ request('severity') === 'critical' ? 'selected' : '' }}>Crítico</option>
                                <option value="high" {{ request('severity') === 'high' ? 'selected' : '' }}>Alto</option>
                                <option value="medium" {{ request('severity') === 'medium' ? 'selected' : '' }}>Medio</option>
                                <option value="low" {{ request('severity') === 'low' ? 'selected' : '' }}>Bajo</option>
                            </select>
                        </div>
                        <div class="col-md-4 d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Filtrar
                            </button>
                            <a href="{{ route('admin.audit') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Limpiar
                            </a>
                        </div>
                    </div>
                </form>

                <h5 style="color: var(--alpha-navy-dark); font-weight: 700; margin-top: 24px; margin-bottom: 16px;">
                    <i class="fas fa-table" style="color: var(--alpha-gold); margin-right: 10px;"></i>
                    Eventos Registrados
                </h5>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-sm">
                        <thead>
                            <tr>
                                <th style="width: 140px;">Fecha/Hora</th>
                                <th style="width: 120px;">Acción</th>
                                <th>Usuario</th>
                                <th>Descripción</th>
                                <th style="width: 80px;">Detalles</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                            @php
                                $rowClass = '';
                                switch($log['severity']) {
                                    case 'critical': $rowClass = 'table-danger'; break;
                                    case 'high': $rowClass = 'table-warning'; break;
                                    case 'medium': $rowClass = 'table-info'; break;
                                }
                            @endphp
                            <tr class="{{ $rowClass }}">
                                <td>
                                    <small><strong>{{ date('d/m/Y', strtotime($log['timestamp'])) }}</strong></small><br>
                                    <small class="text-muted">{{ date('H:i:s', strtotime($log['timestamp'])) }}</small>
                                </td>
                                <td>
                                    @php
                                        $badgeClass = match($log['severity']) {
                                            'critical' => 'badge-danger',
                                            'high' => 'badge-warning', 
                                            'medium' => 'badge-info',
                                            default => 'badge-secondary'
                                        };
                                    @endphp
                                    @php
                                        $actionSpanish = match($log['action']) {
                                            'LOGIN_SUCCESS' => 'Inicio Sesión',
                                            'LOGIN_FAILED' => 'Fallo Login',
                                            'LOGOUT' => 'Cerrar Sesión',
                                            'CREATED' => 'Creado',
                                            'UPDATED' => 'Actualizado', 
                                            'DELETED' => 'Eliminado',
                                            'SECURITY_INVALID_PASSWORD' => 'Contraseña Inválida',
                                            'SECURITY_USER_NOT_FOUND' => 'Usuario No Encontrado',
                                            'SECURITY_RATE_LIMIT_EXCEEDED' => 'Límite Excedido',
                                            'ACCESS_AUDIT_VIEW' => 'Ver Auditoría',
                                            'CRITICAL_POSTULACION_APPROVED' => 'Postulación Aprobada',
                                            'CRITICAL_PERMISSION_DENIED' => 'Permiso Denegado',
                                            default => $log['action']
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">
                                        {{ $actionSpanish }}
                                    </span>
                                </td>
                                <td>
                                    <div><strong>{{ $log['user_name'] }}</strong></div>
                                    <small class="text-muted">{{ $log['user_role'] }}</small>
                                </td>

                                <td>
                                    <small>{{ $log['description'] }}</small>
                                    @if($log['model'])
                                        <br><small class="text-muted">{{ class_basename($log['model']) }} #{{ $log['model_id'] }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if(!empty($log['data']))
                                        <button class="btn btn-xs btn-info" onclick="showDetails({{ json_encode($log) }})">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <i class="fas fa-inbox fa-2x text-muted mb-3" style="display: block;"></i>
                                    <strong class="text-muted">No hay logs disponibles</strong>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginación simple -->
                @if($total > 20)
                <div class="d-flex justify-content-center mt-4">
                    <nav>
                        <ul class="pagination">
                            @if($page > 1)
                                <li class="page-item">
                                    <a class="page-link" href="?page={{ $page - 1 }}&{{ http_build_query(request()->except('page')) }}">
                                        <i class="fas fa-chevron-left"></i> Anterior
                                    </a>
                                </li>
                            @endif
                            
                            <li class="page-item active">
                                <span class="page-link">{{ $page }} / {{ ceil($total / $perPage) }}</span>
                            </li>
                            
                            @if($page * $perPage < $total)
                                <li class="page-item">
                                    <a class="page-link" href="?page={{ $page + 1 }}&{{ http_build_query(request()->except('page')) }}">
                                        Siguiente <i class="fas fa-chevron-right"></i>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </nav>
                </div>
                @endif

                <div class="mt-4 p-3" style="background: var(--alpha-gold-glow); border-radius: 8px;">
                    <small class="text-muted">
                        <strong>Mostrando:</strong> {{ count($logs) }} de {{ $total }} registros
                    </small>
                    <br>
                    <small style="color: var(--alpha-blue-light);">
                        <i class="fas fa-database"></i> 
                        Archivo: {{ $debug['file_exists'] ? 'Activo' : 'No encontrado' }} | 
                        Tamaño: {{ number_format($debug['file_size'], 0, ',', '.') }} bytes
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detalles -->
<div class="modal fade" id="detailsModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Detalles del Evento</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <pre id="detailsContent"></pre>
            </div>
        </div>
    </div>
</div>

<script>
function showDetails(logEntry) {
    const actionTranslations = {
        'LOGIN_SUCCESS': 'Inicio Sesión',
        'LOGIN_FAILED': 'Fallo Login',
        'LOGOUT': 'Cerrar Sesión',
        'CREATED': 'Creado',
        'UPDATED': 'Actualizado',
        'DELETED': 'Eliminado',
        'SECURITY_INVALID_PASSWORD': 'Contraseña Inválida',
        'SECURITY_USER_NOT_FOUND': 'Usuario No Encontrado',
        'SECURITY_RATE_LIMIT_EXCEEDED': 'Límite Excedido',
        'ACCESS_AUDIT_VIEW': 'Ver Auditoría',
        'CRITICAL_POSTULACION_APPROVED': 'Postulación Aprobada',
        'CRITICAL_PERMISSION_DENIED': 'Permiso Denegado'
    };
    
    const severityTranslations = {
        'critical': 'Crítico',
        'high': 'Alto',
        'medium': 'Medio',
        'low': 'Bajo'
    };
    
    let content = `Timestamp: ${logEntry.timestamp}\n`;
    content += `Acción: ${actionTranslations[logEntry.action] || logEntry.action}\n`;
    content += `Usuario: ${logEntry.user_name} (${logEntry.user_role})\n`;
    content += `Severidad: ${severityTranslations[logEntry.severity] || logEntry.severity}\n`;
    
    if (logEntry.user_agent) {
        content += `User Agent: ${logEntry.user_agent}\n`;
    }
    
    if (logEntry.model) {
        content += `Modelo: ${logEntry.model} #${logEntry.model_id}\n`;
    }
    
    content += `\nDatos adicionales:\n`;
    content += JSON.stringify(logEntry.data, null, 2);
    
    document.getElementById('detailsContent').textContent = content;
    $('#detailsModal').modal('show');
}
</script>


@endsection
