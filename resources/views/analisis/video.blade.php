@extends('layouts.admin')

@section('title', 'Análisis de Video')

@section('content')
<div class="container-fluid py-4">
    <div class="alpha-hero-panel mb-4">
        <div class="hero-overlay-grid"></div>
        <div class="p-4 position-relative" style="z-index: 2;">
            <h1 class="hero-welcome"><i class="fas fa-video text-gold me-2"></i>Subir Video y Analizar Movimiento</h1>
            <p class="hero-subtitle mb-0">Marca puntos clave en el video, calcula ángulos y obtén análisis con IA</p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header"><i class="fas fa-upload me-2"></i>Subir Video</div>
                <div class="card-body">
                    <form id="videoUploadForm" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="video">Selecciona un video (mp4, webm, ogg, mov):</label>
                            <input type="file" name="video" id="video" accept="video/mp4,video/webm,video/ogg,video/quicktime" required class="form-control-file">
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-upload"></i> Subir Video</button>
                    </form>
                    <div id="uploadProgress" class="mt-3" style="display:none;">
                        <div class="progress"><div class="progress-bar" id="progressBar" style="width:0%"></div></div>
                    </div>
                </div>
            </div>

            <div id="videoSection" style="display:none;">
                <div class="card mb-4">
                    <div class="card-header"><i class="fas fa-play-circle me-2"></i>Reproductor</div>
                    <div class="card-body p-0">
                        <div id="video-container" style="position:relative;display:inline-block;width:100%;">
                            <video id="videoPlayer" class="w-100" style="border-radius:8px;background:#000;" controls></video>
                            <canvas id="canvasLines" style="position:absolute;top:0;left:0;pointer-events:none;z-index:5;"></canvas>
                            <canvas id="poseCanvas" style="position:absolute;top:0;left:0;pointer-events:none;z-index:6;display:none;"></canvas>
                        </div>
                    </div>
                </div>

                    <div class="card mb-4">
                        <div class="card-header"><i class="fas fa-sliders-h me-2"></i>Controles</div>
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <button id="playButton" class="btn btn-success"><i class="fas fa-play"></i> Reproducir</button>
                                </div>
                                <div class="col">
                                    <label>Velocidad: <span id="speedValue">1.0</span>x</label>
                                    <input type="range" id="speedRange" min="0.25" max="3" step="0.05" value="1" class="w-100">
                                </div>
                            </div>
                            <hr>
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <button id="togglePoseBtn" class="btn btn-outline-info">
                                        <i class="fas fa-robot"></i> <span id="poseBtnText">Activar MediaPipe Pose</span>
                                    </button>
                                </div>
                                <div class="col">
                                    <small class="text-muted">Detección automática de landmarks corporales con MediaPipe</small>
                                </div>
                            </div>
                            <p class="mt-3 text-muted small"><i class="fas fa-info-circle"></i> Haz clic sobre el video para añadir puntos manualmente. La herramienta calcula ángulos automáticamente.</p>
                        </div>
                    </div>

                <div class="card mb-4">
                    <div class="card-header"><i class="fas fa-drafting-compass me-2"></i>Puntos y Ángulos</div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Puntos Marcados</h6>
                                <ul id="markers-list" class="list-group" style="max-height:300px;overflow-y:auto;"></ul>
                            </div>
                            <div class="col-md-6">
                                <h6>Ángulos Calculados</h6>
                                <div class="mb-3">
                                    <label>Selecciona ángulos a mostrar:</label>
                                    <select id="angleSelect" multiple class="form-control" size="4"></select>
                                </div>
                                <button id="calculateAnglesBtn" class="btn btn-success btn-sm"><i class="fas fa-calculator"></i> Calcular</button>
                                <div id="anglesResult" class="mt-3"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header"><i class="fas fa-robot me-2"></i>Análisis con IA</div>
                    <div class="card-body">
                        <form id="observacionesForm">
                            @csrf
                            <div class="form-group">
                                <label for="observaciones">Aparato:</label>
                                <select name="ID_Aparato" id="aparatoSelect" class="form-control mb-2">
                                    <option value="">Todos los aparatos</option>
                                    @isset($aparatos)
                                        @foreach($aparatos as $a)
                                            <option value="{{ $a->ID_Aparato }}">{{ $a->Nombre }}</option>
                                        @endforeach
                                    @endisset
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="observaciones">Observaciones sobre el rendimiento:</label>
                                <textarea name="observaciones" id="observaciones" class="form-control" rows="4" placeholder="Ej: El gimnasta flexionó las rodillas en el mortal, separación de piernas en el salto, cayó con un paso adelante..."></textarea>
                                <small class="text-muted">Describe los errores observados para que la IA los evalúe según el Código FIG.</small>
                            </div>
                            <button type="submit" class="btn btn-info" id="submitBtn">
                                <i class="fas fa-robot"></i> Obtener Análisis con IA
                            </button>
                        </form>
                        <div class="loading text-center py-3" id="loadingIndicator" style="display:none;">
                            <div class="spinner-border text-info" role="status"></div>
                            <p class="mt-2">Analizando con IA...</p>
                        </div>
                        <div id="aiResponseContainer" class="mt-3"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header"><i class="fas fa-list-ol me-2"></i>Instrucciones</div>
                <div class="card-body">
                    <ol class="list-group list-group-numbered">
                        <li class="list-group-item">Sube un video de la rutina</li>
                        <li class="list-group-item">Reproduce y pausa en el fotograma clave</li>
                        <li class="list-group-item">Haz clic para marcar puntos (hombro, codo, muñeca, etc.)</li>
                        <li class="list-group-item">Los ángulos se calculan automáticamente</li>
                        <li class="list-group-item">Usa el análisis IA para retroalimentación técnica</li>
                    </ol>
                </div>
            </div>

            <div class="alpha-glass-list-card p-4 mb-4">
                <h6><i class="fas fa-lightbulb text-gold me-2"></i>Consejo</h6>
                <p class="text-muted small mb-0">Para mejores resultados, marca al menos 3 puntos consecutivos (ej: cadera-rodilla-tobillo) para calcular el ángulo de la articulación.</p>
            </div>
        </div>
    </div>
