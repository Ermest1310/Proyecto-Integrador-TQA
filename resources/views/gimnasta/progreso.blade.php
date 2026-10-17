@extends('layouts.gimnasta')

@section('title', 'Mi Progreso')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-primary">
                <div class="card-body text-white">
                    <h2><i class="fas fa-chart-line"></i> Mi Progreso Deportivo</h2>
                    <p class="mb-0">Seguimiento de tu evolución y desarrollo</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-trophy"></i> Nivel Actual</h3>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <div class="progress mb-3" style="height: 30px;">
                            <div class="progress-bar 
                                @if($gimnasta->Nivel == 'Elite') bg-success
                                @elseif($gimnasta->Nivel == 'Avanzado') bg-warning  
                                @elseif($gimnasta->Nivel == 'Intermedio') bg-info
                                @else bg-secondary @endif" 
                                style="width: 
                                @if($gimnasta->Nivel == 'Elite') 100%
                                @elseif($gimnasta->Nivel == 'Avanzado') 75%  
                                @elseif($gimnasta->Nivel == 'Intermedio') 50%
                                @else 25% @endif">
                                <strong>{{ $gimnasta->Nivel }}</strong>
                            </div>
                        </div>
                        
                        <h4 class="text-center">Nivel: {{ $gimnasta->Nivel }}</h4>
                        
                        @if($gimnasta->Nivel != 'Elite')
                            <p class="text-muted">
                                Próximo objetivo: 
                                @if($gimnasta->Nivel == 'Principiante') Intermedio
                                @elseif($gimnasta->Nivel == 'Intermedio') Avanzado
                                @elseif($gimnasta->Nivel == 'Avanzado') Elite
                                @endif
                            </p>
                        @else
                            <p class="text-success"><i class="fas fa-crown"></i> ¡Has alcanzado el nivel máximo!</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-heartbeat"></i> Estado Físico</h3>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-weight"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Peso</span>
                                    <span class="info-box-number">{{ $gimnasta->Peso }}kg</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fas fa-ruler-vertical"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Altura</span>
                                    <span class="info-box-number">{{ $gimnasta->Altura }}m</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    @php
                        $imc = ($gimnasta->Altura > 0) ? round($gimnasta->Peso / ($gimnasta->Altura * $gimnasta->Altura), 2) : 0;
                    @endphp
                    
                    <div class="text-center mt-3">
                        <h5>IMC: {{ $imc }}</h5>
                        @if($imc >= 18.5 && $imc < 25)
                            <span class="badge badge-success badge-lg">Estado Óptimo</span>
                        @elseif($imc < 18.5)
                            <span class="badge badge-warning badge-lg">Consultar Nutricionista</span>
                        @else
                            <span class="badge badge-info badge-lg">Revisar Plan Nutricional</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-calendar-alt"></i> Próximos Objetivos</h3>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="time-label">
                            <span class="bg-primary">Objetivos a Corto Plazo</span>
                        </div>
                        <div>
                            <i class="fas fa-dumbbell bg-info"></i>
                            <div class="timeline-item">
                                <h3 class="timeline-header">Mejorar Técnica</h3>
                                <div class="timeline-body">
                                    Perfeccionar movimientos básicos y trabajar en la precisión de las rutinas.
                                </div>
                            </div>
                        </div>
                        <div>
                            <i class="fas fa-heart bg-success"></i>
                            <div class="timeline-item">
                                <h3 class="timeline-header">Condición Física</h3>
                                <div class="timeline-body">
                                    Mantener un estado físico óptimo y mejorar la resistencia cardiovascular.
                                </div>
                            </div>
                        </div>
                        @if($gimnasta->Nivel != 'Elite')
                            <div class="time-label">
                                <span class="bg-warning">Objetivo Principal</span>
                            </div>
                            <div>
                                <i class="fas fa-star bg-warning"></i>
                                <div class="timeline-item">
                                    <h3 class="timeline-header">Avanzar al Siguiente Nivel</h3>
                                    <div class="timeline-body">
                                        Trabajar para alcanzar el nivel 
                                        @if($gimnasta->Nivel == 'Principiante') Intermedio
                                        @elseif($gimnasta->Nivel == 'Intermedio') Avanzado
                                        @elseif($gimnasta->Nivel == 'Avanzado') Elite
                                        @endif
                                        en los próximos meses.
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center">
        <a href="{{ route('admin.reportes.exportar-gimnasta', $gimnasta->ID_Usuario) }}" class="btn btn-success" target="_blank">
            <i class="fas fa-file-pdf"></i> Descargar Reporte PDF
        </a>
        <a href="{{ route('gimnasta.dashboard') }}" class="btn btn-primary">
            <i class="fas fa-arrow-left"></i> Volver al Dashboard
        </a>
    </div>
</div>
@endsection