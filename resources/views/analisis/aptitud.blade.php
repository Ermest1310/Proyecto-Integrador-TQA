@extends('layouts.admin')

@section('title', 'Aptitud Médica')

@section('content')
<div class="container-fluid py-4">
    <div class="alpha-hero-panel mb-4">
        <div class="hero-overlay-grid"></div>
        <div class="p-4 position-relative" style="z-index:2;">
            <h1 class="hero-welcome"><i class="fas fa-heartbeat text-gold me-2"></i>Aptitud Médica</h1>
            <p class="hero-subtitle mb-0">Certificaciones médicas y control de aptitud para entrenamiento</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row g-2 mb-4">
        <div class="col-md-3 col-6"><div class="info-box bg-gradient-info"><span class="info-box-icon"><i class="fas fa-file-medical"></i></span><div class="info-box-content"><span class="info-box-text">Total Certificaciones</span><span class="info-box-number">{{ $total }}</span></div></div></div>
        <div class="col-md-3 col-6"><div class="info-box bg-gradient-success"><span class="info-box-icon"><i class="fas fa-check-circle"></i></span><div class="info-box-content"><span class="info-box-text">Aptos</span><span class="info-box-number">{{ $aptos }}</span></div></div></div>
        <div class="col-md-3 col-6"><div class="info-box bg-gradient-danger"><span class="info-box-icon"><i class="fas fa-times-circle"></i></span><div class="info-box-content"><span class="info-box-text">No Aptos</span><span class="info-box-number">{{ $noAptos }}</span></div></div></div>
        <div class="col-md-3 col-6"><div class="info-box bg-gradient-secondary"><span class="info-box-icon"><i class="fas fa-users"></i></span><div class="info-box-content"><span class="info-box-text">Gimnastas Evaluados</span><span class="info-box-number">{{ $gimnastasEvaluados }}</span></div></div></div>
    </div>

    @if($total > 0)
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card"><div class="card-header bg-primary text-white"><h6 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Aptos vs No Aptos</h6></div><div class="card-body"><canvas id="chartAptitudPie" height="180"></canvas></div></div>
        </div>
        <div class="col-md-6">
            <div class="card"><div class="card-header bg-info text-white"><h6 class="mb-0"><i class="fas fa-list me-2"></i>Diagnósticos Comunes</h6></div><div class="card-body"><canvas id="chartDiagnosticos" height="180"></canvas></div></div>
        </div>
    </div>
    @endif

    <div class="d-flex justify-content-end gap-2 mb-3">
        <a href="{{ route('analisis.aptitud.excel') }}" class="btn btn-success btn-sm"><i class="fas fa-file-excel"></i> Excel</a>
        <a href="{{ route('analisis.aptitud.pdf') }}" class="btn btn-danger btn-sm"><i class="fas fa-file-pdf"></i> PDF</a>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header"><i class="fas fa-plus-circle me-2"></i>Nueva Certificación</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('analisis.aptitud.store') }}">
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
                            <input type="date" name="FechaCertificacion" class="form-control" required value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Diagnóstico</label>
                            <textarea name="Diagnostico" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Restricciones</label>
                            <textarea name="RestriccionesMedicas" class="form-control" rows="2" placeholder="Ej: No realizar ejercicios de impacto"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Médico Responsable</label>
                            <input type="text" name="MedicoResponsable" class="form-control">
                        </div>
                        <div class="form-check mb-3">
                            <input type="checkbox" name="AptoParaEntrenar" class="form-check-input" value="1" checked id="apto">
                            <label class="form-check-label" for="apto">Apto para entrenar</label>
                        </div>
                        <button type="submit" class="btn btn-primary w-100"><i class="fas fa-save"></i> Guardar</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header"><i class="fas fa-list me-2"></i>Certificaciones</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead><tr><th>Gimnasta</th><th>Fecha</th><th>Diagnóstico</th><th>Restricciones</th><th>Apto</th><th>Médico</th></tr></thead>
                            <tbody>
                                @forelse($aptitudes as $a)
                                <tr>
                                    <td>{{ $a->gimnasta->Nombre ?? '-' }} {{ $a->gimnasta->Apellido ?? '' }}</td>
                                    <td>{{ $a->FechaCertificacion }}</td>
                                    <td class="small">{{ Str::limit($a->Diagnostico, 40) }}</td>
                                    <td class="small">{{ Str::limit($a->RestriccionesMedicas, 40) }}</td>
                                    <td>{!! $a->AptoParaEntrenar ? '<span class="badge bg-success">Sí</span>' : '<span class="badge bg-danger">No</span>' !!}</td>
                                    <td>{{ $a->MedicoResponsable ?? '-' }}</td>
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
    new Chart(document.getElementById('chartAptitudPie'),{
        type:'doughnut',
        data:{
            labels:['Aptos','No Aptos'],
            datasets:[{data:[{{ $aptos }},{{ $noAptos }}],backgroundColor:['#28a745','#dc3545']}]
        },
        options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{position:'bottom'}}}
    });
    new Chart(document.getElementById('chartDiagnosticos'),{
        type:'bar',
        data:{
            labels:{!! json_encode($diagnosticos->pluck('diagnostico')) !!},
            datasets:[{label:'Casos',data:{!! json_encode($diagnosticos->pluck('count')) !!},backgroundColor:'#0d6efd',borderRadius:4}]
        },
        options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true,ticks:{stepSize:1}}}}
    });
});
</script>
@endif
@endsection