</div>

<script>
const video = document.getElementById('videoPlayer');
const container = document.getElementById('video-container');
const markersList = document.getElementById('markers-list');
const anglesResult = document.getElementById('anglesResult');
const calculateBtn = document.getElementById('calculateAnglesBtn');
const speedRange = document.getElementById('speedRange');
const speedValue = document.getElementById('speedValue');
const angleSelect = document.getElementById('angleSelect');
const canvas = document.getElementById('canvasLines');
const ctx = canvas.getContext('2d');
const playButton = document.getElementById('playButton');
const observacionesForm = document.getElementById('observacionesForm');
const submitBtn = document.getElementById('submitBtn');
const loadingIndicator = document.getElementById('loadingIndicator');
const aiResponseContainer = document.getElementById('aiResponseContainer');
const videoUploadForm = document.getElementById('videoUploadForm');
const videoSection = document.getElementById('videoSection');

let points = [];
let angleLabels = [];
let selectedPointIndex = null;
let lastUploadedUrl = '';

videoUploadForm.addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    document.getElementById('uploadProgress').style.display = 'block';
    
    fetch('{{ route("analisis.upload") }}', {
        method: 'POST',
        body: formData,
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            videoSection.style.display = 'block';
            video.src = data.url;
            lastUploadedUrl = data.url;
            document.getElementById('uploadProgress').style.display = 'none';
        } else {
            alert('Error: ' + data.error);
        }
    })
    .catch(() => { alert('Error al subir el video'); });
});

function resizeCanvas() {
    canvas.width = video.clientWidth;
    canvas.height = video.clientHeight;
    canvas.style.width = video.clientWidth + 'px';
    canvas.style.height = video.clientHeight + 'px';
    drawLines();
    positionAllAngleLabels();
}

video.addEventListener('loadedmetadata', resizeCanvas);
window.addEventListener('resize', resizeCanvas);

function percToPixels(point) {
    return { x: (point.xPerc / 100) * video.clientWidth, y: (point.yPerc / 100) * video.clientHeight };
}

function drawLines() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    if (points.length < 2) return;
    ctx.strokeStyle = 'rgba(0, 128, 255, 0.8)';
    ctx.lineWidth = 2;
    ctx.beginPath();
    for (let i = 0; i < points.length; i++) {
        let px = percToPixels(points[i]);
        i === 0 ? ctx.moveTo(px.x, px.y) : ctx.lineTo(px.x, px.y);
    }
    ctx.stroke();
}

function updateMarkersList() {
    markersList.innerHTML = '';
    points.forEach((pt, idx) => {
        const li = document.createElement('li');
        li.className = 'list-group-item d-flex justify-content-between align-items-center py-2';
        li.textContent = `Punto ${idx + 1}: ${pt.label}`;
        li.style.cursor = 'pointer';
        if (idx === selectedPointIndex) { li.style.backgroundColor = '#cce5ff'; li.style.fontWeight = 'bold'; }
        li.addEventListener('click', () => {
            selectedPointIndex = idx;
            updateMarkersList();
            showAngleForPoint(idx);
        });
        li.addEventListener('dblclick', () => {
            const newName = prompt("Nombre del punto:", pt.label);
            if (newName && newName.trim()) {
                pt.label = newName.trim();
                pt.markerElem.textContent = newName.charAt(0).toUpperCase();
                updateMarkersList();
            }
        });
        markersList.appendChild(li);
    });
    updateAngleOptions();
}

