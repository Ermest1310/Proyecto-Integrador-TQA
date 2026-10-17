@extends('layouts.admin')

@section('title', 'Evaluación Respiratoria')

@section('content')
<div class="container-fluid py-4">
    <div class="alpha-hero-panel mb-4">
        <div class="hero-overlay-grid"></div>
        <div class="p-4 position-relative" style="z-index:2;">
            <h1 class="hero-welcome"><i class="fas fa-lungs text-gold me-2"></i>Evaluación Respiratoria</h1>
            <p class="hero-subtitle mb-0">Control de capacidad pulmonar y saturación de oxígeno</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row g-2 mb-4">
        <div class="col-md-3 col-6"><div class="info-box bg-gradient-info"><span class="info-box-icon"><i class="fas fa-file-medical"></i></span><div class="info-box-content"><span class="info-box-text">Total Evaluaciones</span><span class="info-box-number">{{ $total }}</span></div></div></div>
        <div class="col-md-3 col-6"><div class="info-box bg-gradient-success"><span class="info-box-icon"><i class="fas fa-lungs"></i></span><div class="info-box-content"><span class="info-box-text">Cap. Pulmonar Prom.</span><span class="info-box-number">{{ $capPromedio ? number_format($capPromedio,2) : 'N/A' }}</span></div></div></div>
        <div class="col-md-3 col-6"><div class="info-box bg-gradient-primary"><span class="info-box-icon"><i class="fas fa-percent"></i></span><div class="info-box-content"><span class="info-box-text">Sat O2 Promedio</span><span class="info-box-number">{{ $satPromedio ? number_format($satPromedio,1) : 'N/A' }}</span></div></div></div>
        <div class="col-md-3 col-6"><div class="info-box bg-gradient-secondary"><span class="info-box-icon"><i class="fas fa-users"></i></span><div class="info-box-content"><span class="info-box-text">Gimnastas Evaluados</span><span class="info-box-number">{{ $gimnastasEvaluados }}</span></div></div></div>
    </div>

    @if($total > 0)
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card"><div class="card-header bg-info text-white"><h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Promedios Respiratorios</h6></div><div class="card-body"><canvas id="chartRespiratorio" height="180"></canvas></div></div>
        </div>
        <div class="col-md-6">
            <div class="card"><div class="card-header bg-primary text-white"><h6 class="mb-0"><i class="fas fa-stethoscope me-2"></i>Diagnósticos</h6></div><div class="card-body"><canvas id="chartRespDiagnostico" height="180"></canvas></div></div>
        </div>
    </div>
    @endif

    <div class="d-flex justify-content-end gap-2 mb-3">
        <a href="{{ route('analisis.respiratoria.excel') }}" class="btn btn-success btn-sm"><i class="fas fa-file-excel"></i> Excel</a>
        <a href="{{ route('analisis.respiratoria.pdf') }}" class="btn btn-danger btn-sm"><i class="fas fa-file-pdf"></i> PDF</a>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header"><i class="fas fa-plus-circle me-2"></i>Nueva Evaluación</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('analisis.respiratoria.store') }}">
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
                        <div class="mb-3">
                            <label class="form-label">Capacidad Pulmonar (L)</label>
                            <input type="number" step="0.1" name="CapacidadPulmonar" class="form-control" placeholder="Ej: 4.5">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Saturación O2 (%)</label>
                            <input type="number" step="0.1" name="SaturacionOxigeno" class="form-control" placeholder="Ej: 98">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Diagnóstico</label>
                            <input type="text" name="DiagnosticoRespiratorio" class="form-control" placeholder="Ej: Normal">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Observaciones</label>
                            <textarea name="Observaciones" class="form-control" rows="2"></textarea>
                        </div>
                        <button type="submit" class="btn btn-info w-100"><i class="fas fa-save"></i> Guardar</button>
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
                            <thead><tr><th>Gimnasta</th><th>Fecha</th><th>Cap. Pulmonar</th><th>Sat O2</th><th>Diagnóstico</th></tr></thead>
                            <tbody>
                                @forelse($evaluaciones as $e)
                                <tr>
                                    <td>{{ $e->gimnasta->Nombre ?? '-' }} {{ $e->gimnasta->Apellido ?? '' }}</td>
                                    <td>{{ $e->FechaEvaluacion }}</td>
                                    <td>{{ $e->CapacidadPulmonar ?? '-' }} L</td>
                                    <td>{{ $e->SaturacionOxigeno ?? '-' }}%</td>
                                    <td>{{ $e->DiagnosticoRespiratorio ?? '-' }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="text-center text-muted">Sin registros</td></tr>
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
    new Chart(document.getElementById('chartRespiratorio'),{
        type:'bar',
        data:{
            labels:['Cap. Pulmonar (L)','Sat O2 (%)'],
            datasets:[{
                label:'Promedios',
                data:[{{ $capPromedio ?? 0 }},{{ $satPromedio ?? 0 }}],
                backgroundColor:['#0d6efd','#20c997'],borderRadius:4
            }]
        },
        options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true}}}
    });
    new Chart(document.getElementById('chartRespDiagnostico'),{
        type:'bar',
        data:{
            labels:{!! json_encode($porDiagnostico->pluck('diagnostico')) !!},
            datasets:[{label:'Casos',data:{!! json_encode($porDiagnostico->pluck('count')) !!},backgroundColor:'#0d6efd',borderRadius:4}]
        },
        options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true,ticks:{stepSize:1}}}}
    });
});
</script>
@endif
@endsection
