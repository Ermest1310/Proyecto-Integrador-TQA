@extends('layouts.admin')

@section('title', 'Evaluar Rutina - ' . ($evaluacion->gimnasta->Nombre ?? ''))

@section('content')
<div class="container-fluid py-4">
    <div class="alpha-hero-panel mb-4">
        <div class="hero-overlay-grid"></div>
        <div class="p-4 position-relative" style="z-index: 2;">
            <h1 class="hero-welcome">
                <i class="fas fa-gavel text-gold me-2"></i>
                Evaluar: {{ $evaluacion->gimnasta->Nombre ?? '' }} {{ $evaluacion->gimnasta->Apellido ?? '' }}
            </h1>
            <p class="hero-subtitle mb-0">
                {{ $evaluacion->competencia->NombreCompetencia ?? '' }} —
                {{ $evaluacion->aparato->Nombre ?? '' }}
                @if($evaluacion->estado === 'confirmado')
                    <span class="badge badge-success ml-2">Confirmado</span>
                @elseif($evaluacion->estado === 'en_revision')
                    <span class="badge badge-info ml-2">En Revisión</span>
                @else
                    <span class="badge badge-warning ml-2">Pendiente</span>
                @endif
            </p>
        </div>
    </div>

    <form id="scoreForm" class="row">
        @csrf
        <input type="hidden" name="estado" id="estadoInput" value="{{ $evaluacion->estado === 'confirmado' ? 'confirmado' : 'en_revision' }}">

        <div class="col-lg-7">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-play-circle me-2"></i>Video de la Rutina
                    <div class="card-tools">
                        <button type="button" id="togglePoseBtn" class="btn btn-sm btn-outline-info">
                            <i class="fas fa-robot"></i> <span id="poseBtnText">Activar MediaPipe Pose</span>
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div id="video-container" style="position:relative;width:100%;">
                        @if($videoPath)
                            <video id="videoPlayer" class="w-100" style="border-radius:0;background:#000;max-height:500px;" controls>
                                <source src="{{ asset($videoPath) }}" type="video/mp4">
                            </video>
                        @else
                            <div class="text-center py-5 text-muted">
                                <i class="fas fa-video fa-3x mb-3 d-block"></i>
                                <p>No hay video disponible para esta evaluación.</p>
                            </div>
                        @endif
                        <canvas id="poseCanvas" style="position:absolute;top:0;left:0;pointer-events:none;z-index:6;display:none;width:100%;height:100%;"></canvas>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><i class="fas fa-drafting-compass me-2"></i>Ángulos Detectados</div>
                <div class="card-body">
                    <div class="row" id="anglesContainer">
                        <div class="col-12 text-center text-muted py-3">
                            <i class="fas fa-robot fa-2x mb-2 d-block"></i>
                            Activa MediaPipe Pose y reproduce el video para ver los ángulos en tiempo real.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card mb-4">
                <div class="card-header bg-gradient-dark">
                    <h5 class="mb-0"><i class="fas fa-calculator me-2"></i>Puntuación</h5>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label><i class="fas fa-star text-warning me-1"></i>Nota de Dificultad</label>
                        <div class="input-group">
                            <input type="range" id="nota_dificultad" name="nota_dificultad"
                                   class="form-control-range w-75" min="0" max="10" step="0.1"
                                   value="{{ $evaluacion->nota_dificultad ?? 0 }}">
                            <input type="number" id="nota_dificultad_val" class="form-control w-25 text-center font-weight-bold"
                                   min="0" max="10" step="0.1"
                                   value="{{ $evaluacion->nota_dificultad ?? 0 }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-running text-info me-1"></i>Nota de Ejecución</label>
                        <div class="input-group">
                            <input type="range" id="nota_ejecucion" name="nota_ejecucion"
                                   class="form-control-range w-75" min="0" max="10" step="0.1"
                                   value="{{ $evaluacion->nota_ejecucion ?? 0 }}">
                            <input type="number" id="nota_ejecucion_val" class="form-control w-25 text-center font-weight-bold"
                                   min="0" max="10" step="0.1"
                                   value="{{ $evaluacion->nota_ejecucion ?? 0 }}">
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between mb-2">
                        <span>Descuentos:</span>
                        <span id="totalDescuentosDisplay" class="text-danger font-weight-bold">0.00</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="h5">Nota Final:</span>
                        <span id="notaFinalDisplay" class="h5 text-gold font-weight-bold">0.00</span>
                    </div>
                    <input type="hidden" name="total_descuentos" id="totalDescuentos" value="0">

                    <div class="form-group">
                        <label><i class="fas fa-comment me-1"></i>Observaciones</label>
                        <textarea name="observaciones" id="observaciones" class="form-control" rows="3"
                                  placeholder="Comentarios sobre la rutina...">{{ $evaluacion->observaciones ?? '' }}</textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill" id="saveScoreBtn">
                            <i class="fas fa-save"></i> Guardar Evaluación
                        </button>
                        @if($evaluacion->estado !== 'confirmado')
                            <button type="button" class="btn btn-success flex-fill" id="confirmBtn">
                                <i class="fas fa-check-circle"></i> Confirmar
                            </button>
                        @endif
                        <a href="{{ route('juez.dashboard') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-minus-circle text-danger me-2"></i>Deducciones</span>
                    <button type="button" class="btn btn-sm btn-outline-danger" data-toggle="modal" data-target="#deductionModal">
                        <i class="fas fa-plus"></i> Agregar
                    </button>
                </div>
                <div class="card-body p-0" id="deductionsList">
                    @forelse($evaluacion->descuentos as $d)
                    <div class="deduction-item p-3 border-bottom" data-id="{{ $d->ID_Descuento }}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <strong class="text-danger">-{{ number_format($d->descuento_final, 2) }} pts</strong>
                                <span class="badge badge-secondary ml-2">{{ $d->tipo_error }}</span>
                                @if($d->nombre_elemento)
                                    <br><small class="text-muted">{{ $d->nombre_elemento }}</small>
                                @endif
                                @if($d->angulo_detectado)
                                    <br><small class="text-info">Ángulo: {{ $d->angulo_detectado }}°</small>
                                @endif
                                <br><small>{{ $d->descripcion_error }}</small>
                            </div>
                            <button type="button" class="btn btn-sm btn-link text-muted delete-deduction" data-id="{{ $d->ID_Descuento }}">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-check-circle fa-2x mb-2 d-block"></i>
                        Sin deducciones aún
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </form>
</div>

