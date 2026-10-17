@extends('layouts.admin')

@section('title', 'Dashboard de Análisis')

@section('content')
<div class="container-fluid py-4">
    <div class="alpha-hero-panel mb-4">
        <div class="hero-overlay-grid"></div>
        <div class="p-4 position-relative" style="z-index:2;">
            <h1 class="hero-welcome"><i class="fas fa-chart-line text-gold me-2"></i>Dashboard de Análisis de Movimiento</h1>
            <p class="hero-subtitle mb-0">Resumen de evaluaciones y rendimiento de atletas</p>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-sm-6">
            <div class="alpha-kpi-card" style="border-left:4px solid var(--alpha-navy-dark);">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="alpha-kpi-title">Gimnastas</span>
                        <h3 class="alpha-kpi-value my-1">{{ $gimnastasCount }}</h3>
                        <span class="alpha-kpi-desc">Atletas registrados</span>
                    </div>
                    <div class="alpha-kpi-icon-wrapper" style="background:rgba(11,25,44,0.1);color:var(--alpha-navy-dark);">
                        <i class="fas fa-users fa-xl"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="alpha-kpi-card" style="border-left:4px solid var(--alpha-gold);">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="alpha-kpi-title">Nutricionales</span>
                        <h3 class="alpha-kpi-value my-1">{{ $evalNutricional }}</h3>
                        <span class="alpha-kpi-desc">Evaluaciones realizadas</span>
                    </div>
                    <div class="alpha-kpi-icon-wrapper" style="background:rgba(191,161,95,0.1);color:var(--alpha-gold);">
                        <i class="fas fa-apple-alt fa-xl"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="alpha-kpi-card" style="border-left:4px solid var(--alpha-blue-light);">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="alpha-kpi-title">Fisiológicas</span>
                        <h3 class="alpha-kpi-value my-1">{{ $evalFisiologica }}</h3>
                        <span class="alpha-kpi-desc">Evaluaciones cardiovasculares</span>
                    </div>
                    <div class="alpha-kpi-icon-wrapper" style="background:rgba(30,62,98,0.1);color:var(--alpha-blue-light);">
                        <i class="fas fa-heart-pulse fa-xl"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="alpha-kpi-card" style="border-left:4px solid var(--alpha-danger);">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="alpha-kpi-title">Psicológicas</span>
                        <h3 class="alpha-kpi-value my-1">{{ $evalPsicologica }}</h3>
                        <span class="alpha-kpi-desc">Evaluaciones mentales</span>
                    </div>
                    <div class="alpha-kpi-icon-wrapper" style="background:rgba(239,68,68,0.1);color:var(--alpha-danger);">
                        <i class="fas fa-brain fa-xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        @foreach([
            ['route' => 'analisis.aptitud', 'icon' => 'fa-heartbeat', 'title' => 'Aptitud Médica', 'color' => 'danger'],
            ['route' => 'analisis.nutricional', 'icon' => 'fa-apple-alt', 'title' => 'Nutrición', 'color' => 'success'],
            ['route' => 'analisis.respiratoria', 'icon' => 'fa-lungs', 'title' => 'Respiratorio', 'color' => 'info'],
            ['route' => 'analisis.psicologica', 'icon' => 'fa-brain', 'title' => 'Psicología', 'color' => 'warning'],
            ['route' => 'analisis.fisiologica', 'icon' => 'fa-heart-pulse', 'title' => 'Fisiología', 'color' => 'primary'],
            ['route' => 'analisis.video', 'icon' => 'fa-video', 'title' => 'Video Análisis', 'color' => 'secondary'],
        ] as $m)
        <div class="col-xl-2 col-md-4 col-6">
            <a href="{{ route($m['route']) }}" class="text-decoration-none">
                <div class="alpha-module-card h-100 text-center p-3">
                    <div class="module-icon-box mx-auto mb-2" style="width:50px;height:50px;"><i class="fas {{ $m['icon'] }}"></i></div>
                    <h6 class="module-title mb-0" style="font-size:0.8rem;">{{ $m['title'] }}</h6>
                </div>
            </a>
        </div>
        @endforeach
    </div>
</div>
@endsection
