@extends('layouts.medico')

@section('title', 'Psicología Deportiva - Sistema Experto')

@section('content')
<div class="container-fluid">
    <div class="card card-primary mb-3">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-brain"></i> Psicología Deportiva - Gimnasia Artística de Alto Rendimiento</h3>
        </div>
        <div class="card-body">
            <p>
                Nuestro Sistema Experto de Psicología Deportiva está diseñado específicamente para gimnastas de alto rendimiento. 
                Utilizamos un enfoque basado en <strong>Machine Learning</strong> y <strong>análisis predictivo</strong> para identificar 
                patrones de comportamiento, niveles de estrés pre-competitivo y factores que afectan el rendimiento deportivo.
            </p>
            <p class="mb-0">
                <i class="fas fa-check-circle text-success"></i> Gestión de ansiedad pre-competitiva &nbsp;
                <i class="fas fa-check-circle text-success"></i> Técnicas de visualización para rutinas &nbsp;
                <i class="fas fa-check-circle text-success"></i> Fortalecimiento de la confianza deportiva &nbsp;
                <i class="fas fa-check-circle text-success"></i> Recuperación psicológica post-lesión
            </p>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-12">
            <div class="card card-info">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-brain-circuit"></i> Sistema de Evaluación Psicológica Inteligente (SEPI)
                    </h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h5><i class="fas fa-info-circle"></i> Aporte Ingenieril del Sistema</h5>
                        <p class="mb-2">
                            El <strong>SEPI</strong> es un módulo de inteligencia artificial que analiza múltiples variables psicológicas 
                            del gimnasta para generar recomendaciones personalizadas. El sistema utiliza:
                        </p>
                        <ul class="mb-2">
                            <li><strong>Algoritmo de Scoring Psicológico:</strong> Evalúa 5 dimensiones clave (ansiedad, concentración, motivación, autoconfianza, resiliencia) con ponderación dinámica según la fase de entrenamiento.</li>
                            <li><strong>Sistema de Reglas Expertas:</strong> Base de conocimiento de 150+ reglas validadas por psicólogos deportivos especialistas en gimnasia artística.</li>
                            <li><strong>Análisis Predictivo de Rendimiento:</strong> Predice probabilidad de rendimiento óptimo en competencia basado en estado psicológico actual.</li>
                            <li><strong>Motor de Recomendaciones:</strong> Sugiere intervenciones específicas (técnicas de respiración, visualización, reestructuración cognitiva) según el perfil del atleta.</li>
                        </ul>
                    </div>
                    
                    {{-- 🌟 NUEVO BOTÓN PARA EL CHATBOT DENTRO DEL SEPI --}}
                    <a href="{{ route('psicologia.chatbot') }}" class="btn btn-lg btn-success btn-block mb-3" id="btn-sepi-chatbot">
                        <i class="fas fa-robot"></i> Acceder al **Chatbot** de Soporte Mental (24/7)
                    </a>
                    {{-- ---------------------------------------------------- --}}

                    <div class="row">
                        <div class="col-md-3 col-sm-6">
                            <div class="info-box bg-gradient-success">
                                <span class="info-box-icon"><i class="fas fa-chart-line"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Índice de Bienestar</span>
                                    <span class="info-box-number">{{ $metricas['bienestar'] ?? 'N/A' }}%</span>
                                    <div class="progress">
                                        <div class="progress-bar" style="width: {{ $metricas['bienestar'] ?? 0 }}%"></div>
                                    </div>
                                    <span class="progress-description">Basado en últimas 10 sesiones</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="info-box bg-gradient-info">
                                <span class="info-box-icon"><i class="fas fa-brain"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Resiliencia Mental</span>
                                    <span class="info-box-number">{{ $metricas['resiliencia'] ?? 'N/A' }}/10</span>
                                    <div class="progress">
                                        <div class="progress-bar" style="width: {{ ($metricas['resiliencia'] ?? 0) * 10 }}%"></div>
                                    </div>
                                    <span class="progress-description">Evaluación algorítmica</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="info-box bg-gradient-warning">
                                <span class="info-box-icon"><i class="fas fa-exclamation-triangle"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Nivel de Ansiedad</span>
                                    <span class="info-box-number">{{ $metricas['ansiedad'] ?? 'N/A' }}</span>
                                    <div class="progress">
                                        <div class="progress-bar" style="width: {{ $metricas['ansiedad_porcentaje'] ?? 0 }}%"></div>
                                    </div>
                                    <span class="progress-description">Monitoreo continuo</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="info-box bg-gradient-primary">
                                <span class="info-box-icon"><i class="fas fa-trophy"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Predicción Rendimiento</span>
                                    <span class="info-box-number">{{ $metricas['prediccion'] ?? 'N/A' }}%</span>
                                    <div class="progress">
                                        <div class="progress-bar" style="width: {{ $metricas['prediccion'] ?? 0 }}%"></div>
                                    </div>
                                    <span class="progress-description">Próxima competencia</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if(!empty($recomendaciones))
                    <div class="callout callout-warning">
                        <h5><i class="fas fa-lightbulb"></i> Recomendaciones del Sistema Experto</h5>
                        <ul class="mb-0">
                            @foreach($recomendaciones as $rec)
                            <li><strong>{{ $rec['prioridad'] }}:</strong> {{ $rec['texto'] }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-12">
            <h4><i class="fas fa-user-md text-primary"></i> Especialistas en Psicología Deportiva</h4>
        </div>
        @foreach($especialistas as $especialista)
        <div class="col-md-4">
            <div class="card card-outline card-primary">
                <div class="card-body text-center">
                    <img src="{{ $especialista->foto ?? asset('img/default-user.png') }}" 
                         class="img-fluid rounded-circle mb-2" 
                         width="100" 
                         alt="Foto del especialista"
                         style="border: 3px solid #007bff; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <h5 class="mb-1">{{ $especialista->Nombre }}</h5>
                    <p class="text-muted small mb-2">
                        <i class="fas fa-certificate text-warning"></i> 
                        {{ $especialista->certificacion ?? 'Psicólogo Deportivo Certificado' }}
                    </p>
                    <p class="mb-2">{{ $especialista->Descripcion ?? 'Especialista en alto rendimiento' }}</p>
                    <p class="mb-2">
                        <i class="far fa-clock text-info"></i> 
                        <strong>Horario:</strong> {{ $especialista->horario ?? 'Consultar disponibilidad' }}
                    </p>
                    <div class="btn-group btn-block">
                        <button class="btn btn-primary btn-sm" disabled>
                            <i class="fas fa-calendar-check"></i> Agendar
                        </button>
                        <button class="btn btn-outline-primary btn-sm" disabled>
                            <i class="fas fa-info-circle"></i> Ver Perfil
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="row mb-3">
        <div class="col-12">
            <h4><i class="fas fa-hand-holding-medical text-primary"></i> Intervenciones Psicológicas Especializadas</h4>
        </div>
        @forelse($tratamientos ?? [] as $tratamiento)
        <div class="col-lg-3 col-md-4 col-sm-6">
            <div class="card card-outline card-primary h-100">
                <div class="card-body text-center">
                    <i class="fas fa-notes-medical fa-3x mb-3 text-primary"></i>
                    <h6 class="font-weight-bold">{{ $tratamiento->nombre ?? 'Intervención Psicológica' }}</h6>
                    <hr>
                    <p class="text-left mb-1">
                        <i class="far fa-clock text-info"></i> 
                        <strong>Duración:</strong> {{ $tratamiento->duracion ?? 'N/A' }}
                    </p>
                    <p class="text-left mb-1">
                        <i class="fas fa-user-md text-success"></i> 
                        <strong>Especialista:</strong> {{ $tratamiento->especialista ?? 'N/A' }}
                    </p>
                    <p class="text-left mb-3">
                        <i class="fas fa-tags text-warning"></i> 
                        <strong>Modalidad:</strong> {{ $tratamiento->modalidad ?? 'Presencial' }}
                    </p>
                    <button class="btn btn-outline-primary btn-sm btn-block" disabled>
                        <i class="fas fa-paper-plane"></i> Solicitar Intervención
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-warning text-center">
                <i class="fas fa-exclamation-circle"></i> No hay intervenciones registradas en el sistema.
            </div>
        </div>
        @endforelse
    </div>

    <div class="card card-outline card-secondary">
        <div class="card-header">
            <h4 class="card-title"><i class="fas fa-history"></i> Historial de Sesiones y Análisis de Tendencias</h4>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            @if(!empty($historial) && count($historial) > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped">
                    <thead class="bg-secondary">
                        <tr>
                            <th><i class="far fa-calendar"></i> Fecha</th>
                            <th><i class="fas fa-user-md"></i> Especialista</th>
                            <th><i class="fas fa-clipboard-list"></i> Tipo de Intervención</th>
                            <th><i class="fas fa-chart-line"></i> Progreso</th>
                            <th><i class="fas fa-sticky-note"></i> Observaciones</th>
                            <th><i class="fas fa-cog"></i> Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($historial as $sesion)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($sesion->fecha)->format('d/m/Y H:i') ?? 'N/A' }}</td>
                            <td>{{ $sesion->especialista ?? 'N/A' }}</td>
                            <td>
                                <span class="badge badge-info">{{ $sesion->tipo_consulta ?? 'N/A' }}</span>
                            </td>
                            <td>
                                @if(isset($sesion->progreso))
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar bg-success" 
                                         role="progressbar" 
                                         style="width: {{ $sesion->progreso }}%"
                                         aria-valuenow="{{ $sesion->progreso }}" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                        {{ $sesion->progreso }}%
                                    </div>
                                </div>
                                @else
                                <span class="text-muted">Sin evaluar</span>
                                @endif
                            </td>
                            <td>{{ Str::limit($sesion->notas ?? 'Sin observaciones', 50) }}</td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-info" disabled title="Ver detalles">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3">
                <h5><i class="fas fa-chart-area text-primary"></i> Evolución del Estado Psicológico</h5>
                <div id="grafico-evolucion" style="height: 300px; background: #f4f6f9; border: 1px dashed #ccc; display: flex; align-items: center; justify-content: center;">
                    <p class="text-muted"><i class="fas fa-chart-line fa-3x"></i><br>Gráfico de tendencias - Integrar con Chart.js</p>
                </div>
            </div>
            @else
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle fa-2x mb-2"></i>
                <p class="mb-0">Aún no tienes sesiones registradas en el sistema. Agenda tu primera consulta para comenzar tu seguimiento psicológico.</p>
            </div>
            @endif
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-6">
            <div class="card card-outline card-success">
                <div class="card-header">
                    <h5 class="card-title"><i class="fas fa-book-reader"></i> Recursos de Apoyo</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="fas fa-video text-danger"></i> 
                            <a href="#">Técnicas de respiración para competencia</a>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-file-pdf text-danger"></i> 
                            <a href="#">Guía: Visualización de rutinas exitosas</a>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-headphones text-primary"></i> 
                            <a href="#">Audio: Meditación pre-competitiva</a>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-clipboard-check text-success"></i> 
                            <a href="#">Cuestionario de autoevaluación psicológica</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-outline card-warning">
                <div class="card-header">
                    <h5 class="card-title"><i class="fas fa-phone-alt"></i> Contacto de Emergencia</h5>
                </div>
                <div class="card-body">
                    <p><strong>Línea de Crisis 24/7</strong></p>
                    <p><i class="fas fa-phone"></i> +591 123-4567</p>
                    <p><i class="fas fa-envelope"></i> emergencias@psicodeportiva.com</p>
                    <button class="btn btn-warning btn-block">
                        <i class="fas fa-exclamation-triangle"></i> Reportar Situación Urgente
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 🌟 BOTÓN FLOTANTE (fuera del container-fluid para mayor visibilidad) --}}
<a href="{{ route('psicologia.chatbot') }}" class="btn btn-lg btn-danger shadow" id="floating-chatbot-btn">
    <i class="fas fa-robot fa-lg"></i>
</a>
{{-- -------------------------------------------------------------------- --}}

@push('styles')
<style>
/* Estilo para el botón flotante del Chatbot */
#floating-chatbot-btn {
    position: fixed;
    bottom: 20px; /* Distancia desde la parte inferior */
    right: 20px; /* Distancia desde la derecha */
    border-radius: 50%; /* Hacerlo circular */
    width: 60px;
    height: 60px;
    padding: 0;
    line-height: 60px; /* Centrar el icono verticalmente */
    text-align: center;
    z-index: 1050; /* Asegurarse de que esté sobre otros elementos */
    background-color: #dc3545; /* Rojo de AdminLTE */
    border-color: #dc3545;
    transition: transform 0.3s ease;
}

#floating-chatbot-btn:hover {
    transform: scale(1.1); /* Efecto de hover */
}
</style>
@endpush

@push('scripts')
<script>
// Script para inicializar gráficos con Chart.js (a implementar)
document.addEventListener('DOMContentLoaded', function() {
    console.log('Sistema Experto de Psicología Deportiva cargado');
    // Aquí se puede integrar Chart.js para mostrar tendencias
});
</script>
@endpush
@endsection