<div class="card mb-4" id="aiSuggestionCard" style="display:none;">
    <div class="card-header bg-gradient-ai">
        <i class="fas fa-brain me-2"></i>Sugerencia del Juez IA
        <span class="badge badge-light float-right" id="aiConfidenceBadge">--</span>
    </div>
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="h5 text-danger mb-0">Deducción Estimada: <span id="aiDeduccionValue">--</span> pts</span>
            <small class="text-muted" id="aiSourceBadge">--</small>
        </div>
        <div class="mb-2" id="aiMessage" style="font-size:0.9rem;"></div>
        <div id="aiDetails" style="font-size:0.85rem;"></div>
        <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="aiApplyBtn">
            <i class="fas fa-check"></i> Usar en Deducción
        </button>
    </div>
</div>

<!-- Modal Agregar Deducción -->
<div class="modal fade" id="deductionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-minus-circle text-danger me-2"></i>Agregar Deducción</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="deductionForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Tipo de Error</label>
                        <select name="tipo_error" id="ded_tipo_error" class="form-control" required>
                            <option value="">Seleccionar...</option>
                            <option value="caida">Caída</option>
                            <option value="paso">Paso</option>
                            <option value="perdida_equilibrio">Pérdida de Equilibrio</option>
                            <option value="separacion_linea">Separación de Línea</option>
                            <option value="falta_tecnica">Falta Técnica</option>
                            <option value="otro">Otro</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Deducciones Predefinidas</label>
                        <select id="predefinedDeductions" class="form-control">
                            <option value="">-- Personalizada --</option>
                            @foreach($deducciones as $ded)
                                <option value="{{ $ded->Valor_Deduccion }}"
                                        data-desc="{{ $ded->Descripcion }}"
                                        data-tipo="{{ $ded->Tipo }}">
                                    [{{ number_format($ded->Valor_Deduccion, 2) }}] {{ $ded->Descripcion }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Nombre del Elemento</label>
                        <input type="text" name="nombre_elemento" id="ded_nombre_elemento" class="form-control" placeholder="Ej: Mortal adelante">
                    </div>

                    <div class="form-group">
                        <label>Descripción del Error</label>
                        <textarea name="descripcion_error" id="ded_descripcion" class="form-control" rows="2" required></textarea>
                    </div>

                    <div class="form-group">
                        <label>Valor del Descuento</label>
                        <div class="input-group">
                            <input type="number" name="descuento_final" id="ded_valor"
                                   class="form-control" min="0" max="10" step="0.1" value="0.1" required>
                            <div class="input-group-append"><span class="input-group-text">pts</span></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Ángulo Detectado (opcional)</label>
                        <input type="number" name="angulo_detectado" id="ded_angulo"
                               class="form-control" step="0.1" placeholder="Ej: 145.5">
                    </div>

                    <div class="form-group">
                        <label>Justificación</label>
                        <textarea name="justificacion" id="ded_justificacion" class="form-control" rows="2"
                                  placeholder="Justificación técnica del descuento..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger"><i class="fas fa-plus"></i> Agregar Deducción</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="/mediapipe/pose.js"></script>
<script>
// ===== VIDEO PLAYER =====
const video = document.getElementById('videoPlayer');
const container = document.getElementById('video-container');

// ===== MEDIAPIPE POSE =====
const poseCanvas = document.getElementById('poseCanvas');
const poseCtx = poseCanvas.getContext('2d');
const togglePoseBtn = document.getElementById('togglePoseBtn');
const poseBtnText = document.getElementById('poseBtnText');
let poseActive = false;
let poseModel = null;

function drawConnectors(ctx, landmarks, connections, color) {
    ctx.strokeStyle = color || '#00FF00';
    ctx.lineWidth = 2;
    for (const [i, j] of connections) {
        const a = landmarks[i];
        const b = landmarks[j];
        if (a && b && a.visibility !== 0 && b.visibility !== 0) {
            ctx.beginPath();
            ctx.moveTo(a.x * poseCanvas.width, a.y * poseCanvas.height);
            ctx.lineTo(b.x * poseCanvas.width, b.y * poseCanvas.height);
            ctx.stroke();
        }
    }
}

function drawLandmarks(ctx, landmarks, color) {
    ctx.fillStyle = color || '#FF0000';
    for (const lm of landmarks) {
        if (lm.visibility !== 0) {
            ctx.beginPath();
            ctx.arc(lm.x * poseCanvas.width, lm.y * poseCanvas.height, 3, 0, 2 * Math.PI);
            ctx.fill();
        }
    }
}

function resizePoseCanvas() {
    poseCanvas.width = video.clientWidth;
    poseCanvas.height = video.clientHeight;
    poseCanvas.style.width = video.clientWidth + 'px';
    poseCanvas.style.height = video.clientHeight + 'px';
}

if (video) {
    video.addEventListener('loadedmetadata', resizePoseCanvas);
    window.addEventListener('resize', resizePoseCanvas);

    async function loadPoseModel() {
        try {
            const pose = new Pose({
                locateFile: (file) => `/mediapipe/${file}`
            });
            pose.setOptions({
                modelComplexity: 1,
                smoothLandmarks: true,
                minDetectionConfidence: 0.5,
                minTrackingConfidence: 0.5
            });
            pose.onResults((results) => {
                if (!poseActive) return;
                poseCtx.clearRect(0, 0, poseCanvas.width, poseCanvas.height);
                if (results.poseLandmarks) {
                    drawConnectors(poseCtx, results.poseLandmarks, Pose.POSE_CONNECTIONS, '#00FF00');
                    drawLandmarks(poseCtx, results.poseLandmarks, '#FF0000');
                    updateAngles(results.poseLandmarks);
                }
            });
            poseModel = pose;
            return true;
        } catch (e) {
            console.error('Error loading MediaPipe Pose:', e);
            return false;
        }
    }

    let poseAnimFrame = null;
    async function processPoseFrame() {
        if (!poseActive || video.paused || video.ended) {
            if (poseAnimFrame) cancelAnimationFrame(poseAnimFrame);
            return;
        }
        try {
            if (poseModel) await poseModel.send({ image: video });
        } catch (e) { /* ignore */ }
        poseAnimFrame = requestAnimationFrame(processPoseFrame);
    }

    togglePoseBtn.addEventListener('click', async () => {
        if (!poseActive) {
            if (!poseModel) {
                togglePoseBtn.disabled = true;
                poseBtnText.textContent = 'Cargando...';
                const loaded = await loadPoseModel();
                togglePoseBtn.disabled = false;
                if (!loaded) {
                    alert('No se pudo cargar MediaPipe Pose.');
                    return;
                }
            }
            poseActive = true;
            poseCanvas.style.display = 'block';
            poseBtnText.textContent = 'Desactivar Pose';
            togglePoseBtn.classList.remove('btn-outline-info');
            togglePoseBtn.classList.add('btn-info');
            if (!video.paused) processPoseFrame();
        } else {
            poseActive = false;
            poseCanvas.style.display = 'none';
            poseCtx.clearRect(0, 0, poseCanvas.width, poseCanvas.height);
            poseBtnText.textContent = 'Activar MediaPipe Pose';
            togglePoseBtn.classList.remove('btn-info');
            togglePoseBtn.classList.add('btn-outline-info');
            if (poseAnimFrame) cancelAnimationFrame(poseAnimFrame);
        }
    });

    video.addEventListener('play', () => { if (poseActive) processPoseFrame(); });
    video.addEventListener('pause', () => { if (poseAnimFrame) cancelAnimationFrame(poseAnimFrame); });
}

// ===== KEY ANGLE CALCULATION (from MediaPipe landmarks) =====
function calcAngle(A, B, C) {
    const ab = { x: A.x - B.x, y: A.y - B.y };
    const cb = { x: C.x - B.x, y: C.y - B.y };
    const dot = ab.x * cb.x + ab.y * cb.y;
    const magAB = Math.sqrt(ab.x ** 2 + ab.y ** 2);
    const magCB = Math.sqrt(cb.x ** 2 + cb.y ** 2);
    if (magAB === 0 || magCB === 0) return 0;
    const angle = Math.acos(dot / (magAB * magCB)) * 180 / Math.PI;
    return Math.round(angle);
}

const ANGLE_DEFS = {
    'Codo Izq': [11, 13, 15],
    'Codo Der': [12, 14, 16],
    'Rodilla Izq': [23, 25, 27],
    'Rodilla Der': [24, 26, 28],
    'Cadera Izq': [11, 23, 25],
    'Cadera Der': [12, 24, 26],
    'Hombro Izq': [13, 11, 23],
    'Hombro Der': [14, 12, 24],
    'Tobillo Izq': [25, 27, 29],
    'Tobillo Der': [26, 28, 30],
    'Tronco Izq': [11, 23, 27],
    'Tronco Der': [12, 24, 28],
};

const ANGLE_KEY_MAP = {
    'Codo Izq': 'codo_izq',
    'Codo Der': 'codo_der',
    'Rodilla Izq': 'rodilla_izq',
    'Rodilla Der': 'rodilla_der',
    'Cadera Izq': 'cadera_izq',
    'Cadera Der': 'cadera_der',
    'Hombro Izq': 'hombro_izq',
    'Hombro Der': 'hombro_der',
    'Tobillo Izq': 'tobillo_izq',
    'Tobillo Der': 'tobillo_der',
    'Tronco Izq': 'tronco_izq',
    'Tronco Der': 'tronco_der',
};

let currentAngles = {};

function updateAngles(landmarks) {
    const container = document.getElementById('anglesContainer');
    container.innerHTML = '';
    let row;
    Object.entries(ANGLE_DEFS).forEach(([name, indices], i) => {
        const [a, b, c] = indices.map(idx => landmarks[idx]);
        if (a && b && c && a.visibility !== 0) {
            const angle = calcAngle(a, b, c);
            currentAngles[ANGLE_KEY_MAP[name]] = angle;
            if (i % 2 === 0) {
                row = document.createElement('div');
                row.className = 'row mb-2';
                container.appendChild(row);
            }
            const col = document.createElement('div');
            col.className = 'col-6';
            col.innerHTML = `
                <div class="d-flex justify-content-between align-items-center p-2 rounded" style="background:rgba(0,0,0,0.05);">
                    <strong>${name}</strong>
                    <span class="badge badge-primary badge-lg" style="font-size:1rem;">${angle}°</span>
                </div>
            `;
            row.appendChild(col);
        }
    });
    if (container.children.length === 0) {
        container.innerHTML = '<div class="col-12 text-center text-muted py-2"><small>No se detectaron landmarks</small></div>';
    }
    schedulePrediction();
}

// ===== SCORE CALCULATION =====
const diffSlider = document.getElementById('nota_dificultad');
const diffInput = document.getElementById('nota_dificultad_val');
const ejecSlider = document.getElementById('nota_ejecucion');
const ejecInput = document.getElementById('nota_ejecucion_val');
const totalDescDisplay = document.getElementById('totalDescuentosDisplay');
const totalDescHidden = document.getElementById('totalDescuentos');
const notaFinalDisplay = document.getElementById('notaFinalDisplay');

function updateScore() {
    const diff = parseFloat(diffSlider.value) || 0;
    const ejec = parseFloat(ejecSlider.value) || 0;
    const desc = parseFloat(totalDescHidden.value) || 0;
    const final = Math.max(0, diff + ejec - desc);
    diffInput.value = diff.toFixed(1);
    ejecInput.value = ejec.toFixed(1);
    totalDescDisplay.textContent = desc.toFixed(2);
    notaFinalDisplay.textContent = final.toFixed(2);
}

diffSlider.addEventListener('input', updateScore);
diffInput.addEventListener('input', () => {
    const v = Math.min(10, Math.max(0, parseFloat(diffInput.value) || 0));
    diffSlider.value = v;
    updateScore();
});
ejecSlider.addEventListener('input', updateScore);
ejecInput.addEventListener('input', () => {
    const v = Math.min(10, Math.max(0, parseFloat(ejecInput.value) || 0));
    ejecSlider.value = v;
    updateScore();
});

function recalcTotalDescuentos() {
    let total = 0;
    document.querySelectorAll('.deduction-item').forEach(el => {
        const val = parseFloat(el.dataset.valor) || 0;
        total += val;
    });
    totalDescHidden.value = total.toFixed(2);
    updateScore();
}

// ===== DEDUCTION MODAL =====
document.getElementById('predefinedDeductions').addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];
    if (selected.value) {
        document.getElementById('ded_valor').value = selected.value;
        document.getElementById('ded_descripcion').value = selected.dataset.desc || '';
        document.getElementById('ded_tipo_error').value = selected.dataset.tipo || 'falta_tecnica';
    }
});

