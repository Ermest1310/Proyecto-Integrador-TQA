@extends('layouts.admin')

@section('title', 'Sesiones de Entrenamiento')

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
            
            --alpha-radius-lg: 16px;
            --alpha-radius-md: 12px;
            --alpha-shadow-sm: 0 4px 6px -1px rgb(0 0 0 / 0.05);
            --alpha-shadow-md: 0 10px 15px -3px rgb(11 25 44 / 0.06);
            --alpha-shadow-lg: 0 20px 25px -5px rgb(11 25 44 / 0.1);
            --alpha-transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Modo Oscuro */
        [data-theme="dark"] {
            --alpha-bg-main: #07101c;
            --alpha-card-bg: #122239;
            --alpha-text-main: #f1f5f9;
            --alpha-text-muted: #cbd5e1;
        }

        @keyframes alphaFadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-up { animation: alphaFadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) both; }
        
        /* Contenedores Premium */
        .alpha-glass-list-card { 
            background: var(--alpha-card-bg); 
            border-radius: var(--alpha-radius-lg); 
            box-shadow: var(--alpha-shadow-md); 
            border: 1px solid rgba(11, 25, 44, 0.03); 
        }

        /* Inputs y Selects Estilizados */
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

        /* Botones personalizados */
        .btn-alpha-action-primary { background: var(--alpha-gold); color: white; font-weight: 700; font-size: 0.8rem; text-transform: uppercase; padding: 12px 20px; border-radius: 10px; transition: var(--alpha-transition); border: none; text-align: center; }
        .btn-alpha-action-primary:hover { background-color: #a88d50; transform: translateY(-2px); color: white; }
        
        .btn-alpha-secondary { background: var(--alpha-blue-light); color: white; font-weight: 600; font-size: 0.85rem; padding: 11px 16px; border-radius: 8px; transition: var(--alpha-transition); border: none; }
        .btn-alpha-secondary:hover { background: var(--alpha-navy-dark); color: white; }

        /* Estilos de Tabla */
        .alpha-table-container { border-radius: var(--alpha-radius-md); overflow: hidden; }
        .alpha-table { width: 100%; margin-bottom: 0; color: var(--alpha-text-main); vertical-align: middle; }
        .alpha-table thead th { background-color: var(--alpha-navy-dark); color: #ffffff; font-family: font-monospace; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; border: none; padding: 14px; }
        [data-theme="dark"] .alpha-table thead th { background-color: var(--alpha-navy-medium); }
        .alpha-table tbody tr { border-bottom: 1px solid rgba(11, 25, 44, 0.05); transition: var(--alpha-transition); }
        .alpha-table tbody tr:hover { background-color: rgba(191, 161, 95, 0.03); }
        .alpha-table td { padding: 16px 14px; font-size: 0.9rem; }

        /* Badges de Estado */
        .alpha-badge { padding: 6px 12px; border-radius: 30px; font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; display: inline-block; }
        .badge-alpha-programada { background: rgba(30, 62, 98, 0.15); color: var(--alpha-blue-light); }
        .badge-alpha-completada { background: rgba(16, 185, 129, 0.15); color: var(--alpha-success); }
        .badge-alpha-curso { background: rgba(191, 161, 95, 0.18); color: #a88d50; }
        .badge-alpha-cancelada { background: rgba(239, 68, 68, 0.15); color: var(--alpha-danger); }
        .badge-alpha-auto { background: rgba(11, 25, 44, 0.08); color: var(--alpha-navy-dark); border: 1px solid rgba(11, 25, 44, 0.1); }
        [data-theme="dark"] .badge-alpha-auto { background: rgba(255, 255, 255, 0.08); color: #ffffff; }

        body:not([data-theme="dark"]) .text-navy-title { color: var(--alpha-navy-dark) !important; }
        [data-theme="dark"] .text-navy-title { color: #ffffff !important; }
    </style>

    <div class="row mb-4 align-items-center animate-up">
        <div class="col-md-6">
            <h3 class="fw-bold text-navy-title mb-1">
                <i class="fas fa-calendar-check text-gold me-2"></i>Planificación Horaria
            </h3>
            <p class="text-muted small mb-0">Administración de bloques cronológicos de entrenamiento, agendas y reservas físicas.</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <a href="{{ route('sesiones.create') }}" class="btn btn-alpha-action-primary">
                <i class="fas fa-plus-circle me-1"></i> Nueva Sesión de Entrenamiento
            </a>
        </div>
    </div>

    <div class="alpha-glass-list-card p-4 animate-up">
        
        <div id="alertContainer"></div>
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 rounded-3 mb-4" role="alert" style="background-color: rgba(16, 185, 129, 0.15); color: var(--alpha-success);">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row g-2 mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0 text-muted" style="border-radius: 8px 0 0 8px; border-color: rgba(30, 62, 98, 0.15);"><i class="fas fa-search"></i></span>
                    <input type="text" id="buscar" class="form-control alpha-form-control border-start-0" style="border-radius: 0 8px 8px 0;" placeholder="Buscar en la grilla...">
                </div>
            </div>
            <div class="col-xl-2 col-md-4">
                <select id="filtroGimnasta" class="form-select alpha-form-control">
                    <option value="">Gimnastas (Todos)</option>
                    @foreach($gimnastas as $gimnasta)
                        <option value="{{ $gimnasta->ID_Usuario }}">{{ $gimnasta->Nombre }} {{ $gimnasta->Apellido }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-xl-2 col-md-4">
                <select id="filtroEntrenador" class="form-select alpha-form-control">
                    <option value="">Entrenadores (Todos)</option>
                    @foreach($entrenadores as $entrenador)
                        <option value="{{ $entrenador->ID_Usuario }}">{{ $entrenador->Nombre }} {{ $entrenador->Apellido }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-xl-2 col-md-4">
                <select id="filtroAparato" class="form-select alpha-form-control">
                    <option value="">Aparatos FIG (Todos)</option>
                    @foreach($aparatos as $aparato)
                        <option value="{{ $aparato->ID_Aparato }}">{{ $aparato->Nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-xl-2 col-md-4">
                <select id="filtroEstado" class="form-select alpha-form-control">
                    <option value="">Estados (Todos)</option>
                    <option value="Programada">Programada</option>
                    <option value="Completada">Completada</option>
                    <option value="En_Curso">En Curso</option>
                </select>
            </div>
            <div class="col-xl-1 col-md-4">
                <button type="button" class="btn btn-alpha-secondary w-100" onclick="limpiarFiltros()" title="Limpiar Filtros">
                    <i class="fas fa-filter-circle-xmark"></i>
                </button>
            </div>
        </div>

        <div class="table-responsive alpha-table-container">
            <table class="table alpha-table">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Horario Operativo</th>
                        <th>Gimnasta Élite</th>
                        <th>Entrenador Responsable</th>
                        <th>Aparato FIG</th>
                        <th>Asignación</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sesiones as $sesion)
                    <tr>
                        <td class="font-monospace fw-bold text-navy-title">{{ $sesion->Fecha->format('d . M . Y') }}</td>
                        <td class="font-monospace text-muted">{{ $sesion->Hora_Inicio->format('H:i') }} - {{ $sesion->Hora_Fin->format('H:i') }}</td>
                        <td>
                            <div class="fw-bold text-navy-title">{{ $sesion->gimnasta->Nombre }} {{ $sesion->gimnasta->Apellido }}</div>
                        </td>
                        <td>{{ $sesion->entrenador->Nombre }} {{ $sesion->entrenador->Apellido }}</td>
                        <td>
                            <span class="badge" style="background: rgba(30, 62, 98, 0.08); color: var(--alpha-blue-light); font-weight: 600;">
                                <i class="fas fa-vector-square me-1 text-gold"></i>{{ $sesion->aparato->Nombre ?? 'General' }}
                            </span>
                        </td>
                        <td>
                            <span class="alpha-badge badge-alpha-auto font-monospace text-xs">
                                {{ $sesion->Tipo_Asignacion }}
                            </span>
                        </td>
                        <td>
                            @php
                                $estadoClass = 'badge-alpha-programada';
                                if($sesion->Estado == 'Completada') $estadoClass = 'badge-alpha-completada';
                                if($sesion->Estado == 'En_Curso') $estadoClass = 'badge-alpha-curso';
                                if($sesion->Estado == 'Cancelada') $estadoClass = 'badge-alpha-cancelada';
                            @endphp
                            <span class="alpha-badge {{ $estadoClass }}">
                                <i class="fas fa-circle me-1" style="font-size: 0.5rem; vertical-align: middle;"></i>{{ str_replace('_', ' ', $sesion->Estado) }}
                            </span>
                        </td>
                        <td class="text-end">
                            @if($sesion->Estado == 'Programada')
                                <a href="{{ route('sesiones.edit', $sesion->ID_Sesion) }}" 
                                   class="btn btn-sm btn-light border p-2" style="border-radius: 6px; color: var(--alpha-gold);" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-sm btn-light border p-2 ms-1 text-danger" style="border-radius: 6px;"
                                        onclick="cancelarSesion({{ $sesion->ID_Sesion }})" 
                                        title="Cancelar">
                                    <i class="fas fa-times-circle"></i>
                                </button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function cancelarSesion(id) {
    const modal = `
        <div class="modal fade" id="cancelModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0" style="background: var(--alpha-card-bg); border-radius: var(--alpha-radius-lg); box-shadow: var(--alpha-shadow-lg);">
                    <div class="modal-header border-0 p-4 pb-0 d-flex justify-content-between align-items-center">
                        <h5 class="modal-title fw-bold text-navy-title"><i class="fas fa-exclamation-triangle text-danger me-2"></i> Cancelar Sesión</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <p class="mb-2 fw-semibold text-navy-title">¿Está completamente seguro de que desea cancelar esta sesión cronometrada?</p>
                        <p class="text-muted small mb-0">Esta acción suspenderá el bloque de control biomecánico establecido en los aparatos.</p>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-light border text-secondary" data-bs-dismiss="modal" style="border-radius: 8px; font-weight:600;">
                            <i class="fas fa-arrow-left me-1"></i> Mantener Activa
                        </button>
                        <button type="button" class="btn btn-danger" onclick="confirmarCancelacion(${id})" style="border-radius: 8px; background-color: var(--alpha-danger); border:none; font-weight:600;">
                            <i class="fas fa-times-circle me-1"></i> Sí, Cancelar Sesión
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', modal);
    var myModal = new bootstrap.Modal(document.getElementById('cancelModal'));
    myModal.show();
    
    document.getElementById('cancelModal').addEventListener('hidden.bs.modal', function () {
        this.remove();
    });
}

function confirmarCancelacion(id) {
    var modalEl = document.getElementById('cancelModal');
    var modalInstance = bootstrap.Modal.getInstance(modalEl);
    
    fetch(`/sesiones/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        modalInstance.hide();
        if (data.success) {
            const alert = `
                <div class="alert alert-success alert-dismissible fade show border-0 rounded-3 mb-4" role="alert" style="background-color: rgba(16, 185, 129, 0.15); color: var(--alpha-success);">
                    <i class="fas fa-check-circle me-2"></i> ${data.message}
                </div>
            `;
            document.getElementById('alertContainer').innerHTML = alert;
            setTimeout(() => location.reload(), 1500);
        } else {
            const alert = `
                <div class="alert alert-danger alert-dismissible fade show border-0 rounded-3 mb-4" role="alert" style="background-color: rgba(239, 68, 68, 0.15); color: var(--alpha-danger);">
                    <i class="fas fa-exclamation-circle me-2"></i> ${data.error || 'Error al cancelar la sesión'}
                </div>
            `;
            document.getElementById('alertContainer').innerHTML = alert;
        }
    })
    .catch(error => {
        modalInstance.hide();
        const alert = `
            <div class="alert alert-danger border-0 rounded-3 mb-4" role="alert" style="background-color: rgba(239, 68, 68, 0.15); color: var(--alpha-danger);">
                <i class="fas fa-exclamation-circle me-2"></i> Error al conectar con el servidor central de analíticas.
            </div>
        `;
        document.getElementById('alertContainer').innerHTML = alert;
    });
}

// Filtros Dinámicos de Grilla
function aplicarFiltros() {
    const buscar = document.getElementById('buscar').value.toLowerCase();
    const gimnastaSelect = document.getElementById('filtroGimnasta');
    const entrenadorSelect = document.getElementById('filtroEntrenador');
    const aparatoSelect = document.getElementById('filtroAparato');
    const estado = document.getElementById('filtroEstado').value;
    
    const gimnastaTexto = gimnastaSelect.options[gimnastaSelect.selectedIndex].text;
    const entrenadorTexto = entrenadorSelect.options[entrenadorSelect.selectedIndex].text;
    const aparatoTexto = aparatoSelect.options[aparatoSelect.selectedIndex].text;
    
    const filas = document.querySelectorAll('.alpha-table tbody tr');
    
    filas.forEach(function(fila) {
        const celdas = fila.querySelectorAll('td');
        if(celdas.length === 0) return;
        
        const textoCompleto = fila.textContent.toLowerCase();
        const gimnastaFila = celdas[2].textContent.trim();
        const entrenadorFila = celdas[3].textContent.trim();
        const aparatoFila = celdas[4].textContent.trim();
        const estadoFila = celdas[6].textContent.trim();
        
        let mostrar = true;
        
        if (buscar && !textoCompleto.includes(buscar)) mostrar = false;
        if (gimnastaSelect.value && gimnastaFila !== gimnastaTexto) mostrar = false;
        if (entrenadorSelect.value && entrenadorFila !== entrenadorTexto) mostrar = false;
        if (aparatoSelect.value && !aparatoFila.includes(aparatoTexto)) mostrar = false;
        if (estado && !estadoFila.toLowerCase().includes(estado.toLowerCase().replace('_', ' '))) mostrar = false;
        
        fila.style.display = mostrar ? '' : 'none';
    });
}

function limpiarFiltros() {
    document.getElementById('buscar').value = '';
    document.getElementById('filtroGimnasta').value = '';
    document.getElementById('filtroEntrenador').value = '';
    document.getElementById('filtroAparato').value = '';
    document.getElementById('filtroEstado').value = '';
    aplicarFiltros();
}

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('buscar').addEventListener('input', aplicarFiltros);
    document.getElementById('filtroGimnasta').addEventListener('change', aplicarFiltros);
    document.getElementById('filtroEntrenador').addEventListener('change', aplicarFiltros);
    document.getElementById('filtroAparato').addEventListener('change', aplicarFiltros);
    document.getElementById('filtroEstado').addEventListener('change', aplicarFiltros);
    
    document.getElementById('buscar').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            aplicarFiltros();
        }
    });
});
</script>
@endsection