function showAngleForPoint(i) {
    if (i < 1 || i >= points.length - 1) {
        anglesResult.innerHTML = `<p class="text-muted">El punto no tiene dos vecinos para calcular ángulo.</p>`;
        return;
    }
    const angle = calcularAngulo(points[i - 1], points[i], points[i + 1]);
    anglesResult.innerHTML = `<p>Ángulo en <b>"${points[i].label}"</b>: <b>${angle}°</b></p>`;
}

function updateAngleOptions() {
    angleSelect.innerHTML = '';
    for (let i = 1; i < points.length - 1; i++) {
        let opt = document.createElement('option');
        opt.value = i;
        opt.textContent = `Ángulo "${points[i].label}" (${points[i-1].label}-${points[i+1].label})`;
        angleSelect.appendChild(opt);
    }
}

container.addEventListener('click', function(e) {
    if (e.target.classList.contains('marker') || e.target.id === 'playButton') return;
    const rect = video.getBoundingClientRect();
    let xPerc = ((e.clientX - rect.left) / video.clientWidth) * 100;
    let yPerc = ((e.clientY - rect.top) / video.clientHeight) * 100;
    const marker = document.createElement('div');
    marker.className = 'marker';
    marker.style.left = xPerc + '%';
    marker.style.top = yPerc + '%';
    marker.textContent = points.length + 1;
    marker.style.cssText += 'position:absolute;background:rgba(255,0,0,0.8);color:white;padding:2px 6px;border-radius:50%;cursor:pointer;font-size:12px;transform:translate(-50%,-50%);font-weight:bold;z-index:10;';
    container.appendChild(marker);
    points.push({ xPerc, yPerc, label: 'Sin nombre', markerElem: marker });
    updateMarkersList();
    drawLines();
    clearAngleLabels();
});

function calcularAngulo(A, B, C) {
    const AB = { x: A.xPerc - B.xPerc, y: A.yPerc - B.yPerc };
    const CB = { x: C.xPerc - B.xPerc, y: C.yPerc - B.yPerc };
    const dot = AB.x * CB.x + AB.y * CB.y;
    const magAB = Math.sqrt(AB.x ** 2 + AB.y ** 2);
    const magCB = Math.sqrt(CB.x ** 2 + CB.y ** 2);
    if (magAB === 0 || magCB === 0) return 0;
    return Math.round(Math.acos(dot / (magAB * magCB)) * 180 / Math.PI);
}

function clearAngleLabels() {
    angleLabels.forEach(l => l.remove());
    angleLabels = [];
}

calculateBtn.addEventListener('click', () => {
    if (points.length < 3) { alert('Necesitas al menos 3 puntos.'); return; }
    let selected = [...angleSelect.selectedOptions];
    if (selected.length === 0) { alert('Selecciona al menos un ángulo.'); return; }
    anglesResult.innerHTML = '';
    clearAngleLabels();
    selected.forEach(opt => {
        let i = parseInt(opt.value);
        const angle = calcularAngulo(points[i - 1], points[i], points[i + 1]);
        const p = document.createElement('p');
        p.innerHTML = `<b>"${points[i].label}"</b>: <b>${angle}°</b>`;
        anglesResult.appendChild(p);
        let pxB = percToPixels(points[i]);
        const label = document.createElement('div');
        label.className = 'angle-label';
        label.textContent = angle + '°';
        label.style.cssText = `position:absolute;background:rgba(0,0,0,0.7);color:#0af;padding:3px 6px;border-radius:4px;font-size:13px;pointer-events:none;z-index:20;white-space:nowrap;font-weight:bold;left:${pxB.x + 10}px;top:${pxB.y - 10}px;`;
        container.appendChild(label);
        angleLabels.push(label);
    });
});

function positionAllAngleLabels() {
    angleLabels.forEach(label => {
        let idx = parseInt(label.dataset.index);
        if (idx && idx >= 1 && idx <= points.length - 2) {
            let pxB = percToPixels(points[idx]);
            label.style.left = (pxB.x + 10) + 'px';
            label.style.top = (pxB.y - 10) + 'px';
        }
    });
}