document.getElementById('deductionForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const angulos = Object.keys(currentAngles).length >= 12 ? currentAngles : null;
    if (angulos) {
        formData.append('angulos_json', JSON.stringify(angulos));
    }
    formData.append('ID_Aparato', '{{ $evaluacion->ID_Aparato }}');
    if (window.lastAiPrediction) {
        formData.append('descuento_ia_propuesto', window.lastAiPrediction.deduccion_estimada);
        formData.append('confianza_ia', window.lastAiPrediction.confianza);
    }
    fetch('{{ route("juez.deduction.store", $evaluacion->ID_EvaluacionJuez) }}', {
        method: 'POST',
        body: formData,
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            if (angulos) {
                enviarFeedbackJuez(angulos, parseFloat(formData.get('descuento_final')), formData.get('tipo_error'));
            }
            location.reload();
        } else {
            alert('Error: ' + (data.error || 'Desconocido'));
        }
    })
    .catch(() => alert('Error de red'));
});

function enviarFeedbackJuez(angulos, descuentoJuez, tipoError) {
    fetch('/api/ia/registrar-feedback', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            angulos: angulos,
            descuento_juez: descuentoJuez,
            tipo_error: tipoError,
            ID_EvaluacionJuez: '{{ $evaluacion->ID_EvaluacionJuez }}',
            ID_Aparato: '{{ $evaluacion->ID_Aparato }}',
            descuento_ia_propuesto: window.lastAiPrediction ? window.lastAiPrediction.deduccion_estimada : null
        })
    })
    .then(r => r.json())
    .then(d => {
        if (d.success && window.toastr) {
            toastr.info(d.mensaje || 'Feedback registrado');
        }
    })
    .catch(() => {});
}

