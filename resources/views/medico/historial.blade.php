@extends('layouts.medico')

@section('title', 'Historial Médico')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-info">
                <div class="card-body text-white">
                    <h2><i class="fas fa-clipboard-list"></i> Historial Médico</h2>
                    <p class="mb-0">{{ $gimnasta->Nombre }} {{ $gimnasta->Apellido }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <!-- Información del Paciente -->
            <div class="card">
                <div class="card-header bg-primary">
                    <h3 class="card-title text-white">Información del Paciente</h3>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <i class="fas fa-user-circle fa-4x text-muted"></i>
                        <h5 class="mt-2">{{ $gimnasta->Nombre }} {{ $gimnasta->Apellido }}</h5>
                    </div>
                    
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td><strong>Edad:</strong></td>
                            <td>{{ \Carbon\Carbon::parse($gimnasta->FechaNacimiento)->age }} años</td>
                        </tr>
                        <tr>
                            <td><strong>Peso:</strong></td>
                            <td>{{ $gimnasta->Peso }} kg</td>
                        </tr>
                        <tr>
                            <td><strong>Altura:</strong></td>
                            <td>{{ $gimnasta->Altura }} m</td>
                        </tr>
                        <tr>
                            <td><strong>IMC:</strong></td>
                            <td>
                                @php
                                    $imc = ($gimnasta->Altura > 0) ? round($gimnasta->Peso / ($gimnasta->Altura * $gimnasta->Altura), 2) : 0;
                                @endphp
                                {{ $imc }}
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Nivel:</strong></td>
                            <td>
                                <span class="badge 
                                    @if($gimnasta->Nivel == 'Elite') badge-success
                                    @elseif($gimnasta->Nivel == 'Avanzado') badge-warning  
                                    @elseif($gimnasta->Nivel == 'Intermedio') badge-info
                                    @else badge-secondary @endif">
                                    {{ $gimnasta->Nivel }}
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <!-- Historial Médico -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-history"></i> Historial de Consultas</h3>
                    <button class="btn btn-primary btn-sm float-right">
                        <i class="fas fa-plus"></i> Nueva Consulta
                    </button>
                </div>
                <div class="card-body">
                    <!-- Timeline de consultas médicas -->
                    <div class="timeline">
                        <div class="time-label">
                            <span class="bg-primary">Enero 2025</span>
                        </div>
                        <div>
                            <i class="fas fa-stethoscope bg-info"></i>
                            <div class="timeline-item">
                                <span class="time"><i class="fas fa-clock"></i> 15 Ene 2025</span>
                                <h3 class="timeline-header">Consulta de Rutina</h3>
                                <div class="timeline-body">
                                    <strong>Motivo:</strong> Revisión médica periódica<br>
                                    <strong>Observaciones:</strong> Estado físico óptimo, sin lesiones aparentes.<br>
                                    <strong>Recomendaciones:</strong> Continuar con el entrenamiento actual.
                                </div>
                            </div>
                        </div>
                        
                        <div class="time-label">
                            <span class="bg-warning">Diciembre 2024</span>
                        </div>
                        <div>
                            <i class="fas fa-band-aid bg-warning"></i>
                            <div class="timeline-item">
                                <span class="time"><i class="fas fa-clock"></i> 10 Dic 2024</span>
                                <h3 class="timeline-header">Tratamiento de Lesión</h3>
                                <div class="timeline-body">
                                    <strong>Motivo:</strong> Dolor en muñeca izquierda<br>
                                    <strong>Diagnóstico:</strong> Sobrecarga muscular leve<br>
                                    <strong>Tratamiento:</strong> Reposo relativo 5 días, fisioterapia<br>
                                    <strong>Estado:</strong> <span class="badge badge-success">Recuperado</span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <i class="fas fa-heartbeat bg-success"></i>
                            <div class="timeline-item">
                                <span class="time"><i class="fas fa-clock"></i> 25 Nov 2024</span>
                                <h3 class="timeline-header">Evaluación Cardiovascular</h3>
                                <div class="timeline-body">
                                    <strong>Motivo:</strong> Evaluación previa a competencia<br>
                                    <strong>Resultados:</strong> Frecuencia cardíaca en reposo: 58 bpm<br>
                                    <strong>Conclusión:</strong> Excelente condición cardiovascular<br>
                                    <strong>Autorización:</strong> <span class="badge badge-success">Apto para competir</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recomendaciones Actuales -->
            <div class="card">
                <div class="card-header bg-success">
                    <h3 class="card-title text-white"><i class="fas fa-clipboard-check"></i> Recomendaciones Actuales</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fas fa-dumbbell text-primary"></i> Entrenamiento</h6>
                            <ul>
                                <li>Mantener rutina actual de entrenamiento</li>
                                <li>Incluir ejercicios de flexibilidad</li>
                                <li>Calentamiento mínimo 15 minutos</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-apple-alt text-success"></i> Nutrición</h6>
                            <ul>
                                <li>Hidratación constante durante entrenamientos</li>
                                <li>Dieta balanceada rica en proteínas</li>
                                <li>Suplementos según indicación nutricional</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center mt-4">
        <a href="{{ route('medico.dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
        <a href="{{ route('medico.ver-gimnasta', $gimnasta->ID_Usuario) }}" class="btn btn-info">
            <i class="fas fa-user"></i> Ver Perfil
        </a>
    </div>
</div>
@endsection