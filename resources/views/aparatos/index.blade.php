@extends('layouts.admin')

@section('title', 'Listado de Aparatos')

@section('content')
<div class="container-fluid py-4 alpha-dashboard-wrapper">
    
    <style>
        :root {
            --alpha-navy-dark: #0b192c;
            --alpha-navy-medium: #122239;
            --alpha-blue-light: #1e3e62;
            --alpha-gold: #bfa15f;
            --alpha-gold-glow: rgba(191, 161, 95, 0.15);
            --alpha-bg-main: #f8fafc;
            --alpha-card-bg: #ffffff;
            --alpha-text-main: #1e293b;
            --alpha-text-muted: #64748b;
            --alpha-danger: #ef4444;
            --alpha-success: #10b981;
            --alpha-warning: #f59e0b;
            
            --alpha-radius-lg: 16px;
            --alpha-radius-md: 12px;
            --alpha-shadow-sm: 0 4px 6px -1px rgb(0 0 0 / 0.05);
            --alpha-shadow-md: 0 10px 15px -3px rgb(11 25 44 / 0.06);
            --alpha-transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Soporte Nativo Modo Oscuro */
        [data-theme="dark"] {
            --alpha-bg-main: #07101c;
            --alpha-card-bg: #122239;
            --alpha-text-main: #f1f5f9;
            --alpha-text-muted: #cbd5e1;
        }

        @keyframes alphaFadeInUp {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-up { animation: alphaFadeInUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) both; }
        .delay-1 { animation-delay: 0.1s; }
        
        /* Contenedor de Cristal Premium */
        .alpha-glass-list-card { 
            background: var(--alpha-card-bg); 
            border-radius: var(--alpha-radius-lg); 
            box-shadow: var(--alpha-shadow-md); 
            border: 1px solid rgba(11, 25, 44, 0.04); 
        }

        /* Elementos de Formulario */
        .alpha-form-control {
            background-color: var(--alpha-bg-main);
            border: 1px solid rgba(30, 62, 98, 0.15);
            color: var(--alpha-text-main);
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 0.9rem;
            transition: var(--alpha-transition);
        }
        .alpha-form-control:focus {
            background-color: var(--alpha-card-bg);
            border-color: var(--alpha-gold);
            box-shadow: 0 0 0 3px var(--alpha-gold-glow);
            color: var(--alpha-text-main);
            outline: none;
        }

        /* Botones del Ecosistema */
        .btn-alpha-action-primary { background: var(--alpha-gold); color: white; font-weight: 700; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; padding: 10px 20px; border-radius: 8px; transition: var(--alpha-transition); border: none; }
        .btn-alpha-action-primary:hover { background-color: #a88d50; transform: translateY(-2px); color: white; box-shadow: 0 4px 12px rgba(191, 161, 95, 0.3); }
        
        .btn-alpha-secondary { background: var(--alpha-blue-light); color: white; font-weight: 600; font-size: 0.85rem; padding: 10px 16px; border-radius: 8px; transition: var(--alpha-transition); border: none; }
        .btn-alpha-secondary:hover { background: var(--alpha-navy-dark); color: white; }

        .btn-alpha-icon { width: 34px; height: 34px; padding: 0; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; transition: var(--alpha-transition); border: none; }
        .btn-alpha-icon:hover { transform: translateY(-2px); }

        /* Estilización Avanzada de Tablas */
        .alpha-table-container { border-radius: var(--alpha-radius-md); overflow: hidden; border: 1px solid rgba(11, 25, 44, 0.05); }
        .alpha-table { width: 100%; margin-bottom: 0; color: var(--alpha-text-main); vertical-align: middle; }
        .alpha-table thead th { background-color: var(--alpha-navy-dark); color: #ffffff; font-family: var(--bs-font-monospace), monospace; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; border: none; padding: 14px; }
        [data-theme="dark"] .alpha-table thead th { background-color: var(--alpha-navy-medium); border-bottom: 1px solid rgba(255,255,255,0.05); }
        .alpha-table tbody tr { border-bottom: 1px solid rgba(11, 25, 44, 0.05); transition: var(--alpha-transition); background: transparent; }
        .alpha-table tbody tr:hover { background-color: rgba(191, 161, 95, 0.02) !important; }
        .alpha-table td { padding: 16px 14px; font-size: 0.9rem; color: var(--alpha-text-main); border: none; }

        /* Insignias de Estado Modulares */
        .alpha-badge { padding: 5px 12px; border-radius: 30px; font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; display: inline-block; }
        .badge-alpha-success { background: rgba(16, 185, 129, 0.12); color: var(--alpha-success); }
        .badge-alpha-danger { background: rgba(239, 68, 68, 0.12); color: var(--alpha-danger); }
        .badge-alpha-warning { background: rgba(245, 158, 11, 0.12); color: var(--alpha-warning); }

        body:not([data-theme="dark"]) .text-navy-title { color: var(--alpha-navy-dark) !important; }
        [data-theme="dark"] .text-navy-title { color: #ffffff !important; }
    </style>

    <div class="row mb-4 align-items-center animate-up">
        <div class="col-md-6">
            <h3 class="fw-bold text-navy-title mb-1">
                <i class="fas fa-cubes text-gold me-2"></i>Control de Aparatos FIG
            </h3>
            <p class="text-muted small mb-0">Inventario analítico, estado de ciclos operativos estructurales y métricas de mantenimiento.</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <button type="button" class="btn btn-alpha-action-primary" data-bs-toggle="modal" data-bs-target="#createModal">
                <i class="fas fa-plus me-1"></i> Registrar Aparato
            </button>
        </div>
    </div>

    <div class="alpha-glass-list-card p-4 animate-up delay-1">
        
        <div id="alertContainer"></div>
        
        <div class="row g-3 mb-4">
            <div class="col-xl-4 col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0 text-muted" style="border-radius: 8px 0 0 8px; border-color: rgba(30, 62, 98, 0.15);"><i class="fas fa-search"></i></span>
                    <input type="text" id="buscar" class="form-control alpha-form-control border-start-0" style="border-radius: 0 8px 8px 0;" placeholder="Buscar aparato por especificación...">
                </div>
            </div>
            <div class="col-xl-3 col-md-3">
                <select id="filtroEstado" class="form-select alpha-form-control">
                    <option value="">Todos los estados disponibles</option>
                    <option value="activo">Activos en Planta</option>
                    <option value="inactivo">Inactivos / Deshabilitados</option>
                </select>
            </div>
            <div class="col-xl-3 col-md-4">
                <select id="filtroMantenimiento" class="form-select alpha-form-control">
                    <option value="">Todos los niveles de mantenimiento</option>
                    <option value="Operativo">Certificado Operativo</option>
                    <option value="Mantenimiento">En Línea de Mantenimiento</option>
                </select>
            </div>
            <div class="col-xl-2 col-md-12 text-md-end">
                <button type="button" class="btn btn-alpha-secondary w-100" onclick="limpiarFiltros()">
                    <i class="fas fa-filter-circle-xmark me-1"></i> Limpiar
                </button>
            </div>
        </div>

        <div class="table-responsive alpha-table-container">
            <table class="table alpha-table" id="aparatosTable">
                <thead>
                    <tr>
                        <th width="90">COD</th>
                        <th>Nombre del Aparato</th>
                        <th>Estado Técnico</th>
                        <th>Próxima Revisión</th>
                        <th>Estatus Operativo</th>
                        <th width="120" class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($aparatos as $aparato)
                        <tr class="aparato-row" id="aparato-{{ $aparato->ID_Aparato }}">
                            <td class="font-monospace fw-bold text-muted">{{ str_pad($aparato->ID_Aparato, 4, '0', STR_PAD_LEFT) }}</td>
                            <td>
                                <div class="fw-bold text-navy-title">{{ $aparato->Nombre }}</div>
                            </td>
                            <td>
                                @if($aparato->EstadoMantenimiento === 'Operativo' || !$aparato->EstadoMantenimiento)
                                    <span class="alpha-badge badge-alpha-success">
                                        <i class="fas fa-check-circle me-1"></i> Operativo
                                    </span>
                                @elseif($aparato->EstadoMantenimiento === 'Mantenimiento')
                                    <span class="alpha-badge badge-alpha-warning">
                                        <i class="fas fa-tools me-1"></i> En Revisión
                                    </span>
                                @else
                                    <span class="alpha-badge badge-alpha-danger">
                                        <i class="fas fa-triangle-exclamation me-1"></i> Crítico
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($aparato->ProximoMantenimiento)
                                    <div class="d-flex align-items-center">
                                        <span class="font-monospace {{ $aparato->necesitaMantenimiento() ? 'text-danger fw-bold' : 'text-muted' }}">
                                            {{ $aparato->ProximoMantenimiento->format('d/m/Y') }}
                                        </span>
                                        @if($aparato->necesitaMantenimiento())
                                            <i class="fas fa-circle-exclamation text-warning ms-2 animate-pulse" title="Ciclo de mantenimiento mandatorio pendiente"></i>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-muted small italic">No agendado</span>
                                @endif
                            </td>
                            <td>
                                <span class="alpha-badge {{ $aparato->activo ? 'badge-alpha-success' : 'badge-alpha-danger' }}">
                                    {{ $aparato->activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-1 justify-content-center">
                                    <button class="btn btn-info btn-alpha-icon text-white bg-info" onclick="editAparato({{ $aparato->ID_Aparato }})" title="Editar Configuración">
                                        <i class="fas fa-sliders-h" style="font-size: 0.85rem;"></i>
                                    </button>
                                    @if($aparato->en_mantenimiento)
                                        <button class="btn btn-success btn-alpha-icon text-white bg-success" onclick="finalizarMantenimiento({{ $aparato->ID_Aparato }})" title="Finalizar Mantenimiento">
                                            <i class="fas fa-check" style="font-size: 0.85rem;"></i>
                                        </button>
                                    @else
                                        @if($aparato->activo)
                                            <button class="btn btn-secondary btn-alpha-icon" onclick="desactivarAparato({{ $aparato->ID_Aparato }})" title="Cesar Actividad Operativa">
                                                <i class="fas fa-power-off" style="font-size: 0.85rem;"></i>
                                            </button>
                                        @else
                                            <button class="btn btn-success btn-alpha-icon text-white bg-success" onclick="reactivarAparato({{ $aparato->ID_Aparato }})" title="Habilitar Actividad Operativa">
                                                <i class="fas fa-play" style="font-size: 0.85rem;"></i>
                                            </button>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@include('aparatos.create')
@include('aparatos.edit')

@endsection

@section('scripts')
@include('aparatos.show')

<script>
// Filtros Integrados Dinámicos
function aplicarFiltros() {
    const buscar = document.getElementById('buscar').value.toLowerCase();
    const estado = document.getElementById('filtroEstado').value;
    const mantenimiento = document.getElementById('filtroMantenimiento').value;
    
    const filas = document.querySelectorAll('.aparato-row');
    
    filas.forEach(function(fila) {
        const celdas = fila.querySelectorAll('td');
        const textoCompleto = fila.textContent.toLowerCase();
        const estadoTexto = celdas[4].textContent.toLowerCase();
        const mantenimientoTexto = celdas[2].textContent;
        
        let mostrar = true;
        
        if (buscar && !textoCompleto.includes(buscar)) mostrar = false;
        if (estado && !estadoTexto.includes(estado)) mostrar = false;
        if (mantenimiento && !mantenimientoTexto.includes(mantenimiento)) mostrar = false;
        
        fila.style.display = mostrar ? '' : 'none';
    });
}

function limpiarFiltros() {
    document.getElementById('buscar').value = '';
    document.getElementById('filtroEstado').value = '';
    document.getElementById('filtroMantenimiento').value = '';
    aplicarFiltros();
}

// Inyección Dinámica de Diálogos Modulares Modernizados con Paletas Corporativas
function desactivarAparato(id) {
    const modal = `
        <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content" style="border-radius: 12px; overflow: hidden; border: none; background: var(--alpha-card-bg);">
                    <div class="modal-header py-3" style="background: var(--alpha-warning); border: none;">
                        <h5 class="modal-title text-white fw-bold"><i class="fas fa-power-off me-2"></i> Desactivar Elemento Técnico</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body p-4 text-center">
                        <div class="mb-3"><i class="fas fa-triangle-exclamation text-warning fa-3x"></i></div>
                        <h6 class="fw-bold text-navy-title mb-2" style="font-size:1.1rem;">¿Confirmar cese de actividad operativa?</h6>
                        <p class="text-muted small mb-0">El aparato cambiará su estatus estructural a <span class="badge bg-danger">Fuera de Servicio</span> restringiendo temporalmente sus asignaciones en las grillas cronológicas.</p>
                    </div>
                    <div class="modal-footer p-3" style="border: none; background: var(--alpha-bg-main);">
                        <button type="button" class="btn btn-light border px-3 py-2 small fw-bold" style="border-radius:8px;" data-dismiss="modal">Regresar</button>
                        <button type="button" class="btn text-white px-3 py-2 small fw-bold" style="background: var(--alpha-warning); border-radius:8px;" onclick="confirmarDesactivacion(${id})">Confirmar Cese</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', modal);
    var myModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    myModal.show();
    document.getElementById('confirmModal').addEventListener('hidden.bs.modal', function() {
        this.remove();
    });
}

function reactivarAparato(id) {
    const modal = `
        <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content" style="border-radius: 12px; overflow: hidden; border: none; background: var(--alpha-card-bg);">
                    <div class="modal-header py-3" style="background: var(--alpha-success); border: none;">
                        <h5 class="modal-title text-white fw-bold"><i class="fas fa-play me-2"></i> Reactivación de Aparato</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body p-4 text-center">
                        <div class="mb-3"><i class="fas fa-check-circle text-success fa-3x"></i></div>
                        <h6 class="fw-bold text-navy-title mb-2" style="font-size:1.1rem;">¿Reincorporar componente técnico?</h6>
                        <p class="text-muted small mb-0">El aparato reingresará inmediatamente al catálogo de unidades en estado <span class="badge bg-success">Operativo</span> disponible para entrenamientos de alto rendimiento.</p>
                    </div>
                    <div class="modal-footer p-3" style="border: none; background: var(--alpha-bg-main);">
                        <button type="button" class="btn btn-light border px-3 py-2 small fw-bold" style="border-radius:8px;" data-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn text-white px-3 py-2 small fw-bold" style="background: var(--alpha-success); border-radius:8px;" onclick="confirmarReactivacion(${id})">Habilitar Elemento</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', modal);
    var myModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    myModal.show();
    document.getElementById('confirmModal').addEventListener('hidden.bs.modal', function() {
        this.remove();
    });
}

function confirmarDesactivacion(id) {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    $('#confirmModal').modal('hide');
    
    fetch(`/aparatos/${id}/desactivar`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': token,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const alert = `
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-left: 4px solid var(--alpha-success) !important; background: var(--alpha-card-bg); color: var(--alpha-text-main);">
                    <i class="fas fa-check-circle text-success me-2"></i> ${data.message}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">&times;</button>
                </div>
            `;
            document.getElementById('alertContainer').innerHTML = alert;
            setTimeout(() => location.reload(), 1200);
        } else {
            mostrarErrorAlert(data.error || 'Error crítico en la revocación de la unidad.');
        }
    })
    .catch(error => {
        mostrarErrorAlert('Error de comunicación con el Engine de datos.');
    });
}

function confirmarReactivacion(id) {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    $('#confirmModal').modal('hide');
    
    fetch(`/aparatos/${id}/reactivar`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': token,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const alert = `
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-left: 4px solid var(--alpha-success) !important; background: var(--alpha-card-bg); color: var(--alpha-text-main);">
                    <i class="fas fa-check-circle text-success me-2"></i> ${data.message}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">&times;</button>
                </div>
            `;
            document.getElementById('alertContainer').innerHTML = alert;
            setTimeout(() => location.reload(), 1200);
        } else {
            mostrarErrorAlert(data.error || 'Error crítico al intentar reincorporar la unidad.');
        }
    })
    .catch(error => {
        mostrarErrorAlert('Error de comunicación con el Engine de datos.');
    });
}

function finalizarMantenimiento(id) {
    const modal = `
        <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content" style="border-radius: 12px; overflow: hidden; border: none; background: var(--alpha-card-bg);">
                    <div class="modal-header py-3" style="background: var(--alpha-success); border: none;">
                        <h5 class="modal-title text-white fw-bold"><i class="fas fa-check-circle me-2"></i> Finalizar Mantenimiento</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body p-4 text-center">
                        <div class="mb-3"><i class="fas fa-check-circle text-success fa-3x"></i></div>
                        <h6 class="fw-bold text-navy-title mb-2" style="font-size:1.1rem;">¿Confirmar finalización de mantenimiento?</h6>
                        <p class="text-muted small mb-0">El aparato volverá al estado <span class="badge bg-success">Operativo</span> y se registrará la fecha de mantenimiento como hoy.</p>
                    </div>
                    <div class="modal-footer p-3" style="border: none; background: var(--alpha-bg-main);">
                        <button type="button" class="btn btn-light border px-3 py-2 small fw-bold" style="border-radius:8px;" data-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn text-white px-3 py-2 small fw-bold" style="background: var(--alpha-success); border-radius:8px;" onclick="confirmarFinalizarMantenimiento(${id})">Finalizar Mantenimiento</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', modal);
    var myModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    myModal.show();
    document.getElementById('confirmModal').addEventListener('hidden.bs.modal', function() {
        this.remove();
    });
}

function confirmarFinalizarMantenimiento(id) {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    $('#confirmModal').modal('hide');
    
    fetch(`/aparatos/${id}/finalizar-mantenimiento`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': token,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const alert = `
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-left: 4px solid var(--alpha-success) !important; background: var(--alpha-card-bg); color: var(--alpha-text-main);">
                    <i class="fas fa-check-circle text-success me-2"></i> ${data.message}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">&times;</button>
                </div>
            `;
            document.getElementById('alertContainer').innerHTML = alert;
            setTimeout(() => location.reload(), 1200);
        } else {
            mostrarErrorAlert(data.error || 'Error al finalizar mantenimiento.');
        }
    })
    .catch(error => {
        mostrarErrorAlert('Error de comunicación con el servidor.');
    });
}

function mostrarErrorAlert(mensaje) {
    const alert = `
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-left: 4px solid var(--alpha-danger) !important; background: var(--alpha-card-bg); color: var(--alpha-text-main);">
            <i class="fas fa-circle-exclamation text-danger me-2"></i> ${mensaje}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    document.getElementById('alertContainer').innerHTML = alert;
}

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('buscar').addEventListener('input', aplicarFiltros);
    document.getElementById('filtroEstado').addEventListener('change', aplicarFiltros);
    document.getElementById('filtroMantenimiento').addEventListener('change', aplicarFiltros);
    
    document.getElementById('buscar').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            aplicarFiltros();
        }
    });
});
</script>
@endsection