// ===== JUEZ IA: PREDICCIÓN ASÍNCRONA =====
let predictionTimer = null;
const PREDICTION_INTERVAL = 2500;
let lastPredictionAt = 0;
let predictionInFlight = false;

function schedulePrediction() {
    if (Object.keys(currentAngles).length < 12) return;
    const now = Date.now();
    if (now - lastPredictionAt < PREDICTION_INTERVAL) return;
    if (predictionInFlight) return;
    lastPredictionAt = now;
    predictionInFlight = true;
    requestPrediction();
}

function requestPrediction() {
    const tipoError = document.getElementById('ded_tipo_error').value || null;
    fetch('/api/ia/predecir-deduccion', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            angulos: currentAngles,
            ID_Aparato: {{ $evaluacion->ID_Aparato }},
            tipo_error: tipoError
        })
    })
    .then(r => r.json())
    .then(data => {
        predictionInFlight = false;
        if (data.success && data.deduccion_estimada !== null) {
            window.lastAiPrediction = data;
            renderAiSuggestion(data);
        }
    })
    .catch(() => { predictionInFlight = false; });
}

function renderAiSuggestion(data) {
    const card = document.getElementById('aiSuggestionCard');
    card.style.display = 'block';
    document.getElementById('aiDeduccionValue').textContent = data.deduccion_estimada.toFixed(2);
    document.getElementById('aiMessage').textContent = data.mensaje || '';
    const fuenteLabels = {
        knn_historico: 'k-NN Histórico',
        reglas_fig_heuristico: 'Reglas FIG',
        sin_datos: 'Sin datos',
        error: 'Error'
    };
    document.getElementById('aiSourceBadge').textContent = fuenteLabels[data.fuente] || data.fuente;
    const badge = document.getElementById('aiConfidenceBadge');
    const conf = data.confianza || 0;
    badge.textContent = 'Confianza: ' + conf + '%';
    if (conf >= 70) {
        badge.className = 'badge badge-success float-right';
    } else if (conf >= 40) {
        badge.className = 'badge badge-warning float-right';
    } else {
        badge.className = 'badge badge-danger float-right';
    }
    let details = '';
    if (data.tipo_error_sugerido) {
        details += '<div><i class="fas fa-tag me-1"></i>Tipo sugerido: <strong>' + data.tipo_error_sugerido + '</strong></div>';
    }
    if (data.elemento_referencia) {
        details += '<div><i class="fas fa-fingerprint me-1"></i>Elemento similar: ' + data.elemento_referencia + '</div>';
    }
    if (data.articulo_critico) {
        details += '<div><i class="fas fa-exclamation-triangle me-1 text-warning"></i>Artículo crítico: ' + data.articulo_critico + ' (' + data.desviacion_grados + '° de desviación)</div>';
    }
    if (data.vecinos_consultados) {
        details += '<div class="text-muted"><i class="fas fa-database me-1"></i>' + data.vecinos_consultados + ' muestras consultadas · distancia mín: ' + (data.distancia_minima || 0) + '</div>';
    }
    document.getElementById('aiDetails').innerHTML = details;
}

