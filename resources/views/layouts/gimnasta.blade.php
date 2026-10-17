<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Gimnasta')</title>

    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom Gym Theme -->
    <link rel="stylesheet" href="{{ asset('css/gym-theme.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --alpha-sidebar-bg: #0b192c;
            --alpha-sidebar-text: #e2e8f0;
            --alpha-radius-md: 10px;
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

        
        /* Fondo principal */
        [data-theme="dark"] body,
        [data-theme="dark"] .content-wrapper,
        [data-theme="dark"] .main-footer {
            background-color: var(--bg-color) !important;
            color: var(--text-color) !important;
        }
        
        .navbar-dark {
            background-color: #1f1f1f !important;
        }
        
        [data-theme="dark"] .card {
            background-color: var(--card-bg) !important;
            border-color: var(--border-color) !important;
            color: var(--text-color) !important;
        }
        
        [data-theme="dark"] .card-header {
            background-color: var(--card-bg) !important;
            border-color: var(--border-color) !important;
            color: var(--text-color) !important;
        }
        
        [data-theme="dark"] .table {
            color: var(--text-color) !important;
            background-color: var(--card-bg) !important;
        }
        
        [data-theme="dark"] .table td,
        [data-theme="dark"] .table th {
            border-color: var(--border-color) !important;
        }
        
        [data-theme="dark"] .text-muted {
            color: #999 !important;
        }
        
        [data-theme="dark"] .info-box {
            background-color: var(--card-bg) !important;
            color: var(--text-color) !important;
        }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
<div class="wrapper">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                    <i class="fas fa-bars"></i>
                </a>
            </li>
        </ul>
        
        <!-- Right navbar links -->
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <button class="btn btn-outline-secondary btn-sm mr-2" onclick="toggleTheme()" title="Cambiar tema">
                    <i class="fas fa-moon" id="themeIcon"></i>
                </button>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link btn btn-outline-primary btn-sm" data-toggle="dropdown" href="#" style="border-radius: 20px; padding: 8px 15px;">
                    <i class="fas fa-user"></i> {{ session('usuario_nombre') }}
                    <i class="fas fa-chevron-down ml-1"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow" style="border-radius: 10px;">
                    <a href="{{ route('perfil.show') }}" class="dropdown-item">
                        <i class="fas fa-user mr-2 text-primary"></i> Ver Perfil
                    </a>
                    <div class="dropdown-divider"></div>
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="fas fa-sign-out-alt mr-2"></i> Cerrar Sesión
                        </button>
                    </form>
                </div>
            </li>
        </ul>
    </nav>

    <!-- Sidebar -->
    <aside class="main-sidebar sidebar-light-primary elevation-4">
        <a href="{{ route('gimnasta.dashboard') }}" class="brand-link text-center">
            <i class="fas fa-dumbbell text-warning mr-2"></i>
            <span class="brand-text">Gym Manager</span>
        </a>
        <div class="sidebar">
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column">
                    <li class="nav-header" onclick="toggleSection(this)">
                        <span>DASHBOARD</span>
                        <i class="fas fa-chevron-down nav-header-icon"></i>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('gimnasta.dashboard') }}" class="nav-link {{ request()->is('gimnasta/dashboard') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tachometer-alt text-primary"></i>
                            <p>Panel Principal</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('gimnasta.calendario') }}" class="nav-link {{ request()->is('gimnasta/calendario*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-calendar-alt text-info"></i>
                            <p>Mi Calendario</p>
                        </a>
                    </li>

                    <li class="nav-header" onclick="toggleSection(this)">
                        <span>ENTRENAMIENTO</span>
                        <i class="fas fa-chevron-down nav-header-icon"></i>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('gimnasta.sesiones') }}" class="nav-link {{ request()->is('gimnasta/sesiones*') && !request()->is('gimnasta/historial*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-dumbbell text-success"></i>
                            <p>Mis Sesiones</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('gimnasta.historial') }}" class="nav-link {{ request()->is('gimnasta/historial*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-history text-secondary"></i>
                            <p>Historial</p>
                        </a>
                    </li>

                    <li class="nav-header" onclick="toggleSection(this)">
                        <span>PROGRESO</span>
                        <i class="fas fa-chevron-down nav-header-icon"></i>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('gimnasta.progreso') }}" class="nav-link {{ request()->is('gimnasta/progreso') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-chart-line text-warning"></i>
                            <p>Mi Progreso</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('gimnasta.solicitudes-nivel.index') }}" class="nav-link {{ request()->is('gimnasta/solicitudes-nivel*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-level-up-alt text-success"></i>
                            <p>Mi Nivel</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('gimnasta.entrenador') }}" class="nav-link {{ request()->is('gimnasta/entrenador') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-user-tie text-info"></i>
                            <p>Mi Entrenador</p>
                        </a>
                    </li>

                    <li class="nav-header" onclick="toggleSection(this)">
                        <span>CUENTA</span>
                        <i class="fas fa-chevron-down nav-header-icon"></i>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('perfil.show') }}" class="nav-link {{ request()->is('perfil') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-user text-warning"></i>
                            <p>Mi Perfil</p>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <section class="content pt-3">
            <div class="container-fluid">
                @yield('content')
            </div>
        </section>
    </div>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="float-right d-none d-sm-inline">
            <i class="fas fa-dumbbell text-warning"></i> Gym Manager
        </div>
        <strong>&copy; 2025</strong> Sistema de Gestión Deportiva
    </footer>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<script src="{{ asset('js/csrf-protection.js') }}"></script>
<script src="{{ asset('js/input-sanitizer.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Cargar tema guardado
    const savedTheme = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-theme', savedTheme);
    updateThemeIcon(savedTheme);
    updateNavbarClasses(savedTheme);
});

// Cambiar tema
function toggleTheme() {
    const currentTheme = document.documentElement.getAttribute('data-theme');
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
    
    document.documentElement.setAttribute('data-theme', newTheme);
    localStorage.setItem('theme', newTheme);
    updateThemeIcon(newTheme);
    updateNavbarClasses(newTheme);
}

function updateThemeIcon(theme) {
    const icon = document.getElementById('themeIcon');
    if (icon) {
        icon.className = theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
    }
}

function updateNavbarClasses(theme) {
    const navbar = document.querySelector('.main-header.navbar');
    if (navbar) {
        if (theme === 'dark') {
            navbar.classList.remove('navbar-white', 'navbar-light');
            navbar.classList.add('navbar-dark');
        } else {
            navbar.classList.remove('navbar-dark');
            navbar.classList.add('navbar-white', 'navbar-light');
        }
    }
}

function toggleSection(header) {
    $(header).toggleClass('collapsed');
    $(header).nextUntil('li.nav-header').slideToggle(200);
}
</script>
@yield('scripts')
</body>
</html>
