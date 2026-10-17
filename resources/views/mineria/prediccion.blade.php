@extends('layouts.admin')

@section('title', 'Predicción con IA')

@section('content')
<div class="container-fluid py-4">
    <div class="alpha-hero-panel mb-4">
        <div class="hero-overlay-grid"></div>
        <div class="p-4 position-relative" style="z-index:2;">
            <h1 class="hero-welcome"><i class="fas fa-brain text-gold me-2"></i>Predicción con IA</h1>
            <p class="hero-subtitle mb-0">Análisis predictivo de rendimiento y riesgo de lesiones usando OpenAI GPT-3.5</p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-5">
            <div class="card mb-4">
                <div class="card-header"><i class="fas fa-robot me-2"></i>Solicitar Análisis</div>
                <div class="card-body">
                    <form method="POST" id="iaForm">
                        @csrf
                        <div class="form-group">
                            <label for="tipo_analisis">Tipo de Análisis:</label>
                            <select name="tipo_analisis" id="tipo_analisis" class="form-control">
                                <option value="rendimiento">Predicción de Rendimiento</option>
                                <option value="lesiones">Perfil de Riesgo de Lesiones</option>
                                <option value="recomendaciones">Recomendaciones Personalizadas</option>
                            </select>
                        </div>

                        <h6 class="mt-4">Datos que se enviarán a la IA:</h6>
                        <div class="alpha-glass-list-card p-3 small">
                            <ul class="list-unstyled mb-0">
                                <li><i class="fas fa-check-circle text-success me-2"></i>{{ $gimnastas->count() }} gimnastas activos</li>
                                <li><i class="fas fa-check-circle text-success me-2"></i>Nombre, nivel, edad, IMC por gimnasta</li>
                                <li><i class="fas fa-check-circle text-success me-2"></i>Rendimiento (% sesiones completadas)</li>
                                <li><i class="fas fa-check-circle text-success me-2"></i>Lesiones activas e históricas</li>
                                <li><i class="fas fa-check-circle text-success me-2"></i>Estadísticas globales del sistema</li>
                            </ul>
                        </div>

                        <button type="submit" class="btn btn-info btn-block mt-4" id="iaSubmitBtn">
                            <i class="fas fa-robot"></i> Generar Análisis con IA
                        </button>
                    </form>
                    <div class="loading text-center py-3" id="iaLoading" style="display:none;">
                        <div class="spinner-border text-info" role="status"></div>
                        <p class="mt-2">La IA está analizando los datos minados...</p>
                        <small class="text-muted">Este proceso puede tomar hasta 30 segundos</small>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><i class="fas fa-database me-2"></i>Datos Disponibles</div>
                <div class="card-body small">
                    <table class="table table-sm mb-0">
                        <thead><tr><th>Gimnasta</th><th>Nivel</th><th>Edad</th><th>IMC</th></tr></thead>
                        <tbody>
                             @foreach($gimnastas as $g)
                             @php
                                 $gPeso = $g->g_peso ?? $g->Peso ?? 0;
                                 $gAltura = $g->g_altura ?? $g->Altura ?? 0;
                                 $gNivel = $g->g_nivel ?? $g->Nivel;
                                 $gEdad = $g->g_fecha_nac ? \Carbon\Carbon::parse($g->g_fecha_nac)->age : ($g->FechaNacimiento ? $g->FechaNacimiento->age : '-');
                                 $gImc = $gAltura > 0 ? round($gPeso / ($gAltura ** 2), 1) : '-';
                             @endphp
                             <tr>
                                 <td>{{ $g->Nombre }} {{ $g->Apellido }}</td>
                                 <td><span class="badge badge-info">{{ $gNivel }}</span></td>
                                 <td>{{ $gEdad }}</td>
                                 <td>{{ $gImc }}</td>
                             </tr>
                             @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card">
                <div class="card-header"><i class="fas fa-chart-line me-2"></i>Resultado del Análisis</div>
                <div class="card-body" id="resultadoContainer">
                    @if($resultado)
                        <div class="alert alert-info">
                            <h6><i class="fas fa-brain"></i> Análisis Generado:</h6>
                            <hr>
                            <div class="ia-response">{!! nl2br(e($resultado)) !!}</div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-robot fa-4x text-muted mb-3"></i>
                            <p class="text-muted">Selecciona un tipo de análisis y haz clic en "Generar Análisis con IA"<br>
                            para obtener predicciones basadas en los datos minados del sistema.</p>
                            <p class="small text-muted">La IA analizará patrones de rendimiento, riesgos de lesiones<br>
                            y generará recomendaciones personalizadas para cada gimnasta.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('iaForm').addEventListener('submit', function(e) {
    document.getElementById('iaSubmitBtn').disabled = true;
    document.getElementById('iaSubmitBtn').innerHTML = '<i class="fas fa-spinner fa-spin"></i> Analizando...';
    document.getElementById('iaLoading').style.display = 'block';
});
</script>

<style>
.ia-response { line-height: 1.8; }
.ia-response table { width: 100%; margin: 10px 0; border-collapse: collapse; }
.ia-response th, .ia-response td { border: 1px solid #dee2e6; padding: 8px; }
.ia-response th { background: #f8f9fa; font-weight: 600; }
[data-theme="dark"] .ia-response th { background: #1a2e4a; }
[data-theme="dark"] .ia-response td { border-color: #1e3e62; }
</style>
@endsection
