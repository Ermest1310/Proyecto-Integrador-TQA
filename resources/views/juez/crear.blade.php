@extends('layouts.admin')

@section('title', 'Nueva Evaluación')

@section('content')
<div class="container-fluid py-4">
    <div class="alpha-hero-panel mb-4">
        <div class="hero-overlay-grid"></div>
        <div class="p-4 position-relative" style="z-index: 2;">
            <h1 class="hero-welcome"><i class="fas fa-plus-circle text-gold me-2"></i>Nueva Evaluación</h1>
            <p class="hero-subtitle mb-0">Asigna una nueva rutina para evaluar</p>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><i class="fas fa-clipboard-list me-2"></i>Datos de la Evaluación</div>
                <div class="card-body">
                    <form id="createEvalForm">
                        @csrf
                        <div class="form-group">
                            <label>Gimnasta</label>
                            <select name="ID_Gimnasta" class="form-control" required>
                                <option value="">Seleccionar gimnasta...</option>
                                @foreach($gimnastas as $g)
                                    <option value="{{ $g->ID_Usuario }}">{{ $g->Nombre }} {{ $g->Apellido }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Competencia</label>
                            <select name="ID_Competencia" class="form-control" required>
                                <option value="">Seleccionar competencia...</option>
                                @foreach($competencias as $c)
                                    <option value="{{ $c->ID_Competencia }}">{{ $c->NombreCompetencia }} — {{ $c->Fecha ? date('d/m/Y', strtotime($c->Fecha)) : '' }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Aparato</label>
                            <select name="ID_Aparato" class="form-control" required>
                                <option value="">Seleccionar aparato...</option>
                                @foreach($aparatos as $a)
                                    <option value="{{ $a->ID_Aparato }}">{{ $a->Nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Video (opcional)</label>
                            <input type="file" name="video" class="form-control-file" accept="video/mp4,video/webm,video/ogg,video/quicktime">
                            <small class="text-muted">Sube un video de la rutina para analizar</small>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-play"></i> Iniciar Evaluación
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('createEvalForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = this.querySelector('button[type="submit"]');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creando...';
    const formData = new FormData(this);
    fetch('{{ route("juez.store") }}', {
        method: 'POST',
        body: formData,
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            window.location.href = data.redirect;
        } else {
            alert('Error: ' + (data.error || 'Desconocido'));
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-play"></i> Iniciar Evaluación';
        }
    })
    .catch(() => {
        alert('Error de red');
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-play"></i> Iniciar Evaluación';
    });
});
</script>
@endpush