document.getElementById('aiApplyBtn').addEventListener('click', function() {
    if (!window.lastAiPrediction) return;
    document.getElementById('ded_valor').value = window.lastAiPrediction.deduccion_estimada;
    if (window.lastAiPrediction.tipo_error_sugerido) {
        document.getElementById('ded_tipo_error').value = window.lastAiPrediction.tipo_error_sugerido;
    }
    if (window.lastAiPrediction.muestra_mas_cercana && window.lastAiPrediction.muestra_mas_cercana.angulo_detectado) {
        document.getElementById('ded_angulo').value = window.lastAiPrediction.muestra_mas_cercana.angulo_detectado;
    }
    if (window.lastAiPrediction.elemento_referencia) {
        document.getElementById('ded_nombre_elemento').value = window.lastAiPrediction.elemento_referencia;
    }
    document.getElementById('ded_justificacion').value = 'Sugerencia IA (confianza ' + window.lastAiPrediction.confianza + '%): ' + (window.lastAiPrediction.mensaje || '');
    $('#deductionModal').modal('show');
});

// ===== DELETE DEDUCTION =====
document.addEventListener('click', function(e) {
    const btn = e.target.closest('.delete-deduction');
    if (!btn) return;
    if (!confirm('¿Eliminar esta deducción?')) return;
    const dedId = btn.dataset.id;
    fetch('{{ route("juez.deduction.delete", [$evaluacion->ID_EvaluacionJuez, "DED_ID"]) }}'.replace('DED_ID', dedId), {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) location.reload();
        else alert('Error al eliminar');
    });
});

