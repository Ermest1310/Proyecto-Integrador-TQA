@extends('layouts.entrenador')

@section('title', 'Gestión de Niveles')

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
            --alpha-info: #0ea5e9;
            
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
        
        /* Contenedores Premium Style */
        .alpha-glass-card { 
            background: var(--alpha-card-bg); 
            border-radius: var(--alpha-radius-lg); 
            box-shadow: var(--alpha-shadow-md); 
            border: 1px solid rgba(11, 25, 44, 0.04); 
        }

        /* Controles de Formulario */
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

        /* Botones del Ecosistema AlphaSigma */
        .btn-alpha-primary { background: var(--alpha-success); color: white; font-weight: 700; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; padding: 10px 20px; border-radius: 8px; transition: var(--alpha-transition); border: none; }
        .btn-alpha-primary:hover { background-color: #0d9488; transform: translateY(-2px); color: white; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3); }
        
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

        /* Insignias de Nivel y Estado */
        .alpha-badge { padding: 5px 12px; border-radius: 30px; font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; display: inline-block; }
        .badge-alpha-success { background: rgba(16, 185, 129, 0.12); color: var(--alpha-success); }
        .badge-alpha-danger { background: rgba(239, 68, 68, 0.12); color: var(--alpha-danger); }
        .badge-alpha-warning { background: rgba(245, 158, 11, 0.12); color: var(--alpha-warning); }
        .badge-alpha-info { background: rgba(14, 165, 233, 0.12); color: var(--alpha-info); }
        .badge-alpha-secondary { background: rgba(100, 116, 139, 0.12); color: var(--alpha-text-muted); }

        body:not([data-theme="dark"]) .text-navy-title { color: var(--alpha-navy-dark) !important; }
        [data-theme="dark"] .text-navy-title { color: #ffffff !important; }
    </style>

    <div class="row mb-4 align-items-center animate-up">
        <div class="col-md-6">
            <h3 class="fw-bold text-navy-title mb-1">
                <i class="fas fa-chart-line text-gold me-2"></i>Gestión de Niveles Académicos
            </h3>
            <p class="text-muted small mb-0">Monitoreo de promociones de gimnastas, auditoría de competencias y control de transiciones técnicas.</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <button class="btn btn-alpha-primary" data-bs-toggle="modal" data-bs-target="#modalRecomendar">
                <i class="fas fa-plus me-1"></i> Recomendar Promoción
            </button>
        </div>
    </div>

    <div class="alpha-glass-card p-4 animate-up delay-1">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold text-navy-title mb-0">Solicitudes Cambios de Nivel</h5>
            <span class="alpha-badge badge-alpha-warning fw-bold">
                <i class="fas fa-clock me-1"></i> {{ $solicitudes->where('estado', 'pendiente')->count() }} Pendientes
            </span>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-xl-4 col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0 text-muted" style="border-radius: 8px 0 0 8px; border-color: rgba(30, 62, 98, 0.15);"><i class="fas fa-search"></i></span>
                    <input type="text" id="buscar" class="form-control alpha-form-control border-start-0" style="border-radius: 0 8px 8px 0;" placeholder="Buscar gimnasta...">
                </div>
            </div>
            <div class="col-xl-3 col-md-3">
                <select id="filtroEstado" class="form-select alpha-form-control">
                    <option value="">Todos los estados</option>
                    <option value="pendiente">Pendiente</option>
                    <option value="aprobada">Aprobada</option>
                    <option value="rechazada">Rechazada</option>
                </select>
            </div>
            <div class="col-xl-3 col-md-4">
                <select id="filtroNivel" class="form-select alpha-form-control">
                    <option value="">Todos los niveles</option>
                    <option value="Principiante">Principiante</option>
                    <option value="Intermedio">Intermedio</option>
                    <option value="Avanzado">Avanzado</option>
                </select>
            </div>
            <div class="col-xl-2 col-md-12 text-md-end">
                <button type="button" class="btn btn-alpha-secondary w-100" onclick="limpiarFiltros()">
                    <i class="fas fa-filter-circle-xmark me-1"></i> Limpiar
                </button>
            </div>
        </div>

        <div class="table-responsive alpha-table-container">
            <table class="table alpha-table">
                <thead>
                    <tr>
                        <th width="110">Fecha</th>
                        <th>Gimnasta</th>
                        <th>Nivel Actual</th>
                        <th>Nivel Solicitado</th>
                        <th>Tipo Origen</th>
                        <th>Estado</th>
                        <th width="140" class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($solicitudes as $solicitud)
                    <tr>
                        <td class="font-monospace text-muted small">{{ $solicitud->fecha_solicitud->format('d/m/Y') }}</td>
                        <td>
                            <div class="fw-bold text-navy-title">{{ $solicitud->gimnasta->Nombre }} {{ $solicitud->gimnasta->Apellido }}</div>
                            <small class="text-muted font-monospace d-block" style="font-size: 0.78rem;">{{ $solicitud->gimnasta->Email }}</small>
                        </td>
                        <td>
                            @php
                                $badgeActual = match($solicitud->nivel_actual) {
                                    'Principiante' => 'badge-alpha-secondary',
                                    'Intermedio' => 'badge-alpha-info',
                                    'Avanzado' => 'badge-alpha-warning',
                                    'Elite' => 'badge-alpha-danger',
                                    default => 'badge-alpha-secondary'
                                };
                            @endphp
                            <span class="alpha-badge {{ $badgeActual }}">{{ $solicitud->nivel_actual }}</span>
                        </td>
                        <td>
                            @php
                                $badgeSolicitado = match($solicitud->nivel_solicitado) {
                                    'Principiante' => 'badge-alpha-secondary',
                                    'Intermedio' => 'badge-alpha-info',
                                    'Avanzado' => 'badge-alpha-warning',
                                    'Elite' => 'badge-alpha-danger',
                                    default => 'badge-alpha-secondary'
                                };
                            @endphp
                            <span class="alpha-badge {{ $badgeSolicitado }}">{{ $solicitud->nivel_solicitado }}</span>
                        </td>
                        <td>
                            @if($solicitud->tipo === 'evaluacion')
                                <span class="alpha-badge badge-alpha-info"><i class="fas fa-file-signature me-1"></i> Evaluación</span>
                            @else
                                <span class="alpha-badge badge-alpha-success"><i class="fas fa-star me-1"></i> Recomendación</span>
                                @if($solicitud->entrenador)
                                    <small class="d-block text-muted mt-1" style="font-size:0.75rem;">Por: {{ $solicitud->entrenador->Nombre }}</small>
                                @endif
                            @endif
                        </td>
                        <td>
                            @if($solicitud->estado === 'pendiente')
                                <span class="alpha-badge badge-alpha-warning">Pendiente</span>
                            @elseif($solicitud->estado === 'aprobada')
                                <span class="alpha-badge badge-alpha-success">Aprobada</span>
                            @else
                                <span class="alpha-badge badge-alpha-danger">Rechazada</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-1 justify-content-center">
                                <button class="btn btn-info btn-alpha-icon text-white bg-info" onclick="verDetalles({{ json_encode($solicitud) }})" title="Ver Ficha Completa">
                                    <i class="fas fa-eye" style="font-size:0.85rem;"></i>
                                </button>
                                
                                @if($solicitud->estado === 'pendiente')
                                    <button class="btn btn-success btn-alpha-icon text-white bg-success" onclick="aprobar({{ $solicitud->ID_Solicitud }})" title="Aprobar Trámite">
                                        <i class="fas fa-check" style="font-size:0.85rem;"></i>
                                    </button>
                                    <button class="btn btn-danger btn-alpha-icon text-white bg-danger" onclick="rechazar({{ $solicitud->ID_Solicitud }})" title="Rechazar Trámite">
                                        <i class="fas fa-times" style="font-size:0.85rem;"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted small">
                            <i class="fas fa-folder-open d-block mb-2 fa-2x text-muted opacity-50"></i>
                            No existen registros ni solicitudes de nivel mapeadas en el sistema.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalRecomendar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:14px; border:none; background: var(--alpha-card-bg);">
            <form action="{{ route('entrenador.solicitudes-nivel.recomendar') }}" method="POST">
                @csrf
                <div class="modal-header py-3" style="background: var(--alpha-success); border:none;">
                    <h5 class="modal-title text-white fw-bold"><i class="fas fa-certificate me-2"></i> Recomendar Promoción</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-navy-title small">Gimnasta Bajo Matrícula *</label>
                        <select name="ID_Gimnasta" class="form-select alpha-form-control" required id="selectGimnasta">
                            <option value="">Seleccione un gimnasta...</option>
                            @foreach($gimnastas as $gimnasta)
                                <option value="{{ $gimnasta->ID_Usuario }}" data-nivel="{{ $gimnasta->Nivel }}">
                                    {{ $gimnasta->Nombre }} {{ $gimnasta->Apellido }} — [Nivel: {{ $gimnasta->Nivel }}]
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-navy-title small">Nivel Técnico Destinado *</label>
                        <select name="nivel_solicitado" class="form-select alpha-form-control" required id="selectNivel">
                            <option value="">Seleccione primero un gimnasta...</option>
                        </select>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-bold text-navy-title small">Justificación Analítica de la Promoción *</label>
                        <textarea name="comentarios_entrenador" class="form-control alpha-form-control" rows="4" required placeholder="Explica detalladamente las competencias físicas y técnicas adquiridas por el gimnasta..."></textarea>
                    </div>
                </div>
                <div class="modal-footer p-3 bg-light" style="border:none; background-color: var(--alpha-bg-main) !important;">
                    <button type="button" class="btn btn-light border px-3 py-2 small fw-bold" style="border-radius:8px;" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn text-white px-3 py-2 small fw-bold" style="background: var(--alpha-success); border-radius:8px;">Enviar Recomendación</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDetalles" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius:14px; border:none; background: var(--alpha-card-bg);">
            <div class="modal-header py-3" style="background: var(--alpha-navy-dark); border:none;">
                <h5 class="modal-title text-white fw-bold"><i class="fas fa-file-invoice me-2"></i> Expediente Técnico de Solicitud</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="detallesContent">
                </div>
            <div class="modal-footer p-3 bg-light" style="border:none; background-color: var(--alpha-bg-main) !important;">
                <button type="button" class="btn btn-secondary px-4 py-2 small fw-bold" style="border-radius:8px; background: var(--alpha-blue-light);" data-bs-dismiss="modal">Cerrar Expediente</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalAprobar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:14px; border:none; background: var(--alpha-card-bg);">
            <form id="formAprobar" method="POST">
                @csrf
                <div class="modal-header py-3" style="background: var(--alpha-success); border:none;">
                    <h5 class="modal-title text-white fw-bold"><i class="fas fa-check me-2"></i> Dictamen Positivo de Solicitud</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-navy-title fw-bold mb-3">¿Estás seguro de ratificar y aprobar la promoción de este atleta?</p>
                    <div class="mb-0">
                        <label class="form-label fw-bold text-muted small">Comentarios y Observaciones (Opcional)</label>
                        <textarea name="comentarios_entrenador" class="form-control alpha-form-control" rows="3" placeholder="Inserta anotaciones técnicas sobre el proceso..."></textarea>
                    </div>
                </div>
                <div class="modal-footer p-3 bg-light" style="border:none; background-color: var(--alpha-bg-main) !important;">
                    <button type="button" class="btn btn-light border px-3 py-2 small fw-bold" style="border-radius:8px;" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn text-white px-3 py-2 small fw-bold" style="background: var(--alpha-success); border-radius:8px;">Aprobar Promoción</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalRechazar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:14px; border:none; background: var(--alpha-card-bg);">
            <form id="formRechazar" method="POST">
                @csrf
                <div class="modal-header py-3" style="background: var(--alpha-danger); border:none;">
                    <h5 class="modal-title text-white fw-bold"><i class="fas fa-times me-2"></i> Declinar Cambio de Nivel</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-navy-title fw-bold mb-3">¿Confirmas el rechazo técnico de la postulación?</p>
                    <div class="mb-0">
                        <label class="form-label fw-bold text-navy-title small">Argumento / Motivo de Rechazo Mandatorio *</label>
                        <textarea name="comentarios_entrenador" class="form-control alpha-form-control" rows="3" required placeholder="Describe las falencias encontradas u observaciones por las cuales no califica..."></textarea>
                    </div>
                </div>
                <div class="modal-footer p-3 bg-light" style="border:none; background-color: var(--alpha-bg-main) !important;">
                    <button type="button" class="btn btn-light border px-3 py-2 small fw-bold" style="border-radius:8px;" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn text-white px-3 py-2 small fw-bold" style="background: var(--alpha-danger); border-radius:8px;">Denegar Solicitud</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
const niveles = {
    'Principiante': 'Intermedio',
    'Intermedio': 'Avanzado',
    'Avanzado': 'Elite',
    'Elite': null
};

document.getElementById('selectGimnasta').addEventListener('change', function() {
    const nivelActual = this.options[this.selectedIndex].dataset.nivel;
    const selectNivel = document.getElementById('selectNivel');
    const siguienteNivel = niveles[nivelActual];
    
    selectNivel.innerHTML = '';
    
    if (siguienteNivel) {
        selectNivel.innerHTML = `<option value="${siguienteNivel}">${siguienteNivel}</option>`;
    } else {
        selectNivel.innerHTML = '<option value="">El atleta se encuentra en el nivel máximo (Elite)</option>';
    }
});

function verDetalles(solicitud) {
    const renderBadge = (nivel) => {
        const cls = nivel === 'Principiante' ? 'badge-alpha-secondary' : (nivel === 'Intermedio' ? 'badge-alpha-info' : (nivel === 'Avanzado' ? 'badge-alpha-warning' : 'badge-alpha-danger'));
        return `<span class="alpha-badge ${cls}">${nivel}</span>`;
    };

    let html = `
        <div class="row g-3">
            <div class="col-md-6">
                <div class="small text-muted mb-1 font-monospace">ATLETA GIMNASTA</div>
                <h6 class="fw-bold text-navy-title mb-3"><i class="fas fa-user me-2 text-gold"></i>${solicitud.gimnasta.Nombre} ${solicitud.gimnasta.Apellido}</h6>
                <p class="mb-2 text-muted small"><strong>Fecha Solicitud:</strong> ${new Date(solicitud.fecha_solicitud).toLocaleDateString()}</p>
                <p class="mb-2 text-muted small"><strong>Tipo Origen:</strong> ${solicitud.tipo === 'evaluacion' ? 'Evaluación Técnica' : 'Recomendación Directa'}</p>
                <p class="mb-0 text-muted small"><strong>Transición de Rango:</strong> ${renderBadge(solicitud.nivel_actual)} <i class="fas fa-arrow-right mx-2 text-muted small"></i> ${renderBadge(solicitud.nivel_solicitado)}</p>
            </div>
            <div class="col-md-6 border-start ps-4">
                <div class="small text-muted mb-1 font-monospace">ESTADO DE TRÁMITE</div>
                <div class="mb-3"><span class="alpha-badge ${solicitud.estado === 'pendiente' ? 'badge-alpha-warning' : (solicitud.estado === 'aprobada' ? 'badge-alpha-success' : 'badge-alpha-danger')}">${solicitud.estado.toUpperCase()}</span></div>
                ${solicitud.fecha_respuesta ? `<p class="mb-2 text-muted small"><strong>Resolución Evaluada:</strong> ${new Date(solicitud.fecha_respuesta).toLocaleDateString()}</p>` : ''}
                ${solicitud.entrenador ? `<p class="mb-0 text-muted small"><strong>Especialista Auditor:</strong> ${solicitud.entrenador.Nombre} ${solicitud.entrenador.Apellido}</p>` : ''}
            </div>
        </div>
        <hr class="my-4 opacity-10">
        ${solicitud.comentarios_gimnasta ? `<div class="p-3 mb-3 border-0 rounded-3 alert-type-info" style="background: rgba(14,165,233,0.06); border-left: 4px solid var(--alpha-info) !important;"><strong class="text-navy-title small d-block mb-1"><i class="fas fa-comment-alt me-2 text-info"></i>Argumentación del Gimnasta:</strong><div class="text-muted small">${solicitud.comentarios_gimnasta}</div></div>` : ''}
        ${solicitud.comentarios_entrenador ? `<div class="p-3 mb-3 border-0 rounded-3" style="background: rgba(16,185,129,0.06); border-left: 4px solid var(--alpha-success) !important;"><strong class="text-navy-title small d-block mb-1"><i class="fas fa-user-tie me-2 text-success"></i>Informe de Justificación del Entrenador:</strong><div class="text-muted small">${solicitud.comentarios_entrenador}</div></div>` : ''}
        ${solicitud.comentarios_admin ? `<div class="p-3 border-0 rounded-3" style="background: ${solicitud.estado === 'aprobada' ? 'rgba(16,185,129,0.06)' : 'rgba(239,68,68,0.06)'}; border-left: 4px solid ${solicitud.estado === 'aprobada' ? 'var(--alpha-success)' : 'var(--alpha-danger)'} !important;"><strong class="text-navy-title small d-block mb-1"><i class="fas fa-gavel me-2"></i>Resolución del Administrador:</strong><div class="text-muted small">${solicitud.comentarios_admin}</div></div>` : ''}
    `;
    
    document.getElementById('detallesContent').innerHTML = html;
    var myModal = new bootstrap.Modal(document.getElementById('modalDetalles'));
    myModal.show();
}

function aprobar(id) {
    document.getElementById('formAprobar').action = `/entrenador/solicitudes-nivel/${id}/aprobar`;
    var myModal = new bootstrap.Modal(document.getElementById('modalAprobar'));
    myModal.show();
}

function rechazar(id) {
    document.getElementById('formRechazar').action = `/entrenador/solicitudes-nivel/${id}/rechazar`;
    var myModal = new bootstrap.Modal(document.getElementById('modalRechazar'));
    myModal.show();
}

// Filtros de Tabla
function aplicarFiltros() {
    const buscar = document.getElementById('buscar').value.toLowerCase();
    const estado = document.getElementById('filtroEstado').value;
    const nivel = document.getElementById('filtroNivel').value;
    
    const filas = document.querySelectorAll('tbody tr');
    
    filas.forEach(function(fila) {
        if(fila.cells.length < 7) return; // Salta la fila de vacio
        const textoCompleto = fila.textContent.toLowerCase();
        const celdas = fila.querySelectorAll('td');
        
        let mostrar = true;
        
        if (buscar && !textoCompleto.includes(buscar)) mostrar = false;
        if (estado && !celdas[5].textContent.toLowerCase().includes(estado)) mostrar = false;
        if (nivel && !celdas[2].textContent.includes(nivel)) mostrar = false;
        
        fila.style.display = mostrar ? '' : 'none';
    });
}

function limpiarFiltros() {
    document.getElementById('buscar').value = '';
    document.getElementById('filtroEstado').value = '';
    document.getElementById('filtroNivel').value = '';
    aplicarFiltros();
}

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('buscar').addEventListener('input', aplicarFiltros);
    document.getElementById('filtroEstado').addEventListener('change', aplicarFiltros);
    document.getElementById('filtroNivel').addEventListener('change', aplicarFiltros);
});
</script>
@endsection