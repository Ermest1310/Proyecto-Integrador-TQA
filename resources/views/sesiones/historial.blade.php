@extends('layouts.admin')

@section('title', 'Historial de Sesiones')

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

        /* Soporte Nativo Modo Oscuro */
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
        .delay-1 { animation-delay: 0.1s; }
        
        /* Contenedores Premium */
        .alpha-glass-list-card { 
            background: var(--alpha-card-bg); 
            border-radius: var(--alpha-radius-lg); 
            box-shadow: var(--alpha-shadow-md); 
            border: 1px solid rgba(11, 25, 44, 0.03); 
        }

        .alpha-filter-box {
            background-color: var(--alpha-bg-main);
            border-radius: var(--alpha-radius-md);
            border: 1px solid rgba(30, 62, 98, 0.08);
        }

        /* Controles de Formulario Estilizados */
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

        /* Botones e Interactivos */
        .btn-alpha-action-primary { background: var(--alpha-gold); color: white; font-weight: 700; font-size: 0.8rem; text-transform: uppercase; padding: 10px 18px; border-radius: 8px; transition: var(--alpha-transition); border: none; text-align: center; }
        .btn-alpha-action-primary:hover { background-color: #a88d50; transform: translateY(-2px); color: white; }
        
        .btn-alpha-secondary { background: var(--alpha-blue-light); color: white; font-weight: 600; font-size: 0.85rem; padding: 10px 16px; border-radius: 8px; transition: var(--alpha-transition); border: none; }
        .btn-alpha-secondary:hover { background: var(--alpha-navy-dark); color: white; }

        .btn-alpha-outline-gold { border: 1px solid var(--alpha-gold); color: var(--alpha-gold); background: transparent; font-weight: 600; font-size: 0.85rem; padding: 10px 16px; border-radius: 8px; transition: var(--alpha-transition); }
        .btn-alpha-outline-gold:hover { background: var(--alpha-gold); color: white; }

        .btn-alpha-xs { padding: 4px 10px; font-size: 0.75rem; border-radius: 6px; font-weight: 600; }

        /* Estilos de Tabla */
        .alpha-table-container { border-radius: var(--alpha-radius-md); overflow: hidden; }
        .alpha-table { width: 100%; margin-bottom: 0; color: var(--alpha-text-main); vertical-align: middle; }
        .alpha-table thead th { background-color: var(--alpha-navy-dark); color: #ffffff; font-family: font-monospace; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; border: none; padding: 14px; }
        [data-theme="dark"] .alpha-table thead th { background-color: var(--alpha-navy-medium); }
        .alpha-table tbody tr { border-bottom: 1px solid rgba(11, 25, 44, 0.05); transition: var(--alpha-transition); }
        .alpha-table tbody tr:hover { background-color: rgba(191, 161, 95, 0.02); }
        .alpha-table td { padding: 16px 14px; font-size: 0.9rem; border: none; }

        /* Badges de Estado */
        .alpha-badge { padding: 6px 12px; border-radius: 30px; font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; display: inline-block; }
        .badge-alpha-completada { background: rgba(16, 185, 129, 0.15); color: var(--alpha-success); }
        .badge-alpha-cancelada { background: rgba(239, 68, 68, 0.15); color: var(--alpha-danger); }
        .badge-alpha-info { background: rgba(30, 62, 98, 0.1); color: var(--alpha-blue-light); }
        [data-theme="dark"] .badge-alpha-info { background: rgba(255, 255, 255, 0.08); color: #ffffff; }

        body:not([data-theme="dark"]) .text-navy-title { color: var(--alpha-navy-dark) !important; }
        [data-theme="dark"] .text-navy-title { color: #ffffff !important; }
    </style>

    <div class="row mb-4 align-items-center animate-up">
        <div class="col-md-6">
            <h3 class="fw-bold text-navy-title mb-1">
                <i class="fas fa-history text-gold me-2"></i>Historial de Sesiones
            </h3>
            <p class="text-muted small mb-0">Registro y auditoría histórica de bloques cronológicos consolidados y cancelados.</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <a href="{{ route('sesiones.index') }}" class="btn btn-alpha-outline-gold">
                <i class="fas fa-arrow-left me-1"></i> Volver a Sesiones Activas
            </a>
        </div>
    </div>

    <div class="alpha-glass-list-card p-4 animate-up delay-1">
        
        <div class="row g-3 mb-4 align-items-center">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0 text-muted" style="border-radius: 8px 0 0 8px; border-color: rgba(30, 62, 98, 0.15);"><i class="fas fa-search"></i></span>
                    <input type="text" id="busquedaRapida" class="form-control alpha-form-control border-start-0" style="border-radius: 0 8px 8px 0;" placeholder="Buscar por gimnasta, entrenador, aparato FIG...">
                </div>
            </div>
            <div class="col-md-6 text-md-end">
                <button class="btn btn-alpha-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#filtrosAvanzadosPanel" aria-expanded="false">
                    <i class="fas fa-sliders me-1"></i> Filtros Avanzados
                </button>
            </div>
        </div>
        
        <div class="collapse mb-4" id="filtrosAvanzadosPanel">
            <div class="alpha-filter-box p-4">
                <form method="GET" action="{{ route('sesiones.historial') }}">
                    <div class="row g-3">
                        <div class="col-xl-2 col-md-4">
                            <label class="form-label text-muted small fw-bold">Fecha Desde:</label>
                            <input type="date" name="fecha_desde" class="form-control alpha-form-control" value="{{ request('fecha_desde') }}">
                        </div>
                        <div class="col-xl-2 col-md-4">
                            <label class="form-label text-muted small fw-bold">Fecha Hasta:</label>
                            <input type="date" name="fecha_hasta" class="form-control alpha-form-control" value="{{ request('fecha_hasta') }}">
                        </div>
                        <div class="col-xl-2 col-md-4">
                            <label class="form-label text-muted small fw-bold">Gimnasta:</label>
                            <select name="gimnasta" class="form-select alpha-form-control">
                                <option value="">Todos</option>
                                @foreach($gimnastas as $gimnasta)
                                    <option value="{{ $gimnasta->ID_Usuario }}" {{ request('gimnasta') == $gimnasta->ID_Usuario ? 'selected' : '' }}>
                                        {{ $gimnasta->Nombre }} {{ $gimnasta->Apellido }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-xl-2 col-md-4">
                            <label class="form-label text-muted small fw-bold">Entrenador:</label>
                            <select name="entrenador" class="form-select alpha-form-control">
                                <option value="">Todos</option>
                                @foreach($entrenadores as $entrenador)
                                    <option value="{{ $entrenador->ID_Usuario }}" {{ request('entrenador') == $entrenador->ID_Usuario ? 'selected' : '' }}>
                                        {{ $entrenador->Nombre }} {{ $entrenador->Apellido }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-xl-2 col-md-4">
                            <label class="form-label text-muted small fw-bold">Aparato FIG:</label>
                            <select name="aparato" class="form-select alpha-form-control">
                                <option value="">Todos</option>
                                @foreach($aparatos as $aparato)
                                    <option value="{{ $aparato->ID_Aparato }}" {{ request('aparato') == $aparato->ID_Aparato ? 'selected' : '' }}>
                                        {{ $aparato->Nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-xl-2 col-md-4">
                            <label class="form-label text-muted small fw-bold">Estado:</label>
                            <select name="estado" class="form-select alpha-form-control">
                                <option value="">Todos</option>
                                <option value="Completada" {{ request('estado') == 'Completada' ? 'selected' : '' }}>Completada</option>
                                <option value="Cancelada" {{ request('estado') == 'Cancelada' ? 'selected' : '' }}>Cancelada</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top d-flex flex-wrap gap-2 justify-content-between align-items-center" style="border-color: rgba(30,62,98,0.05) !important;">
                        <div>
                            <button type="submit" class="btn btn-alpha-action-primary">
                                <i class="fas fa-filter me-1"></i> Aplicar Parámetros
                            </button>
                            <a href="{{ route('sesiones.historial') }}" class="btn btn-light border ms-1" style="border-radius: 8px; padding: 10px 16px; font-size: 0.85rem;">
                                <i class="fas fa-eraser me-1"></i> Reestablecer
                            </a>
                        </div>
                        
                        <div class="d-flex flex-wrap gap-1 align-items-center">
                            <span class="text-muted small me-1">Rangos rápidos:</span>
                            <a href="{{ route('sesiones.historial', ['fecha_desde' => now()->subDays(7)->format('Y-m-d'), 'fecha_hasta' => now()->format('Y-m-d')]) }}" class="btn btn-outline-secondary btn-alpha-xs btn-sm">
                                <i class="fas fa-calendar-week me-1"></i>7 Días
                            </a>
                            <a href="{{ route('sesiones.historial', ['fecha_desde' => now()->subDays(30)->format('Y-m-d')]) }}" class="btn btn-outline-secondary btn-alpha-xs btn-sm">
                                <i class="fas fa-calendar-alt me-1"></i>Mes Actual
                            </a>
                            <a href="{{ route('sesiones.historial', ['estado' => 'Completada']) }}" class="btn btn-outline-success btn-alpha-xs btn-sm">
                                <i class="fas fa-check me-1"></i>Completadas
                            </a>
                            <a href="{{ route('sesiones.historial', ['estado' => 'Cancelada']) }}" class="btn btn-outline-danger btn-alpha-xs btn-sm">
                                <i class="fas fa-times me-1"></i>Canceladas
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="mb-3">
            <span class="text-muted small bg-light px-3 py-2 rounded-3 border d-inline-block" style="background-color: var(--alpha-bg-main) !important;">
                <i class="fas fa-chart-bar text-gold me-2"></i>
                <span id="contadorTexto">
                    Mostrando {{ $sesiones->count() }} bloque{{ $sesiones->count() != 1 ? 's' : '' }} histórico{{ $sesiones->count() != 1 ? 's' : '' }}
                    @if(request()->hasAny(['fecha_desde', 'fecha_hasta', 'gimnasta', 'entrenador', 'aparato', 'estado']))
                        (bajo criterios de filtrado)
                    @endif
                </span>
            </span>
        </div>
        
        @if($sesiones->count() > 0)
            <div class="table-responsive alpha-table-container">
                <table class="table alpha-table">
                    <thead>
                        <tr>
                            <th>Bloque Temporal</th>
                            <th>Franja Horaria</th>
                            <th>Duración Efectiva</th>
                            <th>Gimnasta Élite</th>
                            <th>Entrenador Analista</th>
                            <th>Aparato Asignado</th>
                            <th>Estado Final</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sesiones as $sesion)
                        <tr>
                            <td>
                                <div class="font-monospace fw-bold text-navy-title mb-0">{{ $sesion->Fecha->format('d/m/Y') }}</div>
                                <small class="text-muted text-xs d-block mt-0">{{ $sesion->Fecha->diffForHumans() }}</small>
                            </td>
                            <td class="font-monospace text-muted">
                                <i class="far fa-clock text-gold me-1" style="font-size: 0.8rem;"></i>
                                {{ $sesion->Hora_Inicio->format('H:i') }} - {{ $sesion->Hora_Fin->format('H:i') }}
                            </td>
                            <td>
                                <span class="alpha-badge badge-alpha-info font-monospace text-xs">
                                    {{ $sesion->Duracion_Minutos }} min
                                </span>
                            </td>
                            <td>
                                <div class="fw-bold text-navy-title mb-0">{{ $sesion->gimnasta->Nombre }} {{ $sesion->gimnasta->Apellido }}</div>
                                <small class="text-muted text-xs d-block">{{ $sesion->gimnasta->Nivel }}</small>
                            </td>
                            <td>
                                <span class="text-muted"><i class="far fa-user-circle me-1"></i>{{ $sesion->entrenador->Nombre }} {{ $sesion->entrenador->Apellido }}</span>
                            </td>
                            <td>
                                @if($sesion->aparato)
                                    <span class="badge" style="background: rgba(30, 62, 98, 0.06); color: var(--alpha-blue-light); font-weight: 600; padding: 6px 10px;">
                                        <i class="fas fa-vector-square me-1 text-gold"></i>{{ $sesion->aparato->Nombre }}
                                    </span>
                                @else
                                    <span class="text-muted small italic">Entrenamiento General</span>
                                @endif
                            </td>
                            <td>
                                @if($sesion->Estado == 'Completada')
                                    <span class="alpha-badge badge-alpha-completada">
                                        <i class="fas fa-check-circle me-1"></i> Completada
                                    </span>
                                @else
                                    <span class="alpha-badge badge-alpha-cancelada">
                                        <i class="fas fa-ban me-1"></i> Cancelada
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <div class="mb-3 d-inline-flex align-items-center justify-content-center bg-light rounded-circle" style="width: 80px; height: 80px; background-color: var(--alpha-bg-main) !important;">
                    <i class="fas fa-folder-open fa-2x text-muted" style="color: var(--alpha-gold) !important;"></i>
                </div>
                <h5 class="fw-bold text-navy-title mb-1">Sin Registros en el Historial</h5>
                <p class="text-muted small mx-auto mb-4" style="max-width: 380px;">No se localizan bloques operativos completados o revocados que coincidan con los parámetros configurados actualmente.</p>
                <a href="{{ route('sesiones.index') }}" class="btn btn-alpha-action-primary">
                    <i class="fas fa-cubes me-1"></i> Revisar Agenda Activa
                </a>
            </div>
        @endif
    </div>
</div>

<script>
// Motor de Búsqueda Reactiva en Tiempo Real
document.addEventListener('DOMContentLoaded', function() {
    const inputBusqueda = document.getElementById('busquedaRapida');
    if (inputBusqueda) {
        inputBusqueda.addEventListener('input', function() {
            const filtro = this.value.toLowerCase();
            const filas = document.querySelectorAll('.alpha-table tbody tr');
            let visibles = 0;
            
            filas.forEach(function(fila) {
                const texto = fila.textContent.toLowerCase();
                if (texto.includes(filtro)) {
                    fila.style.display = '';
                    visibles++;
                } else {
                    fila.style.display = 'none';
                }
            });
            
            // Actualización dinámica del contador premium
            const contador = document.getElementById('contadorTexto');
            if (contador) {
                contador.innerHTML = `Mostrando ${visibles} bloque${visibles != 1 ? 's' : ''} histórico${visibles != 1 ? 's' : ''} ${filtro ? '(filtrados dinámicamente)' : ''}`;
            }
        });
    }
});
</script>
@endsection