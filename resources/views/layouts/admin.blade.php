<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'AlphaSigma Admin')</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="{{ asset('css/gym-theme.css') }}">
    
    <style>
        :root {
            --alpha-sidebar-bg: #0b192c;
            --alpha-sidebar-text: #e2e8f0;
            --alpha-radius-md: 10px;
            --alpha-bg-main: #f8fafc;
            --alpha-card-bg: #ffffff;
            --alpha-text-main: #1e293b;
            --alpha-gold: #bfa15f;
            --alpha-gold-glow: rgba(191, 161, 95, 0.15);
            --alpha-blue-light: #1e3e62;
            --alpha-navy-dark: #0b192c;
            --bg-color: var(--alpha-bg-main);
            --card-bg: var(--alpha-card-bg);
            --text-color: var(--alpha-text-main);
            --border-color: #e2e8f0;
            --navbar-bg: #ffffff;
        }

        [data-theme="dark"] {
            --border-color: #1e3e62;
            --navbar-bg: #122239;
        }
        
        body {
            background-color: var(--bg-color);
            color: var(--text-color);
        }

        body, .content-wrapper, .main-footer {
            background-color: var(--bg-color) !important;
            color: var(--text-color) !important;
            transition: var(--alpha-transition);
        }

        .main-header.navbar {
            background-color: var(--navbar-bg) !important;
            border-bottom: 1px solid var(--border-color) !important;
            transition: var(--alpha-transition);
        }

        .main-header.navbar .nav-link {
            color: var(--text-color) !important;
        }

        /* Botón de Perfil Ejecutivo */
        .btn-profile-dropdown {
            border: 1px solid var(--alpha-gold) !important;
            background: transparent !important;
            color: var(--text-color) !important;
            font-weight: 600;
            font-size: 0.8rem;
            letter-spacing: 0.3px;
            border-radius: 30px;
            padding: 6px 16px !important;
            transition: var(--alpha-transition);
        }
        .btn-profile-dropdown:hover {
            background: var(--alpha-gold) !important;
            color: white !important;
            box-shadow: 0 4px 10px rgba(191, 161, 95, 0.25);
        }

        /* ========================================================
           DISEÑO PREMIUM DE LA BARRA LATERAL (SIDEBAR ALPHA)
        ======================================================== */
        .main-sidebar {
            background-color: var(--alpha-sidebar-bg) !important;
            border-right: 1px solid rgba(191, 161, 95, 0.15) !important;
            box-shadow: 4px 0 25px rgba(0, 0, 0, 0.15) !important;
        }

        /* Cabecera del Panel (Brand Link) */
        .brand-link {
            background-color: var(--alpha-sidebar-bg) !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
            padding: 18px 15px !important;
            transition: var(--alpha-transition);
        }
        .brand-text {
            font-weight: 800 !important;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            font-size: 1.05rem !important;
            background: linear-gradient(135deg, #ffffff 0%, var(--alpha-gold) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Encabezados de Sección (NAV HEADERS) */
        .sidebar .nav-header {
            color: var(--alpha-gold) !important;
            font-family: monospace;
            font-weight: 700 !important;
            font-size: 0.72rem !important;
            letter-spacing: 1.5px;
            padding: 14px 16px 6px 16px !important;
            opacity: 0.85;
            cursor: pointer;
            user-select: none;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .sidebar .nav-header .nav-header-icon {
            font-size: 0.6rem;
            opacity: 0.6;
            transition: transform 0.3s ease;
        }
        .sidebar .nav-header.collapsed .nav-header-icon {
            transform: rotate(-90deg);
        }

        /* Enlaces de Navegación Estándar */
        .sidebar .nav-sidebar .nav-item .nav-link {
            color: var(--alpha-sidebar-text) !important;
            border-radius: var(--alpha-radius-md);
            margin: 2px 10px !important;
            padding: 10px 14px !important;
            font-size: 0.85rem;
            font-weight: 500;
            transition: var(--alpha-transition);
            border: 1px solid transparent;
        }

        /* Efecto Hover Inteligente */
        .sidebar .nav-sidebar .nav-item .nav-link:hover {
            background-color: rgba(30, 62, 98, 0.4) !important;
            color: #ffffff !important;
            transform: translateX(4px);
            border-left: 3px solid var(--alpha-gold) !important;
        }

        /* Estado Activo / Módulo Seleccionado */
        .sidebar .nav-sidebar .nav-item .nav-link.active {
            background: linear-gradient(90deg, var(--alpha-blue-light) 0%, rgba(30, 62, 98, 0.6) 100%) !important;
            color: #ffffff !important;
            border: 1px solid rgba(191, 161, 95, 0.3) !important;
            border-left: 4px solid var(--alpha-gold) !important;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2) !important;
            font-weight: 700;
        }

        /* Iconos de la barra lateral */
        .sidebar .nav-sidebar .nav-item .nav-link .nav-icon {
            margin-right: 10px !important;
            font-size: 1rem;
            transition: var(--alpha-transition);
            /* Ignoramos los colores inline antiguos forzando una armonía dorada/blanca */
            color: var(--alpha-gold) !important; 
        }
        .sidebar .nav-sidebar .nav-item .nav-link.active .nav-icon {
            color: #ffffff !important;
            text-shadow: 0 0 8px var(--alpha-gold);
        }

        /* Modificadores de drop-down internos de cuenta */
        .dropdown-menu {
            background-color: var(--card-bg) !important;
            border: 1px solid var(--border-color) !important;
        }
        .dropdown-item {
            color: var(--text-color) !important;
            font-size: 0.85rem;
            font-weight: 500;
            transition: var(--alpha-transition);
        }
        .dropdown-item:hover {
            background-color: var(--alpha-gold-glow) !important;
        }

        .btn-xs {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            line-height: 1.2;
            border-radius: 0.2rem;
        }

        /* ========================================================
           ALPHA HERO PANEL - shared component
        ======================================================== */
        .alpha-hero-panel {
            background: linear-gradient(135deg, #0b192c 0%, #1e3e62 100%);
            border-radius: 16px;
            border-top: 5px solid #bfa15f;
            position: relative;
            overflow: hidden;
        }
        .hero-overlay-grid {
            position: absolute; inset: 0;
            background-image: radial-gradient(rgba(191, 161, 95, 0.1) 1px, transparent 0);
            background-size: 24px 24px; z-index: 1;
        }
        .hero-welcome { font-weight: 800; font-size: 1.8rem; letter-spacing: -0.5px; color: #fff !important; }
        .hero-subtitle { color: #ffffff !important; }
        .alpha-hero-panel .text-gold,
        .hero-welcome .text-gold { color: #bfa15f !important; }
        .badge-session-active { background: rgba(16, 185, 129, 0.15); color: #10b981; padding: 6px 12px; border-radius: 30px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; display: inline-flex; align-items: center; gap: 6px; }
        .pulse-dot { width: 6px; height: 6px; background-color: #10b981; border-radius: 50%; display: inline-block; }

        /* ========================================================
           MODO OSCURO - Correcciones de contraste
        ======================================================== */
        [data-theme="dark"] {
            --alpha-bg-main: #07101c;
            --alpha-card-bg: #122239;
            --alpha-text-main: #f1f5f9;
        }
        [data-theme="dark"] h1,
        [data-theme="dark"] h2,
        [data-theme="dark"] h3,
        [data-theme="dark"] h4,
        [data-theme="dark"] h5,
        [data-theme="dark"] h6,
        [data-theme="dark"] .card-title,
        [data-theme="dark"] .content-header h1,
        [data-theme="dark"] .card-header h3,
        [data-theme="dark"] .card-header h4 {
            color: #fff !important;
        }
        [data-theme="dark"] .text-dark { color: #fff !important; }
        [data-theme="dark"] li, [data-theme="dark"] p, [data-theme="dark"] td {
            color: var(--alpha-text-main, #e0e0e0) !important;
        }
        [data-theme="dark"] .small-box .inner h3,
        [data-theme="dark"] .small-box .inner p,
        [data-theme="dark"] .info-box .info-box-text,
        [data-theme="dark"] .info-box .info-box-number {
            color: #fff !important;
        }
        [data-theme="dark"] .modal-title,
        [data-theme="dark"] .modal-body { color: #fff !important; }
        [data-theme="dark"] a:not(.btn):not(.nav-link):not(.dropdown-item) {
            color: #6ea8fe !important;
        }
    </style>
    @stack('styles')
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
<div class="wrapper">

    <nav class="main-header navbar navbar-expand">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                    <i class="fas fa-bars"></i>
                </a>
            </li>
        </ul>
        
        <ul class="navbar-nav ml-auto align-items-center">
            <li class="nav-item mr-2">
                <button class="btn btn-outline-secondary btn-sm" onclick="toggleTheme()" title="Cambiar tema" style="border-radius: 50%; width: 34px; height: 34px; padding: 0; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-moon" id="themeIcon"></i>
                </button>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link btn-profile-dropdown d-flex align-items-center gap-2" data-toggle="dropdown" href="#">
                    <i class="fas fa-user-shield mr-1"></i> <span>{{ session('usuario_nombre', 'Boris') }}</span>
                    <i class="fas fa-chevron-down ml-2" style="font-size: 0.7rem;"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow menu-profile-box" style="border-radius: 12px; overflow: hidden; margin-top: 10px;">
                    <a href="{{ route('perfil.show') }}" class="dropdown-item py-2">
                        <i class="fas fa-user mr-2 text-muted"></i> Ver Perfil
                    </a>
                    <div class="dropdown-divider" style="margin: 0; border-color: var(--border-color);"></div>
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="dropdown-item py-2 text-danger" style="font-weight: 600;">
                            <i class="fas fa-sign-out-alt mr-2"></i> Cerrar Sesión
                        </button>
                    </form>
                </div>
            </li>
        </ul>
    </nav>

    <aside class="main-sidebar elevation-4">
        <a href="{{ route('dashboard') }}" class="brand-link text-center">
            <span class="brand-text">AlphaSigma</span>
        </a>
        <div class="sidebar px-0">
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    
                    <li class="nav-header" onclick="toggleSection(this)">
                        <span>DASHBOARD</span>
                        <i class="fas fa-chevron-down nav-header-icon"></i>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.inicio') }}" class="nav-link {{ request()->is('admin/inicio') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Panel Principal</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.historial') }}" class="nav-link {{ request()->is('admin/historial*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-history"></i>
                            <p>Historial</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.audit') }}" class="nav-link {{ request()->is('admin/audit*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-shield-alt"></i>
                            <p>Logs de Auditoría</p>
                        </a>
                    </li>

                    <li class="nav-header" onclick="toggleSection(this)">
                        <span>USUARIOS</span>
                        <i class="fas fa-chevron-down nav-header-icon"></i>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('postulaciones.index') }}" class="nav-link {{ request()->is('postulaciones*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-user-clock"></i>
                            <p>Postulaciones</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.solicitudes-nivel.index') }}" class="nav-link {{ request()->is('admin/solicitudes-nivel*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tasks"></i>
                            <p>Solicitudes de Nivel</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('usuarios-unificado.index') }}" class="nav-link {{ request()->is('usuarios-unificado*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Gestión Usuarios</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('roles.index') }}" class="nav-link {{ request()->is('roles*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-user-tag"></i>
                            <p>Roles y Permisos</p>
                        </a>
                    </li>

                    <li class="nav-header" onclick="toggleSection(this)">
                        <span>INFRAESTRUCTURA</span>
                        <i class="fas fa-chevron-down nav-header-icon"></i>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('aparatos.index') }}" class="nav-link {{ request()->is('aparatos*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-vector-square"></i>
                            <p>Aparatos FIG</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('sesiones.index') }}" class="nav-link {{ request()->is('sesiones*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-calendar-check"></i>
                            <p>Sesiones</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('entrenamientos.index') }}" class="nav-link {{ request()->is('entrenamientos*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-stopwatch"></i>
                            <p>Entrenamientos</p>
                        </a>
                    </li>

                    <li class="nav-header" onclick="toggleSection(this)">
                        <span>EVALUACIÓN DEPORTIVA</span>
                        <i class="fas fa-chevron-down nav-header-icon"></i>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('juez.dashboard') }}" class="nav-link {{ request()->is('juez/dashboard') || request()->is('juez/evaluar*') || request()->is('juez/crear*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-gavel"></i>
                            <p>Panel del Juez</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('juez.crear') }}" class="nav-link {{ request()->is('juez/crear*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-plus-circle"></i>
                            <p>Nueva Evaluación</p>
                        </a>
                    </li>

                    <li class="nav-header" onclick="toggleSection(this)">
                        <span>EVENTOS Y SALUD</span>
                        <i class="fas fa-chevron-down nav-header-icon"></i>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('competencias.index') }}" class="nav-link {{ request()->is('competencias*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-trophy"></i>
                            <p>Competencias</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('lesiones.index') }}" class="nav-link {{ request()->is('lesiones*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-heartbeat"></i>
                            <p>Lesiones</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('areamedica.index') }}" class="nav-link {{ request()->is('areamedica*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-stethoscope"></i>
                            <p>Área Médica</p>
                        </a>
                    </li>

                    <li class="nav-header" onclick="toggleSection(this)">
                        <span>MINERÍA DE DATOS E IA</span>
                        <i class="fas fa-chevron-down nav-header-icon"></i>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('mineria.index') }}" class="nav-link {{ request()->is('mineria*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-database"></i>
                            <p>Minería de Datos</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('mineria.clustering') }}" class="nav-link {{ request()->is('mineria/clustering*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-layer-group"></i>
                            <p>Segmentación</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('mineria.patrones') }}" class="nav-link {{ request()->is('mineria/patrones*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-heartbeat"></i>
                            <p>Patrones Lesiones</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('mineria.correlaciones') }}" class="nav-link {{ request()->is('mineria/correlaciones*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-chart-scatter"></i>
                            <p>Correlaciones</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('mineria.prediccion') }}" class="nav-link {{ request()->is('mineria/prediccion*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-brain"></i>
                            <p>Predicción IA</p>
                        </a>
                    </li>

                    <li class="nav-header" onclick="toggleSection(this)">
                        <span>ANÁLISIS DE MOVIMIENTO</span>
                        <i class="fas fa-chevron-down nav-header-icon"></i>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('analisis.movimiento-dashboard') }}" class="nav-link {{ request()->is('analisis/movimiento-dashboard*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-chart-line"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('analisis.index') }}" class="nav-link {{ request()->is('analisis') && !request()->is('analisis/*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-video"></i>
                            <p>Mis Videos</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('analisis.video') }}" class="nav-link {{ request()->is('analisis/video*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-upload"></i>
                            <p>Subir Video</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('analisis.angulo') }}" class="nav-link {{ request()->is('analisis/angulo*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-angle-double-right"></i>
                            <p>Ángulos</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('analisis.estadisticas') }}" class="nav-link {{ request()->is('analisis/estadisticas*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-chart-bar"></i>
                            <p>Estadísticas</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('analisis.aptitud') }}" class="nav-link {{ request()->is('analisis/aptitud*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-heartbeat"></i>
                            <p>Aptitud Médica</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('analisis.nutricional') }}" class="nav-link {{ request()->is('analisis/nutricional*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-apple-alt"></i>
                            <p>Nutricional</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('analisis.respiratoria') }}" class="nav-link {{ request()->is('analisis/respiratoria*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-lungs"></i>
                            <p>Respiratoria</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('analisis.psicologica') }}" class="nav-link {{ request()->is('analisis/psicologica*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-brain"></i>
                            <p>Psicológica</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('analisis.fisiologica') }}" class="nav-link {{ request()->is('analisis/fisiologica*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-dna"></i>
                            <p>Fisiológica</p>
                        </a>
                    </li>

                    <li class="nav-header" onclick="toggleSection(this)">
                        <span>REPORTES</span>
                        <i class="fas fa-chevron-down nav-header-icon"></i>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.reportes.index') }}" class="nav-link {{ request()->is('admin/reportes') && !request()->is('admin/reportes/*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-chart-bar"></i>
                            <p>Estadísticas</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.reportes.completo') }}" class="nav-link {{ request()->is('admin/reportes/completo') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-file-alt"></i>
                            <p>Reportes Detallados</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.reportes.backup') }}" class="nav-link {{ request()->is('admin/reportes/backup') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-database"></i>
                            <p>Backups</p>
                        </a>
                    </li>

                    <li class="nav-header" onclick="toggleSection(this)">
                        <span>CUENTA</span>
                        <i class="fas fa-chevron-down nav-header-icon"></i>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('perfil.show') }}" class="nav-link {{ request()->is('perfil') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-user"></i>
                            <p>Mi Perfil</p>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>

    <div class="content-wrapper">
        <section class="content pt-3">
            <div class="container-fluid">
                @yield('content')
            </div>
        </section>
    </div>

    <footer class="main-footer text-xs">
        <div class="float-right d-none d-sm-inline font-monospace" style="color: var(--alpha-gold)">
            AlphaSigma System v1.0
        </div>
        <strong>&copy; {{ now()->format('Y') }}</strong> Sistema de Gestión Gimnástica de Alto Rendimiento
    </footer>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<script src="{{ asset('js/csrf-protection.js') }}"></script>
<script src="{{ asset('js/input-sanitizer.js') }}"></script>
<script src="{{ asset('js/crud-modals.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const savedTheme = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-theme', savedTheme);
    updateThemeIcon(savedTheme);
});

function toggleTheme() {
    const currentTheme = document.documentElement.getAttribute('data-theme');
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
    
    document.documentElement.setAttribute('data-theme', newTheme);
    localStorage.setItem('theme', newTheme);
    updateThemeIcon(newTheme);
}

function updateThemeIcon(theme) {
    const icon = document.getElementById('themeIcon');
    if (icon) {
        icon.className = theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
    }
}

function toggleSection(header) {
    $(header).toggleClass('collapsed');
    $(header).nextUntil('li.nav-header').slideToggle(200);
}
</script>
@stack('scripts')
@yield('scripts')
</body>
</html>