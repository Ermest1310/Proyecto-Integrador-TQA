@extends('layouts.admin')

@section('title', 'Evaluación Fisiológica')

@section('content')
<div class="container-fluid py-4">
    <div class="alpha-hero-panel mb-4">
        <div class="hero-overlay-grid"></div>
        <div class="p-4 position-relative" style="z-index:2;">
            <h1 class="hero-welcome"><i class="fas fa-heart-pulse text-gold me-2"></i>Evaluación Fisiológica</h1>
            <p class="hero-subtitle mb-0">Control cardiovascular y signos vitales de atletas</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row g-2 mb-4">
        <div class="col-md-2-4 col-6"><div class="info-box bg-gradient-info"><span class="info-box-icon"><i class="fas fa-file-medical"></i></span><div class="info-box-content"><span class="info-box-text">Total Evaluaciones</span><span class="info-box-number">{{ $total }}</span></div></div></div>
        <div class="col-md-2-4 col-6"><div class="info-box bg-gradient-primary"><span class="info-box-icon"><i class="fas fa-heart"></i></span><div class="info-box-content"><span class="info-box-text">FC Reposo Prom.</span><span class="info-box-number">{{ $fcReposoProm ? number_format($fcReposoProm,0) : 'N/A' }}</span></div></div></div>
        <div class="col-md-2-4 col-6"><div class="info-box bg-gradient-warning"><span class="info-box-icon"><i class="fas fa-heartbeat"></i></span><div class="info-box-content"><span class="info-box-text">FC Esfuerzo Prom.</span><span class="info-box-number">{{ $fcEsfuerzoProm ? number_format($fcEsfuerzoProm,0) : 'N/A' }}</span></div></div></div>
        <div class="col-md-2-4 col-6"><div class="info-box bg-gradient-danger"><span class="info-box-icon"><i class="fas fa-tachometer-alt"></i></span><div class="info-box-content"><span class="info-box-text">Presión Sist./Dias.</span><span class="info-box-number">{{ $sisProm ? number_format($sisProm,0) : 'N/A' }}/{{ $diaProm ? number_format($diaProm,0) : 'N/A' }}</span></div></div></div>
        <div class="col-md-2-4 col-6"><div class="info-box bg-gradient-success"><span class="info-box-icon"><i class="fas fa-percent"></i></span><div class="info-box-content"><span class="info-box-text">Sat O2 Prom.</span><span class="info-box-number">{{ $satProm ? number_format($satProm,1) : 'N/A' }}</span></div></div></div>
    </div>

    @if($total > 0)
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card"><div class="card-header bg-danger text-white"><h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Frecuencia Cardíaca Promedio</h6></div><div class="card-body"><canvas id="chartFisiologiaFC" height="180"></canvas></div></div>
        </div>
        <div class="col-md-6">
            <div class="card"><div class="card-header bg-primary text-white"><h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Signos Vitales Promedio</h6></div><div class="card-body"><canvas id="chartFisiologiaSignos" height="180"></canvas></div></div>
        </div>
    </div>
    @endif

    <div class="d-flex justify-content-end gap-2 mb-3">
        <a href="{{ route('analisis.fisiologica.excel') }}" class="btn btn-success btn-sm"><i class="fas fa-file-excel"></i> Excel</a>
        <a href="{{ route('analisis.fisiologica.pdf') }}" class="btn btn-danger btn-sm"><i class="fas fa-file-pdf"></i> PDF</a>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header"><i class="fas fa-plus-circle me-2"></i>Nueva Evaluación</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('analisis.fisiologica.store') }}">
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
                                <label class="form-label">FC Reposo (lpm)</label>
                                <input type="number" name="FCReposo" class="form-control" placeholder="Ej: 72">
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">FC Esfuerzo (lpm)</label>
                                <input type="number" name="FCEsfuerzo" class="form-control" placeholder="Ej: 145">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label">Presión Sistólica</label>
                                <input type="number" name="PresionSistolica" class="form-control" placeholder="Ej: 120">
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">Presión Diastólica</label>
                                <input type="number" name="PresionDiastolica" class="form-control" placeholder="Ej: 80">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Saturación O2 (%)</label>
                            <input type="number" step="0.1" name="SaturacionOxigeno" class="form-control" placeholder="Ej: 98">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Observaciones</label>
                            <textarea name="Observaciones" class="form-control" rows="2"></textarea>
                        </div>
                        <button type="submit" class="btn btn-danger w-100"><i class="fas fa-save"></i> Guardar</button>
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
                            <thead><tr><th>Gimnasta</th><th>Fecha</th><th>FC Reposo</th><th>FC Esfuerzo</th><th>Presión</th><th>Sat O2</th></tr></thead>
                            <tbody>
                                @forelse($evaluaciones as $e)
                                <tr>
                                    <td>{{ $e->gimnasta->Nombre ?? '-' }} {{ $e->gimnasta->Apellido ?? '' }}</td>
                                    <td>{{ $e->FechaEvaluacion }}</td>
                                    <td>{{ $e->FCReposo ?? '-' }} lpm</td>
                                    <td>{{ $e->FCEsfuerzo ?? '-' }} lpm</td>
                                    <td>{{ $e->PresionSistolica ?? '-' }}/{{ $e->PresionDiastolica ?? '-' }}</td>
                                    <td>{{ $e->SaturacionOxigeno ?? '-' }}%</td>
                                </tr>
                                @empty
                                <tr><td colspan="6" class="text-center text-muted">Sin registros</td></tr>
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
    new Chart(document.getElementById('chartFisiologiaFC'),{
        type:'bar',
        data:{
            labels:['FC Reposo','FC Esfuerzo'],
            datasets:[{
                label:'Promedio (lpm)',
                data:[{{ $fcReposoProm ?? 0 }},{{ $fcEsfuerzoProm ?? 0 }}],
                backgroundColor:['#0d6efd','#dc3545'],borderRadius:4
            }]
        },
        options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true}}}
    });
    new Chart(document.getElementById('chartFisiologiaSignos'),{
        type:'bar',
        data:{
            labels:['Pres. Sistólica','Pres. Diastólica','Sat O2'],
            datasets:[{
                label:'Promedio',
                data:[{{ $sisProm ?? 0 }},{{ $diaProm ?? 0 }},{{ $satProm ?? 0 }}],
                backgroundColor:['#dc3545','#fd7e14','#28a745'],borderRadius:4
            }]
        },
        options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true}}}
    });
});
</script>
@endif
@endsection