// ===== SAVE SCORE =====
document.getElementById('scoreForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    document.getElementById('saveScoreBtn').disabled = true;
    document.getElementById('saveScoreBtn').innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
    fetch('{{ route("juez.score.store", $evaluacion->ID_EvaluacionJuez) }}', {
        method: 'POST',
        body: formData,
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    })
    .then(r => r.json())
    .then(data => {
        document.getElementById('saveScoreBtn').disabled = false;
        document.getElementById('saveScoreBtn').innerHTML = '<i class="fas fa-save"></i> Guardar Evaluación';
        if (data.success) {
            toastr?.success?.(data.message) || alert(data.message);
        } else {
            alert('Error: ' + (data.error || 'Desconocido'));
        }
    })
    .catch(() => {
        document.getElementById('saveScoreBtn').disabled = false;
        document.getElementById('saveScoreBtn').innerHTML = '<i class="fas fa-save"></i> Guardar Evaluación';
        alert('Error de red');
    });
});

document.getElementById('confirmBtn')?.addEventListener('click', function() {
    if (!confirm('¿Confirmar esta evaluación? Ya no podrá ser editada.')) return;
    document.getElementById('estadoInput').value = 'confirmado';
    document.getElementById('scoreForm').dispatchEvent(new Event('submit'));
});
</script>
@endpush

@push('styles')
<style>
#video-container { position: relative; width: 100%; }
#videoPlayer { max-width: 100%; display: block; background: black; }
#poseCanvas { position: absolute; top: 0; left: 0; pointer-events: none; z-index: 6; }
.text-gold { color: #ffc107; }
.form-control-range { display: inline-block; vertical-align: middle; }
.gap-2 { gap: 0.5rem; }
.deduction-item:last-child { border-bottom: none !important; }
.deduction-item:hover { background: rgba(0,0,0,0.02); }
.bg-gradient-dark { background: linear-gradient(135deg, #1a1a2e, #16213e); color: white; }
.bg-gradient-ai { background: linear-gradient(135deg, #4a148c, #7b1fa2); color: white; }
#aiSuggestionCard .card-header { border: none; }
#aiSuggestionCard .badge { font-size: 0.75rem; }
.table td { vertical-align: middle; }
</style>
@endpush
