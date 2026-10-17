@extends('layouts.admin')

@section('title', 'Análisis de Movimiento')

@section('content')
<div class="container-fluid py-4">
    <div class="alpha-hero-panel mb-4">
        <div class="hero-overlay-grid"></div>
        <div class="p-4 position-relative" style="z-index: 2;">
            <h1 class="hero-welcome"><i class="fas fa-robot text-gold me-2"></i>Análisis de Movimiento</h1>
            <p class="hero-subtitle mb-0">Sistema de análisis biomecánico con IA, MediaPipe y evaluación médica</p>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-4 col-md-6">
            <a href="{{ route('analisis.video') }}" class="text-decoration-none">
                <div class="alpha-module-card h-100">
                    <div class="module-glow"></div>
                    <div class="card-body-content p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="module-icon-box"><i class="fas fa-video"></i></div>
                            <h5 class="module-title mb-0 ms-3">Análisis de Video</h5>
                        </div>
                        <p class="module-text mb-0">Sube videos, marca puntos clave, calcula ángulos articulares y obtén análisis con IA.</p>
                        <div class="module-footer-arrow mt-4 d-flex align-items-center text-uppercase font-monospace">
                            <span>Ingresar</span><i class="fas fa-chevron-right ms-2 transition-arrow"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-4 col-md-6">
            <a href="{{ route('analisis.angulo') }}" class="text-decoration-none">
                <div class="alpha-module-card h-100">
                    <div class="module-glow"></div>
                    <div class="card-body-content p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="module-icon-box"><i class="fas fa-drafting-compass"></i></div>
                            <h5 class="module-title mb-0 ms-3">Analizador de Ángulos</h5>
                        </div>
                        <p class="module-text mb-0">Herramienta interactiva para análisis de ángulos biomecánicos en video en tiempo real.</p>
                        <div class="module-footer-arrow mt-4 d-flex align-items-center text-uppercase font-monospace">
                            <span>Ingresar</span><i class="fas fa-chevron-right ms-2 transition-arrow"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-4 col-md-6">
            <a href="{{ route('analisis.estadisticas') }}" class="text-decoration-none">
                <div class="alpha-module-card h-100">
                    <div class="module-glow"></div>
                    <div class="card-body-content p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="module-icon-box"><i class="fas fa-chart-bar"></i></div>
                            <h5 class="module-title mb-0 ms-3">Estadísticas Deportivas</h5>
                        </div>
                        <p class="module-text mb-0">Dashboard de estadísticas de rendimiento, evaluaciones médicas y progreso de atletas.</p>
                        <div class="module-footer-arrow mt-4 d-flex align-items-center text-uppercase font-monospace">
                            <span>Ingresar</span><i class="fas fa-chevron-right ms-2 transition-arrow"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <h6 class="text-uppercase font-monospace text-muted mb-3 fs-7 letter-spacing-1">Evaluaciones Médicas</h6>
    <div class="row g-4">
        @foreach([
            ['route' => 'analisis.aptitud', 'icon' => 'fa-heartbeat', 'title' => 'Aptitud Médica', 'desc' => 'Certificaciones médicas y restricciones de entrenamiento.', 'color' => 'danger'],
            ['route' => 'analisis.nutricional', 'icon' => 'fa-apple-alt', 'title' => 'Evaluación Nutricional', 'desc' => 'IMC, porcentaje graso, masa muscular e hidratación.', 'color' => 'success'],
            ['route' => 'analisis.respiratoria', 'icon' => 'fa-lungs', 'title' => 'Evaluación Respiratoria', 'desc' => 'Capacidad pulmonar, saturación O2 y diagnóstico.', 'color' => 'info'],
            ['route' => 'analisis.psicologica', 'icon' => 'fa-brain', 'title' => 'Evaluación Psicológica', 'desc' => 'Nivel de ansiedad, estrés, motivación y sueño.', 'color' => 'warning'],
            ['route' => 'analisis.fisiologica', 'icon' => 'fa-heart-pulse', 'title' => 'Evaluación Fisiológica', 'desc' => 'FC reposo/esfuerzo, presión arterial y saturación.', 'color' => 'primary'],
        ] as $mod)
        <div class="col-xl-2 col-md-4 col-6">
            <a href="{{ route($mod['route']) }}" class="text-decoration-none">
                <div class="alpha-module-card h-100 text-center p-3">
                    <div class="module-icon-box mx-auto mb-2" style="width:50px;height:50px;">
                        <i class="fas {{ $mod['icon'] }}"></i>
                    </div>
                    <h6 class="module-title mb-1" style="font-size:0.8rem;">{{ $mod['title'] }}</h6>
                    <p class="module-text mb-0" style="font-size:0.7rem;">{{ $mod['desc'] }}</p>
                </div>
            </a>
        </div>
        @endforeach
    </div>
</div>
@endsection
