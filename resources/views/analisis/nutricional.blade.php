@extends('layouts.admin')

@section('title', 'Evaluación Nutricional')

@section('content')
<div class="container-fluid py-4">
    <div class="alpha-hero-panel mb-4">
        <div class="hero-overlay-grid"></div>
        <div class="p-4 position-relative" style="z-index:2;">
            <h1 class="hero-welcome"><i class="fas fa-apple-alt text-gold me-2"></i>Evaluación Nutricional</h1>
            <p class="hero-subtitle mb-0">Control de IMC, composición corporal e hidratación de atletas</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row g-2 mb-4">
        <div class="col-md-2-4 col-6"><div class="info-box bg-gradient-info"><span class="info-box-icon"><i class="fas fa-file-medical"></i></span><div class="info-box-content"><span class="info-box-text">Total Evaluaciones</span><span class="info-box-number">{{ $total }}</span></div></div></div>
        <div class="col-md-2-4 col-6"><div class="info-box bg-gradient-success"><span class="info-box-icon"><i class="fas fa-weight"></i></span><div class="info-box-content"><span class="info-box-text">IMC Promedio</span><span class="info-box-number">{{ $imcPromedio ? number_format($imcPromedio,1) : 'N/A' }}</span></div></div></div>
        <div class="col-md-2-4 col-6"><div class="info-box bg-gradient-warning"><span class="info-box-icon"><i class="fas fa-fire"></i></span><div class="info-box-content"><span class="info-box-text">% Grasa Prom.</span><span class="info-box-number">{{ $grasaPromedio ? number_format($grasaPromedio,1) : 'N/A' }}</span></div></div></div>
        <div class="col-md-2-4 col-6"><div class="info-box bg-gradient-secondary"><span class="info-box-icon"><i class="fas fa-dumbbell"></i></span><div class="info-box-content"><span class="info-box-text">Masa Musc. Prom.</span><span class="info-box-number">{{ $muscularPromedio ? number_format($muscularPromedio,1) : 'N/A' }}</span></div></div></div>
        <div class="col-md-2-4 col-6"><div class="info-box bg-gradient-primary"><span class="info-box-icon"><i class="fas fa-tint"></i></span><div class="info-box-content"><span class="info-box-text">Hidratación Prom.</span><span class="info-box-number">{{ $hidratacionPromedio ? number_format($hidratacionPromedio,1) : 'N/A' }}</span></div></div></div>
    </div>

    @if($total > 0)
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card"><div class="card-header bg-success text-white"><h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Promedios Nutricionales</h6></div><div class="card-body"><canvas id="chartNutricional" height="180"></canvas></div></div>
        </div>
        <div class="col-md-6">
            <div class="card"><div class="card-header bg-info text-white"><h6 class="mb-0"><i class="fas fa-chart-line me-2"></i>Evaluaciones por Mes</h6></div><div class="card-body"><canvas id="chartNutricionalMes" height="180"></canvas></div></div>
        </div>
    </div>
    @endif

    <div class="d-flex justify-content-end gap-2 mb-3">
        <a href="{{ route('analisis.nutricional.excel') }}" class="btn btn-success btn-sm"><i class="fas fa-file-excel"></i> Excel</a>
        <a href="{{ route('analisis.nutricional.pdf') }}" class="btn btn-danger btn-sm"><i class="fas fa-file-pdf"></i> PDF</a>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header"><i class="fas fa-plus-circle me-2"></i>Nueva Evaluación</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('analisis.nutricional.store') }}">
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
                            <div class="col-6 mb-3">
                                <label class="form-label">Peso (kg)</label>
                                <input type="number" step="0.1" name="Peso" class="form-control">
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">Altura (cm)</label>
                                <input type="number" step="0.1" name="Altura" class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label">% Grasa</label>
                                <input type="number" step="0.1" name="PorcentajeGrasa" class="form-control">
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">Masa Muscular</label>
                                <input type="number" step="0.1" name="MasaMuscular" class="form-control">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nivel Hidratación</label>
                            <input type="number" step="0.1" name="NivelHidratacion" class="form-control" placeholder="0-100%">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Seguimiento Dieta</label>
                            <input type="text" name="SeguimientoDieta" class="form-control" placeholder="Ej: Dieta hipocalórica">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Recomendaciones</label>
                            <textarea name="Recomendaciones" class="form-control" rows="2"></textarea>
                        </div>
                        <button type="submit" class="btn btn-success w-100"><i class="fas fa-save"></i> Guardar</button>
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
                            <thead><tr><th>Gimnasta</th><th>Fecha</th><th>Peso</th><th>Altura</th><th>IMC</th><th>% Grasa</th><th>Masa Musc.</th><th>Hidrat.</th></tr></thead>
                            <tbody>
                                @forelse($evaluaciones as $e)
                                <tr>
                                    <td>{{ $e->gimnasta->Nombre ?? '-' }} {{ $e->gimnasta->Apellido ?? '' }}</td>
                                    <td>{{ $e->FechaEvaluacion }}</td>
                                    <td>{{ $e->Peso ?? '-' }}</td>
                                    <td>{{ $e->Altura ?? '-' }}</td>
                                    <td><strong>{{ $e->IMC ?? '-' }}</strong></td>
                                    <td>{{ $e->PorcentajeGrasa ?? '-' }}%</td>
                                    <td>{{ $e->MasaMuscular ?? '-' }}</td>
                                    <td>{{ $e->NivelHidratacion ?? '-' }}%</td>
                                </tr>
                                @empty
                                <tr><td colspan="8" class="text-center text-muted">Sin registros</td></tr>
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
    new Chart(document.getElementById('chartNutricional'),{
        type:'bar',
        data:{
            labels:['IMC','% Grasa','Masa Musc.','Hidratación'],
            datasets:[{
                label:'Promedios',
                data:[{{ $imcPromedio ?? 0 }},{{ $grasaPromedio ?? 0 }},{{ $muscularPromedio ?? 0 }},{{ $hidratacionPromedio ?? 0 }}],
                backgroundColor:['#28a745','#dc3545','#0d6efd','#17a2b8'],borderRadius:4
            }]
        },
        options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true}}}
    });
    new Chart(document.getElementById('chartNutricionalMes'),{
        type:'line',
        data:{
            labels:{!! json_encode($porMes->pluck('mes')) !!},
            datasets:[{label:'Evaluaciones',data:{!! json_encode($porMes->pluck('count')) !!},borderColor:'#0d6efd',backgroundColor:'rgba(13,110,253,0.1)',fill:true,tension:0.3}]
        },
        options:{responsive:true,maintainAspectRatio:false,scales:{y:{beginAtZero:true,ticks:{stepSize:1}}}}
    });
});
</script>
@endif
@endsection
