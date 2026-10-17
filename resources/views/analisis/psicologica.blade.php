@extends('layouts.admin')

@section('title', 'Evaluación Psicológica')

@section('content')
<div class="container-fluid py-4">
    <div class="alpha-hero-panel mb-4">
        <div class="hero-overlay-grid"></div>
        <div class="p-4 position-relative" style="z-index:2;">
            <h1 class="hero-welcome"><i class="fas fa-brain text-gold me-2"></i>Evaluación Psicológica</h1>
            <p class="hero-subtitle mb-0">Monitoreo de salud mental y bienestar de atletas</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row g-2 mb-4">
        <div class="col-md-2-4 col-6"><div class="info-box bg-gradient-info"><span class="info-box-icon"><i class="fas fa-file-medical"></i></span><div class="info-box-content"><span class="info-box-text">Total Evaluaciones</span><span class="info-box-number">{{ $total }}</span></div></div></div>
        <div class="col-md-2-4 col-6"><div class="info-box bg-gradient-warning"><span class="info-box-icon"><i class="fas fa-brain"></i></span><div class="info-box-content"><span class="info-box-text">Ansiedad Prom.</span><span class="info-box-number">{{ $ansiedadProm ? number_format($ansiedadProm,1) : 'N/A' }}</span></div></div></div>
        <div class="col-md-2-4 col-6"><div class="info-box bg-gradient-danger"><span class="info-box-icon"><i class="fas fa-exclamation-triangle"></i></span><div class="info-box-content"><span class="info-box-text">Estrés Prom.</span><span class="info-box-number">{{ $estresProm ? number_format($estresProm,1) : 'N/A' }}</span></div></div></div>
        <div class="col-md-2-4 col-6"><div class="info-box bg-gradient-success"><span class="info-box-icon"><i class="fas fa-fire"></i></span><div class="info-box-content"><span class="info-box-text">Motivación Prom.</span><span class="info-box-number">{{ $motivacionProm ? number_format($motivacionProm,1) : 'N/A' }}</span></div></div></div>
        <div class="col-md-2-4 col-6"><div class="info-box bg-gradient-secondary"><span class="info-box-icon"><i class="fas fa-users"></i></span><div class="info-box-content"><span class="info-box-text">Gimnastas Eval.</span><span class="info-box-number">{{ $gimnastasEvaluados }}</span></div></div></div>
    </div>

    @if($total > 0)
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card"><div class="card-header bg-primary text-white"><h6 class="mb-0"><i class="fas fa-chart-radar me-2"></i>Perfil Psicológico Promedio</h6></div><div class="card-body"><canvas id="chartPsicologia" height="220"></canvas></div></div>
        </div>
        <div class="col-md-6">
            <div class="card"><div class="card-header bg-info text-white"><h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Promedios por Dimensión</h6></div><div class="card-body"><canvas id="chartPsicologiaBar" height="220"></canvas></div></div>
        </div>
    </div>
    @endif

    <div class="d-flex justify-content-end gap-2 mb-3">
        <a href="{{ route('analisis.psicologica.excel') }}" class="btn btn-success btn-sm"><i class="fas fa-file-excel"></i> Excel</a>
        <a href="{{ route('analisis.psicologica.pdf') }}" class="btn btn-danger btn-sm"><i class="fas fa-file-pdf"></i> PDF</a>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header"><i class="fas fa-plus-circle me-2"></i>Nueva Evaluación</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('analisis.psicologica.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Gimnasta</label>
                            <select name="ID_Gimnasta" class="form-control" required>
                                <option value="">Seleccionar...</option>
                                @foreach($gimnastas as $g)
                                    <option value="{{ $g->ID_Gimnasta }}">{{ $g->Nombre }} {{ $g->Apellido }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Fecha</label>
                            <input type="date" name="FechaEvaluacion" class="form-control" required value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="row">
                            <div class="col-4 mb-3">
                                <label class="form-label">Ansiedad (1-10)</label>
                                <input type="number" min="1" max="10" name="NivelAnsiedad" class="form-control">
                            </div>
                            <div class="col-4 mb-3">
                                <label class="form-label">Estrés (1-10)</label>
                                <input type="number" min="1" max="10" name="NivelEstres" class="form-control">
                            </div>
                            <div class="col-4 mb-3">
                                <label class="form-label">Motivación (1-10)</label>
                                <input type="number" min="1" max="10" name="NivelMotivacion" class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label">Sueño (1-10)</label>
                                <input type="number" min="1" max="10" name="CalidadSueno" class="form-control">
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">Concentración (1-10)</label>
                                <input type="number" min="1" max="10" name="Concentracion" class="form-control">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Recomendaciones</label>
                            <textarea name="Recomendaciones" class="form-control" rows="2"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100"><i class="fas fa-save"></i> Guardar</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header"><i class="fas fa-list me-2"></i>Evaluaciones</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead><tr><th>Gimnasta</th><th>Fecha</th><th>Ansiedad</th><th>Estrés</th><th>Motivación</th><th>Sueño</th><th>Concentración</th></tr></thead>
                            <tbody>
                                @forelse($evaluaciones as $e)
                                <tr>
                                    <td>{{ $e->gimnasta->Nombre ?? '-' }} {{ $e->gimnasta->Apellido ?? '' }}</td>
                                    <td>{{ $e->FechaEvaluacion }}</td>
                                    <td><span class="badge bg-{{ $e->NivelAnsiedad > 7 ? 'danger' : ($e->NivelAnsiedad > 4 ? 'warning text-dark' : 'success') }}">{{ $e->NivelAnsiedad ?? '-' }}</span></td>
                                    <td><span class="badge bg-{{ $e->NivelEstres > 7 ? 'danger' : ($e->NivelEstres > 4 ? 'warning text-dark' : 'success') }}">{{ $e->NivelEstres ?? '-' }}</span></td>
                                    <td><span class="badge bg-{{ $e->NivelMotivacion >= 7 ? 'success' : ($e->NivelMotivacion >= 4 ? 'warning text-dark' : 'danger') }}">{{ $e->NivelMotivacion ?? '-' }}</span></td>
                                    <td>{{ $e->CalidadSueno ?? '-' }}</td>
                                    <td>{{ $e->Concentracion ?? '-' }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="7" class="text-center text-muted">Sin registros</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@if($total > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded',function(){
    new Chart(document.getElementById('chartPsicologia'),{
        type:'radar',
        data:{
            labels:['Ansiedad','Estrés','Motivación','Sueño','Concentración'],
            datasets:[{
                label:'Promedio',
                data:[{{ $ansiedadProm ?? 0 }},{{ $estresProm ?? 0 }},{{ $motivacionProm ?? 0 }},{{ $suenoProm ?? 0 }},{{ $concentracionProm ?? 0 }}],
                backgroundColor:'rgba(13,110,253,0.2)',borderColor:'#0d6efd',pointBackgroundColor:'#0d6efd'
            }]
        },
        options:{responsive:true,maintainAspectRatio:false,scales:{r:{min:0,max:10,ticks:{stepSize:2}}}}
    });
    new Chart(document.getElementById('chartPsicologiaBar'),{
        type:'bar',
        data:{
            labels:['Ansiedad','Estrés','Motivación','Sueño','Concentración'],
            datasets:[{
                label:'Promedios',
                data:[{{ $ansiedadProm ?? 0 }},{{ $estresProm ?? 0 }},{{ $motivacionProm ?? 0 }},{{ $suenoProm ?? 0 }},{{ $concentracionProm ?? 0 }}],
                backgroundColor:['#dc3545','#fd7e14','#28a745','#0d6efd','#6610f2'],borderRadius:4
            }]
        },
        options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true,max:10}}}
    });
});
</script>
@endif
@endsection
