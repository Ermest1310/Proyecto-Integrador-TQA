@extends('layouts.admin')

@section('title', 'Inicio - Panel de Control')

@section('content')
<div class="container-fluid py-4 alpha-dashboard-wrapper">

    <style>
        .card-glass-stats { background: rgba(255, 255, 255, 0.06); backdrop-filter: blur(8px); padding: 12px 20px; border-radius: 12px; border: 1px solid rgba(255, 255, 255, 0.1); }
        .card-glass-stats .text-white-50 { color: rgba(255, 255, 255, 0.7) !important; }
        .card-glass-stats .text-white { color: #fff !important; }
        .card-glass-stats .text-gold { color: #bfa15f !important; }
    </style>

    <div class="row mb-4 animate-up">
        <div class="col-12">
            <div class="alpha-hero-panel">
                <div class="hero-overlay-grid"></div>
                <div class="p-4 d-flex align-items-center justify-content-between flex-wrap position-relative" style="z-index: 2;">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge-session-active"><span class="pulse-dot"></span>Estación Biomédica Conectada</span>
                        </div>
                        <h1 class="hero-welcome">Bienvenido de nuevo, <span class="text-gold">{{ Auth::user()->Nombre ?? 'Boris' }}</span></h1>
                        <p class="hero-subtitle mb-0">AlphaSigma Gymnastics Analytics Engine</p>
                    </div>
                    <div class="text-md-end mt-3 mt-md-0 card-glass-stats">
                        <span class="d-block font-monospace text-uppercase text-white-50 small">Pasarela Operativa</span>
                        <h4 class="text-white fw-bold mb-0" id="liveClock">{{ now()->format('H:i:s') }}</h4>
                        <small class="text-gold font-monospace">{{ now()->format('d . M . Y') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4 animate-up delay-1">
        <div class="col-xl-3 col-sm-6">
            <div class="alpha-kpi-card" style="border-left: 4px solid var(--alpha-navy-dark);">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="alpha-kpi-title">Usuarios Activos</span>
                        <h3 class="alpha-kpi-value my-1">{{ $estadisticas['total_usuarios'] ?? 0 }}</h3>
                        <span class="alpha-kpi-desc">Personal autenticado en la plataforma</span>
                    </div>
                    <div class="alpha-kpi-icon-wrapper" style="background-color: rgb(255, 255, 255); color: var(--alpha-navy-dark);">
                        <i class="fas fa-users-shield fa-xl"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="alpha-kpi-card" style="border-left: 4px solid var(--alpha-gold);">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="alpha-kpi-title">Gimnastas Élite</span>
                        <h3 class="alpha-kpi-value my-1">{{ $estadisticas['total_gimnastas'] ?? 0 }}</h3>
                        <span class="alpha-kpi-desc">Atletas bajo control biomecánico</span>
                    </div>
                    <div class="alpha-kpi-icon-wrapper" style="background-color: rgba(191,161,95,0.1); color: var(--alpha-gold);">
                        <i class="fas fa-medal fa-xl"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="alpha-kpi-card" style="border-left: 4px solid var(--alpha-blue-light);">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="alpha-kpi-title">Sesiones del Día</span>
                        <h3 class="alpha-kpi-value my-1">{{ $estadisticas['sesiones_hoy'] ?? 0 }}</h3>
                        <span class="alpha-kpi-desc">Bloques cronometrados agendados hoy</span>
                    </div>
                    <div class="alpha-kpi-icon-wrapper" style="background-color: rgba(30,62,98,0.1); color: var(--alpha-blue-light);">
                        <i class="fas fa-stopwatch fa-xl"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="alpha-kpi-card" style="border-left: 4px solid var(--alpha-danger);">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="alpha-kpi-title">Nuevos Registros</span>
                        <h3 class="alpha-kpi-value my-1">{{ $estadisticas['postulaciones_pendientes'] ?? 0 }}</h3>
                        <span class="alpha-kpi-desc">Cuentas pendientes de auditoría</span>
                    </div>
                    <div class="alpha-kpi-icon-wrapper" style="background-color: rgba(239,68,68,0.1); color: var(--alpha-danger);">
                        <i class="fas fa-user-plus fa-xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4 g-4 animate-up delay-2">
        <div class="col-lg-8">
            <div class="alpha-card-chart">
                <div class="card-chart-header d-flex justify-content-between align-items-center p-4 border-bottom border-light">
                    <div>
                        <h5 class="fw-bold text-navy mb-1"><i class="fas fa-chart-line me-2 text-gold"></i>Carga Cinemática Semanal</h5>
                        <p class="text-muted small mb-0">Volumen e intensidad media de entrenamientos por aparato de gimnasia</p>
                    </div>
                    <span class="badge bg-light text-dark font-monospace">Unidad: Puntos FIG</span>
                </div>
                <div class="p-4 bg-white rounded-bottom-lg">
                    <div id="analyticsMainChart"></div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="alpha-card-actions h-100 d-flex flex-column justify-content-between p-4">
                <div>
                    <h5 class="fw-bold text-white mb-3"><i class="fas fa-bolt text-gold me-2"></i>Acciones de Despliegue</h5>
                    <p class="text-white-50 small mb-4">Lanzamiento inmediato de operaciones reglamentarias del sistema.</p>
                </div>
                <div class="d-grid gap-2">
                    <a href="/sesiones/create" class="btn btn-alpha-action-primary">
                        <i class="fas fa-plus-circle"></i> Nueva Sesión de Entrenamiento
                    </a>
                    <a href="/usuarios-unificado/create" class="btn btn-alpha-action-secondary">
                        <i class="fas fa-user-plus"></i> Dar de Alta Nuevo Personal
                    </a>
                    <a href="/postulaciones" class="btn btn-alpha-action-secondary">
                        <i class="fas fa-user-check"></i> Auditar Registros Nuevos
                    </a>
                    <a href="#" onclick="window.print(); return false;" class="btn btn-alpha-action-gold">
                        <i class="fas fa-print"></i> Exportar Métricas de Rendimiento
                    </a>
                </div>
            </div>
        </div>
    </div>

    <h6 class="text-uppercase font-monospace text-muted mb-3 fs-7 letter-spacing-1 animate-up delay-2">Módulos del Ecosistema Deportivo</h6>
    <div class="row mb-4">
        <a href="/analisis/estadisticas" class="text-decoration-none col-xl-4 col-md-6 mb-4 animate-up">
            <div class="alpha-module-card h-100">
                <div class="module-glow"></div>
                <div class="card-body-content p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="module-icon-box"><i class="fas fa-chart-pie"></i></div>
                        <h5 class="module-title mb-0 ms-3">Métricas & Analíticas</h5>
                    </div>
                    <p class="module-text mb-0">Dashboard analítico avanzado con el control de rendimiento técnico y valoraciones mecánicas.</p>
                    <div class="module-footer-arrow mt-4 d-flex align-items-center text-uppercase font-monospace">
                        <span>Ingresar Módulo</span><i class="fas fa-chevron-right ms-2 transition-arrow"></i>
                    </div>
                </div>
            </div>
        </a>

        <a href="/usuarios-unificado" class="text-decoration-none col-xl-4 col-md-6 mb-4 animate-up">
            <div class="alpha-module-card h-100">
                <div class="module-glow"></div>
                <div class="card-body-content p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="module-icon-box"><i class="fas fa-user-shield"></i></div>
                        <h5 class="module-title mb-0 ms-3">Gestión de Personal</h5>
                    </div>
                    <p class="module-text mb-0">Control integral de perfiles federados, atletas élite, entrenadores y personal médico clínico.</p>
                    <div class="module-footer-arrow mt-4 d-flex align-items-center text-uppercase font-monospace">
                        <span>Ingresar Módulo</span><i class="fas fa-chevron-right ms-2 transition-arrow"></i>
                    </div>
                </div>
            </div>
        </a>

        <a href="/sesiones" class="text-decoration-none col-xl-4 col-md-6 mb-4 animate-up">
            <div class="alpha-module-card h-100">
                <div class="module-glow"></div>
                <div class="card-body-content p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="module-icon-box"><i class="fas fa-calendar-check"></i></div>
                        <h5 class="module-title mb-0 ms-3">Planificación Horaria</h5>
                    </div>
                    <p class="module-text mb-0">Administración de bloques cronológicos de entrenamiento, agendas y reservas físicas.</p>
                    <div class="module-footer-arrow mt-4 d-flex align-items-center text-uppercase font-monospace">
                        <span>Ingresar Módulo</span><i class="fas fa-chevron-right ms-2 transition-arrow"></i>
                    </div>
                </div>
            </div>
        </a>

        <a href="/aparatos" class="text-decoration-none col-xl-4 col-md-6 mb-4 animate-up">
            <div class="alpha-module-card h-100">
                <div class="module-glow"></div>
                <div class="card-body-content p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="module-icon-box"><i class="fas fa-vector-square"></i></div>
                        <h5 class="module-title mb-0 ms-3">Aparatos FIG</h5>
                    </div>
                    <p class="module-text mb-0">Catálogo e infraestructura reglamentaria de aparatos olímpicos y control de asignaciones.</p>
                    <div class="module-footer-arrow mt-4 d-flex align-items-center text-uppercase font-monospace">
                        <span>Ingresar Módulo</span><i class="fas fa-chevron-right ms-2 transition-arrow"></i>
                    </div>
                </div>
            </div>
        </a>

        <a href="/lesiones" class="text-decoration-none col-xl-4 col-md-6 mb-4 animate-up">
            <div class="alpha-module-card h-100">
                <div class="module-glow"></div>
                <div class="card-body-content p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="module-icon-box"><i class="fas fa-heartbeat"></i></div>
                        <h5 class="module-title mb-0 ms-3">Seguimiento Sanitario</h5>
                    </div>
                    <p class="module-text mb-0">Expedientes médicos de rehabilitación, fisioterapia deportiva y altas de lesiones.</p>
                    <div class="module-footer-arrow mt-4 d-flex align-items-center text-uppercase font-monospace">
                        <span>Ingresar Módulo</span><i class="fas fa-chevron-right ms-2 transition-arrow"></i>
                    </div>
                </div>
            </div>
        </a>

        <a href="/postulaciones" class="text-decoration-none col-xl-4 col-md-6 mb-4 animate-up">
            <div class="alpha-module-card h-100">
                <div class="module-glow"></div>
                <div class="card-body-content p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="module-icon-box"><i class="fas fa-clipboard-list"></i></div>
                        <h5 class="module-title mb-0 ms-3">Validación de Cuentas</h5>
                    </div>
                    <p class="module-text mb-0">Auditoría, aprobación o rechazo de solicitudes de registro de gimnastas.</p>
                    <div class="module-footer-arrow mt-4 d-flex align-items-center text-uppercase font-monospace">
                        <span>Ingresar Módulo</span><i class="fas fa-chevron-right ms-2 transition-arrow"></i>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="row g-4 animate-up delay-3">
        <div class="col-md-7">
            <div class="alpha-glass-list-card h-100 p-4">
                <h5 class="fw-bold mb-4 text-navy"><i class="fas fa-history text-muted me-2"></i>Log de Actividad Reciente</h5>
                <div class="alpha-timeline">
                    <div class="timeline-item">
                        <div class="timeline-dot bg-success"></div>
                        <div class="timeline-content">
                            <span class="time font-monospace">Hace 2 min</span>
                            <strong class="text-dark">Atleta Registrado Directamente</strong>
                            <p class="mb-0 text-muted small">Nuevo perfil de gimnasta asignado al nivel "Intermedio".</p>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-danger"></div>
                        <div class="timeline-content">
                            <span class="time font-monospace">Hace 15 min</span>
                            <strong class="text-dark">Alerta de Lesión Modificada</strong>
                            <p class="mb-0 text-muted small">Fisioterapeuta actualizó el parte médico de Esguince de tobillo.</p>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-warning"></div>
                        <div class="timeline-content">
                            <span class="time font-monospace">Hace 1 hora</span>
                            <strong class="text-dark">Sesión Cronometrada Completada</strong>
                            <p class="mb-0 text-muted small">Rutina completada en módulo de Anillas Olímpicas.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="alpha-glass-list-card h-100 p-4">
                <h5 class="fw-bold mb-4 text-navy"><i class="fas fa-shield-virus text-danger me-2"></i>Monitoreo de Alertas Críticas</h5>
                <div class="d-flex flex-column gap-3">
                    <div class="alert-medical-strip alert-type-danger">
                        <div class="d-flex align-items-center gap-3">
                            <i class="fas fa-heartbeat fa-lg text-danger"></i>
                            <div>
                                <h6 class="mb-0 fw-bold text-dark">Gimnasta con Lesión Activa</h6>
                                <p class="mb-0 text-muted style-xs">Exclusión médica en aparatos de salto de potro.</p>
                            </div>
                        </div>
                    </div>
                    <div class="alert-medical-strip alert-type-warning">
                        <div class="d-flex align-items-center gap-3">
                            <i class="fas fa-exclamation-triangle fa-lg text-warning"></i>
                            <div>
                                <h6 class="mb-0 fw-bold text-dark">Sesión Pendiente por Aparato</h6>
                                <p class="mb-0 text-muted style-xs">Falta asignación de juez supervisor en barras paralelas.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Reloj Operativo en Vivo
        setInterval(() => {
            const time = new Date().toLocaleTimeString('es-ES', { hour12: false });
            const liveClockElement = document.getElementById('liveClock');
            if(liveClockElement) liveClockElement.textContent = time;
        }, 1000);

        // Configuración Cinemática del Gráfico
        var options = {
            series: [{ name: 'Intensidad Mecánica', data: [31, 40, 28, 51, 42, 109, 100] }, 
                     { name: 'Volumen de Carga (Minutos)', data: [11, 32, 45, 32, 34, 52, 41] }],
            chart: { height: 320, type: 'area', toolbar: { show: false }, fontFamily: 'Montserrat, sans-serif' },
            colors: ['#bfa15f', '#1e3e62'],
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 3 },
            xaxis: { categories: ["Lun", "Mar", "Mié", "Jue", "Vie", "Sáb", "Dom"], axisBorder: { show: false }, axisTicks: { show: false } },
            grid: { borderColor: '#f1f5f9', strokeDashArray: 4 },
            fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05 } },
            tooltip: { theme: 'light' }
        };

        var chart = new ApexCharts(document.querySelector("#analyticsMainChart"), options);
        chart.render();
    });
</script>

@endsection