speedRange.addEventListener('input', () => {
    video.playbackRate = parseFloat(speedRange.value);
    speedValue.textContent = speedRange.value;
});

playButton.addEventListener('click', () => {
    if (video.paused) {
        video.play();
        playButton.innerHTML = '<i class="fas fa-pause"></i> Pausar';
    } else {
        video.pause();
        playButton.innerHTML = '<i class="fas fa-play"></i> Reproducir';
    }
});
video.addEventListener('pause', () => playButton.innerHTML = '<i class="fas fa-play"></i> Reproducir');
video.addEventListener('ended', () => playButton.innerHTML = '<i class="fas fa-play"></i> Reproducir');

observacionesForm.addEventListener('submit', function(e) {
    e.preventDefault();
    const obs = document.getElementById('observaciones').value.trim();
    if (!obs) { showError('Ingresa observaciones para el análisis.'); return; }
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';
    loadingIndicator.style.display = 'block';
    aiResponseContainer.innerHTML = '';
    const formData = new FormData();
    formData.append('observaciones', obs);
    formData.append('_token', '{{ csrf_token() }}');
    const aparatoId = document.getElementById('aparatoSelect').value;
    if (aparatoId) formData.append('ID_Aparato', aparatoId);
    fetch('{{ route("analisis.openai") }}', {
        method: 'POST', body: formData
    })
    .then(r => r.json())
    .then(data => {
        loadingIndicator.style.display = 'none';
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-robot"></i> Obtener Análisis con IA';
        data.success ? showAIResponse(data.response) : showError(data.error);
    })
    .catch(err => {
        loadingIndicator.style.display = 'none';
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-robot"></i> Obtener Análisis con IA';
        showError('Error: ' + err.message);
    });
});

function showAIResponse(text) {
    const div = document.createElement('div');
    div.className = 'alert alert-info';
    div.innerHTML = `<h6><i class="fas fa-brain"></i> Análisis de IA:</h6>${text.replace(/\n/g, '<br>')}`;
    aiResponseContainer.appendChild(div);
}

function showError(msg) {
    const div = document.createElement('div');
    div.className = 'alert alert-danger';
    div.innerHTML = `<h6><i class="fas fa-exclamation-triangle"></i> Error:</h6>${msg}`;
    aiResponseContainer.appendChild(div);
}

/* === MediaPipe Pose Integration === */
const poseCanvas = document.getElementById('poseCanvas');
const poseCtx = poseCanvas.getContext('2d');
const togglePoseBtn = document.getElementById('togglePoseBtn');
const poseBtnText = document.getElementById('poseBtnText');
let poseActive = false;
let poseModel = null;

function resizePoseCanvas() {
    poseCanvas.width = video.clientWidth;
    poseCanvas.height = video.clientHeight;
    poseCanvas.style.width = video.clientWidth + 'px';
    poseCanvas.style.height = video.clientHeight + 'px';
}

video.addEventListener('loadedmetadata', resizePoseCanvas);
window.addEventListener('resize', resizePoseCanvas);

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
                showError('No se pudo cargar MediaPipe Pose. Verifica tu conexión a internet.');
                return;
            }
        }
        if (!video.src) { showError('Primero sube un video.'); return; }
        poseActive = true;
        poseCanvas.style.display = 'block';
        canvas.style.display = 'none';
        poseBtnText.textContent = 'Desactivar MediaPipe';
        togglePoseBtn.classList.remove('btn-outline-info');
        togglePoseBtn.classList.add('btn-info');
        if (!video.paused) processPoseFrame();
    } else {
        poseActive = false;
        poseCanvas.style.display = 'none';
        canvas.style.display = 'block';
        poseCtx.clearRect(0, 0, poseCanvas.width, poseCanvas.height);
        poseBtnText.textContent = 'Activar MediaPipe Pose';
        togglePoseBtn.classList.remove('btn-info');
        togglePoseBtn.classList.add('btn-outline-info');
        if (poseAnimFrame) cancelAnimationFrame(poseAnimFrame);
    }
});

video.addEventListener('play', () => { if (poseActive) processPoseFrame(); });
video.addEventListener('pause', () => { if (poseAnimFrame) cancelAnimationFrame(poseAnimFrame); });
</script>

<script src="/mediapipe/pose.js"></script>

<style>
#video-container { position: relative; display: inline-block; width: 100%; }
#videoPlayer { max-width: 100%; border-radius: 8px; display: block; background: black; }
canvas { position: absolute; top: 0; left: 0; pointer-events: none; z-index: 5; }
#poseCanvas { z-index: 6; }
.marker { z-index: 10; }
.angle-label { z-index: 20; }
</style>
@endsection
