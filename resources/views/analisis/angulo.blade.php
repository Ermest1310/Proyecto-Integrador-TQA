@extends('layouts.admin')

@section('title', 'Analizador de Ángulos')

@section('content')
<style>
.video-wrap { position: relative; background: #000; border-radius: 12px 12px 0 0; overflow: hidden; }
.video-wrap video, .video-wrap canvas { width: 100%; display: block; }
.video-wrap canvas { position: absolute; top: 0; left: 0; }
#poseCanvas { z-index: 5; pointer-events: none; }
#manualCanvas { z-index: 6; cursor: crosshair; }
.stat-badge { background: var(--alpha-navy-dark); color: white; padding: 15px; border-radius: 10px; text-align: center; }
.stat-badge .num { font-size: 1.8rem; font-weight: 800; }
.stat-badge .lbl { font-size: 0.7rem; text-transform: uppercase; opacity: 0.8; }
.fig-angle-perfecto { color: #10b981; }
.fig-angle-aceptable { color: #3b82f6; }
.fig-angle-deficiente { color: #f59e0b; }
.fig-angle-critico { color: #ef4444; }

/* Progress bar YouTube-style */
#progressBarContainer {
    position: relative; width: 100%; height: 30px; background: #222; cursor: pointer; user-select: none;
}
#progressTrack { position: absolute; top: 50%; left: 8px; right: 8px; height: 4px; background: #555; transform: translateY(-50%); border-radius: 2px; }
#progressBarContainer:hover #progressTrack { height: 6px; }
#progressBuffered { position: absolute; top: 0; left: 0; height: 100%; background: #777; border-radius: 2px; }
#progressFill { position: absolute; top: 0; left: 0; height: 100%; background: linear-gradient(90deg, #4e73df, #36b9cc); border-radius: 2px; }
#progressThumb {
    position: absolute; top: 50%; width: 14px; height: 14px; background: #fff; border-radius: 50%;
    transform: translate(-50%, -50%); box-shadow: 0 1px 4px rgba(0,0,0,0.4); opacity: 0; transition: opacity 0.15s;
    pointer-events: none; z-index: 2;
}
#progressBarContainer:hover #progressThumb { opacity: 1; }
#progressTimeTooltip {
    position: absolute; bottom: 34px; background: rgba(0,0,0,0.8); color: #fff; padding: 2px 6px;
    border-radius: 3px; font-size: 11px; white-space: nowrap; pointer-events: none; display: none; z-index: 5;
}
#progressTimeDisplay {
    position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
    color: #aaa; font-size: 11px; font-family: monospace; pointer-events: none; z-index: 3;
}

/* Speed slider moderno */
.speed-slider-wrap { display: flex; align-items: center; gap: 15px; }
.speed-slider-wrap label { margin: 0; font-weight: 600; font-size: 0.85rem; white-space: nowrap; min-width: 50px; }
.speed-slider-wrap input[type=range] {
    -webkit-appearance: none; appearance: none; flex: 1; height: 8px; border-radius: 4px;
    background: linear-gradient(90deg, #4e73df 0%, #1cc88a 33%, #f6c23e 66%, #e74a3b 100%);
    outline: none; cursor: pointer;
}
.speed-slider-wrap input[type=range]::-webkit-slider-thumb {
    -webkit-appearance: none; appearance: none; width: 20px; height: 20px; border-radius: 50%;
    background: #fff; border: 3px solid #4e73df; cursor: pointer; box-shadow: 0 1px 4px rgba(0,0,0,0.3);
}
.speed-slider-wrap input[type=range]::-webkit-slider-thumb:hover { transform: scale(1.15); }
.speed-slider-wrap input[type=range]::-moz-range-thumb { width: 20px; height: 20px; border-radius: 50%; background: #fff; border: 3px solid #4e73df; cursor: pointer; }
.speed-display {
    font-size: 1.4rem; font-weight: 700; min-width: 55px; text-align: center;
    background: linear-gradient(135deg, #4e73df, #36b9cc); -webkit-background-clip: text; -webkit-text-fill-color: transparent;
    background-clip: text;
}
.speed-labels { display: flex; justify-content: space-between; font-size: 10px; color: #858796; margin: 2px 15px 0; }

.pose-debug-row { font-size: 11px; color: #858796; margin-top: 4px; }
</style>

<div class="container-fluid py-4">
    <div class="alpha-hero-panel mb-4">
        <div class="hero-overlay-grid"></div>
        <div class="p-4 position-relative" style="z-index:2;">
            <h1 class="hero-welcome"><i class="fas fa-drafting-compass text-gold me-2"></i>Analizador de Ángulos FIG</h1>
            <p class="hero-subtitle mb-0">Detección automática de landmarks con MediaPipe + análisis comparativo contra el Código FIG</p>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="stat-badge col"><div class="num" id="totalPuntos">0</div><div class="lbl">Manuales</div></div>
        <div class="stat-badge col"><div class="num" id="tiempoActual">0.00s</div><div class="lbl">Tiempo</div></div>
        <div class="stat-badge col"><div class="num" id="velocidadActual">1.0x</div><div class="lbl">Velocidad</div></div>
        <div class="stat-badge col"><div class="num" id="totalAngulos">0</div><div class="lbl">Ángulos FIG</div></div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                    <span><i class="fas fa-video me-2"></i>Cargar Video</span>
                    <div>
                        <button id="togglePoseBtn" class="btn btn-sm btn-outline-info">
                            <i class="fas fa-robot"></i> <span id="poseBtnText">Activar MediaPipe Pose</span>
                        </button>
                        <button id="btnDebugPose" class="btn btn-sm btn-outline-secondary" title="Diagnóstico Pose">
                            <i class="fas fa-bug"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="file-input">
                        <input type="file" id="inputVideo" accept="video/*" class="d-none">
                        <label for="inputVideo" class="btn btn-primary"><i class="fas fa-upload"></i> Seleccionar Video</label>
                        <span id="fileName" class="ms-2 text-muted"></span>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><i class="fas fa-play-circle me-2"></i>Reproductor</div>
                <div class="card-body p-0">
                    <div class="video-wrap">
                        <video id="video" playsinline muted></video>
                        <canvas id="poseCanvas"></canvas>
                        <canvas id="manualCanvas"></canvas>
                    </div>
                    <div id="progressBarContainer">
                        <div id="progressTimeTooltip">0:00</div>
                        <div id="progressTrack">
                            <div id="progressBuffered"></div>
                            <div id="progressFill"></div>
                        </div>
                        <div id="progressThumb"></div>
                        <div id="progressTimeDisplay">0:00 / 0:00</div>
                    </div>
                </div>
                <div class="card-footer py-2">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div class="btn-group">
                            <button id="btnPlay" class="btn btn-primary"><i class="fas fa-play"></i></button>
                            <button id="btnPause" class="btn btn-warning"><i class="fas fa-pause"></i></button>
                            <button id="btnRewind" class="btn btn-outline-secondary" title="Retroceder 5s"><i class="fas fa-backward"></i></button>
                            <button id="btnForward" class="btn btn-outline-secondary" title="Avanzar 5s"><i class="fas fa-forward"></i></button>
                            <button id="btnStepBack" class="btn btn-outline-secondary" title="Cuadro anterior"><i class="fas fa-step-backward"></i></button>
                            <button id="btnStepFwd" class="btn btn-outline-secondary" title="Siguiente cuadro"><i class="fas fa-step-forward"></i></button>
                            <button id="btnClear" class="btn btn-danger"><i class="fas fa-trash" title="Limpiar puntos manuales"></i></button>
                            <button id="btnReset" class="btn btn-secondary"><i class="fas fa-redo" title="Resetear"></i></button>
                        </div>
                        <small class="text-muted" id="poseStatus"><i class="fas fa-circle mr-1" style="color:#858796;"></i> Pose: inactivo</small>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><i class="fas fa-tachometer-alt me-2"></i>Control de Velocidad</div>
                <div class="card-body">
                    <div class="speed-slider-wrap">
                        <label><i class="fas fa-turtle"></i></label>
                        <div style="flex:1">
                            <input type="range" id="speedSlider" min="0.1" max="2.0" step="0.05" value="1.0">
                            <div class="speed-labels">
                                <span>0.1x</span><span>0.5x</span><span>1.0x</span><span>1.5x</span><span>2.0x</span>
                            </div>
                        </div>
                        <div class="speed-display" id="speedDisplay">1.00x</div>
                    </div>
                    <div class="pose-debug-row" id="poseDebugInfo"></div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="alpha-glass-list-card p-3 mb-4">
                <h6><i class="fas fa-robot text-info me-2"></i>Detección FIG</h6>
                <div id="figPositionPanel" class="small">
                    <p class="text-muted text-center py-3 mb-0">Activa MediaPipe Pose y reproduce el video</p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><i class="fas fa-drafting-compass me-2"></i>Ángulos FIG (vs ideal)</div>
                <div class="card-body p-2" id="figAnglesPanel">
                    <p class="text-muted text-center py-3 mb-0 small">Los ángulos detectados por MediaPipe aparecerán aquí<br>comparados con los valores ideales FIG</p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><i class="fas fa-hand-pointer me-2"></i>Ángulos Manuales</div>
                <div class="card-body" id="angulosActuales">
                    <p class="text-muted text-center py-4">Marca 3+ puntos para ver ángulos</p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><i class="fas fa-info-circle me-2"></i>Referencia FIG</div>
                <div class="card-body p-2 small">
                    <table class="table table-sm mb-0">
                        <thead><tr><th>Posición</th><th>Ideal</th></tr></thead>
                        <tbody>
                            <tr><td>Parada de Manos</td><td>180°</td></tr>
                            <tr><td>Plancha (Layout)</td><td>180°</td></tr>
                            <tr><td>Spagat</td><td>180°</td></tr>
                            <tr><td>Carpado (Pike)</td><td>90°</td></tr>
                            <tr><td>Agrupado (Tuck)</td><td>~45°</td></tr>
                            <tr><td>Compás (Straddle)</td><td>120°+</td></tr>
                            <tr><td>Arabesque</td><td>135°+</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><i class="fas fa-robot me-2"></i>Análisis con IA</div>
                <div class="card-body">
                    <form id="observacionesForm">
                        @csrf
                        <div class="form-group">
                            <label for="aparatoSelect">Aparato:</label>
                            <select name="ID_Aparato" id="aparatoSelect" class="form-control mb-2">
                                <option value="">Todos los aparatos</option>
                                @isset($aparatos)
                                    @foreach($aparatos as $a)
                                        <option value="{{ $a->ID_Aparato }}">{{ $a->Nombre }}</option>
                                    @endforeach
                                @endisset
                            </select>
                        </div>
                        <input type="hidden" name="angulos_detectados" id="angulosDetectados" value="">
                        <div id="angulosDetectadosPanel" class="mb-3" style="display:none;">
                            <div class="p-3 rounded" style="background:rgba(16,185,129,0.08);border:1px solid rgba(16,185,129,0.3);">
                                <h6 class="text-success mb-2"><i class="fas fa-robot"></i> Ángulos Detectados (MediaPipe)</h6>
                                <div id="angulosList" class="small"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="observaciones">Observaciones:</label>
                            <textarea name="observaciones" id="observaciones" class="form-control" rows="3" placeholder="Ej: Flexión de rodillas en el mortal..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-info btn-block" id="submitBtn">
                            <i class="fas fa-robot"></i> Analizar con IA
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
</div>

<script src="{{ asset('js/gymnastics-analysis.js') }}"></script>
<script src="/mediapipe/pose.js"></script>
<script>
const video = document.getElementById("video");
const manualCanvas = document.getElementById("manualCanvas");
const manualCtx = manualCanvas.getContext("2d");
const poseCanvas = document.getElementById("poseCanvas");
const poseCtx = poseCanvas.getContext("2d");

const el = {
    inputVideo: document.getElementById("inputVideo"), btnPlay: document.getElementById("btnPlay"),
    btnPause: document.getElementById("btnPause"), btnClear: document.getElementById("btnClear"),
    btnReset: document.getElementById("btnReset"), totalPuntos: document.getElementById("totalPuntos"),
    tiempoActual: document.getElementById("tiempoActual"), velocidadActual: document.getElementById("velocidadActual"),
    totalAngulos: document.getElementById("totalAngulos"), angulosActuales: document.getElementById("angulosActuales"),
    fileName: document.getElementById("fileName"), togglePoseBtn: document.getElementById("togglePoseBtn"),
    poseBtnText: document.getElementById("poseBtnText"),
    btnRewind: document.getElementById("btnRewind"), btnForward: document.getElementById("btnForward"),
    btnStepBack: document.getElementById("btnStepBack"), btnStepFwd: document.getElementById("btnStepFwd"),
    btnDebugPose: document.getElementById("btnDebugPose"), poseStatus: document.getElementById("poseStatus"),
    poseDebugInfo: document.getElementById("poseDebugInfo"),
    speedSlider: document.getElementById("speedSlider"), speedDisplay: document.getElementById("speedDisplay"),
};
const progressFill = document.getElementById("progressFill");
const progressBuffered = document.getElementById("progressBuffered");
const progressThumb = document.getElementById("progressThumb");
const progressTimeDisplay = document.getElementById("progressTimeDisplay");
const progressTimeTooltip = document.getElementById("progressTimeTooltip");
const progressBarContainer = document.getElementById("progressBarContainer");

let state = { puntos: [], isPlaying: false, velocidad: 1.0 };
let poseActive = false, poseModel = null, poseAnimFrame = null, poseProcessing = false;
let poseFrameCount = 0, poseFrameTimer = 0, poseErrorCount = 0, poseInitTime = 0;
let lastLandmarks = null;
let ultimosAngulosFIG = [];

function mostrarAngulosDetectados(angulos) {
    const panel = document.getElementById('angulosDetectadosPanel');
    const list = document.getElementById('angulosList');
    const input = document.getElementById('angulosDetectados');
    if (!angulos || angulos.length === 0) { panel.style.display = 'none'; return; }
    panel.style.display = 'block';
    const obj = {};
    angulos.forEach(a => { obj[a.name] = a.detected; });
    input.value = JSON.stringify(obj);
    list.innerHTML = '';
    let html = '<div class="row">';
    angulos.forEach(a => {
        const cls = a.status === 'perfecto' ? 'success' : a.status === 'aceptable' ? 'info' : a.status === 'deficiente' ? 'warning' : 'danger';
        html += `<div class="col-6 py-1"><span class="text-muted">${a.name}:</span> <strong class="text-${cls}">${a.detected}°</strong> <small class="text-muted">(ideal ${a.ideal}°)</small></div>`;
    });
    html += '</div>';
    list.innerHTML = html;
}

function resizeCanvases() {
    [manualCanvas, poseCanvas].forEach(c => {
        c.width = video.videoWidth || video.clientWidth;
        c.height = video.videoHeight || video.clientHeight;
        c.style.width = '100%';
    });
}

video.addEventListener("loadeddata", () => { resizeCanvases(); drawManual(); });
video.addEventListener("timeupdate", updateStats);
video.addEventListener("progress", updateProgressBar);
window.addEventListener('resize', resizeCanvases);

el.inputVideo.addEventListener("change", e => {
    const f = e.target.files[0]; if (!f) return;
    video.src = URL.createObjectURL(f); state.puntos = []; updateStats();
    el.fileName.textContent = f.name;
});

// --- Playback controls ---
el.btnPlay.addEventListener("click", () => {
    if (video.src) { video.play(); video.playbackRate = state.velocidad; state.isPlaying = true; anim(); }
});
el.btnPause.addEventListener("click", () => { video.pause(); state.isPlaying = false; });
el.btnClear.addEventListener("click", () => { state.puntos = []; updateStats(); drawManual(); });
el.btnReset.addEventListener("click", () => { state.velocidad = 1; video.playbackRate = 1; video.currentTime = 0; updateStats(); el.speedSlider.value = 1; el.speedDisplay.textContent = '1.00x'; });
el.btnRewind.addEventListener("click", () => { video.currentTime = Math.max(0, video.currentTime - 5); });
el.btnForward.addEventListener("click", () => { video.currentTime = Math.min(video.duration || 0, video.currentTime + 5); });
el.btnStepBack.addEventListener("click", () => { video.pause(); video.currentTime = Math.max(0, video.currentTime - 1/30); });
el.btnStepFwd.addEventListener("click", () => { video.pause(); video.currentTime = Math.min(video.duration || 0, video.currentTime + 1/30); });

// --- Speed Slider ---
el.speedSlider.addEventListener("input", () => {
    state.velocidad = parseFloat(el.speedSlider.value);
    video.playbackRate = state.velocidad;
    el.speedDisplay.textContent = state.velocidad.toFixed(2) + 'x';
    el.velocidadActual.textContent = state.velocidad.toFixed(1) + 'x';
});

// --- Manual points ---
manualCanvas.addEventListener("click", e => {
    if (!video.src) return;
    const r = manualCanvas.getBoundingClientRect();
    const sx = manualCanvas.width / r.width, sy = manualCanvas.height / r.height;
    state.puntos.push({ x: (e.clientX - r.left) * sx, y: (e.clientY - r.top) * sy });
    updateStats(); drawManual();
});

function drawManual() {
    manualCtx.clearRect(0, 0, manualCanvas.width, manualCanvas.height);
    state.puntos.forEach((p, i) => {
        manualCtx.beginPath(); manualCtx.arc(p.x, p.y, 8, 0, 2 * Math.PI);
        manualCtx.fillStyle = "#4e73df"; manualCtx.shadowColor = "#4e73df"; manualCtx.shadowBlur = 10; manualCtx.fill(); manualCtx.shadowBlur = 0;
        manualCtx.fillStyle = "white"; manualCtx.font = "bold 12px Arial"; manualCtx.textAlign = "center"; manualCtx.fillText(i + 1, p.x, p.y + 4);
    });
    if (state.puntos.length > 1) {
        manualCtx.strokeStyle = "#4e73df"; manualCtx.lineWidth = 2; manualCtx.beginPath();
        manualCtx.moveTo(state.puntos[0].x, state.puntos[0].y);
        for (let i = 1; i < state.puntos.length; i++) manualCtx.lineTo(state.puntos[i].x, state.puntos[i].y);
        manualCtx.stroke();
    }
    if (state.puntos.length >= 3) {
        for (let i = 0; i <= state.puntos.length - 3; i++) {
            const a = FIG.calcAngle(state.puntos[i], state.puntos[i + 1], state.puntos[i + 2]), B = state.puntos[i + 1];
            manualCtx.font = "bold 16px Arial"; manualCtx.fillStyle = "#f6c23e"; manualCtx.shadowColor = "#f6c23e"; manualCtx.shadowBlur = 5;
            manualCtx.fillText(a.toFixed(1) + "°", B.x + 15, B.y - 15); manualCtx.shadowBlur = 0;
        }
    }
}

function updateStats() {
    el.totalPuntos.textContent = state.puntos.length;
    el.tiempoActual.textContent = (video.currentTime || 0).toFixed(2) + "s";
    el.velocidadActual.textContent = state.velocidad.toFixed(1) + "x";
    updateProgressBar();
    if (state.puntos.length >= 3) {
        let h = "";
        for (let i = 0; i <= state.puntos.length - 3; i++) {
            const a = FIG.calcAngle(state.puntos[i], state.puntos[i + 1], state.puntos[i + 2]);
            h += `<div class="d-flex justify-content-between border-bottom py-1"><span>P${i + 1}-P${i + 2}-P${i + 3}</span><strong class="text-warning">${a.toFixed(1)}°</strong></div>`;
        }
        el.angulosActuales.innerHTML = h;
    } else {
        el.angulosActuales.innerHTML = `<p class="text-muted text-center py-3 mb-0 small">Marca 3+ puntos para ver ángulos</p>`;
    }
}

function anim() { if (!state.isPlaying) return; drawManual(); requestAnimationFrame(anim); }

// --- YouTube-style Progress Bar ---
function formatTime(s) {
    if (!s || isNaN(s)) return '0:00';
    const m = Math.floor(s / 60), sec = Math.floor(s % 60);
    return m + ':' + (sec < 10 ? '0' : '') + sec;
}

function updateProgressBar() {
    if (!video.duration) return;
    const pct = (video.currentTime / video.duration) * 100;
    progressFill.style.width = pct + '%';
    progressThumb.style.left = pct + '%';
    progressTimeDisplay.textContent = formatTime(video.currentTime) + ' / ' + formatTime(video.duration);
    if (video.buffered.length > 0) {
        const end = video.buffered.end(video.buffered.length - 1);
        progressBuffered.style.width = (end / video.duration) * 100 + '%';
    }
}

function seekVideo(clientX) {
    if (!video.duration) return;
    const rect = progressBarContainer.getBoundingClientRect();
    const x = Math.max(0, Math.min(clientX - rect.left, rect.width));
    video.currentTime = (x / rect.width) * video.duration;
}

progressBarContainer.addEventListener("click", (e) => seekVideo(e.clientX));
let isDraggingProgress = false;
progressBarContainer.addEventListener("mousedown", (e) => { isDraggingProgress = true; seekVideo(e.clientX); });
document.addEventListener("mousemove", (e) => { if (isDraggingProgress) seekVideo(e.clientX); });
document.addEventListener("mouseup", () => { isDraggingProgress = false; });

progressBarContainer.addEventListener("mousemove", (e) => {
    const rect = progressBarContainer.getBoundingClientRect();
    const x = e.clientX - rect.left;
    const pct = x / rect.width;
    const t = pct * (video.duration || 0);
    progressTimeTooltip.textContent = formatTime(t);
    progressTimeTooltip.style.display = 'block';
    progressTimeTooltip.style.left = Math.max(0, Math.min(x - 20, rect.width - 50)) + 'px';
});
progressBarContainer.addEventListener("mouseleave", () => { progressTimeTooltip.style.display = 'none'; });

// ===== MEDIAPIPE POSE (fixed) =====
async function loadPoseModel() {
    el.poseDebugInfo.textContent = 'Cargando MediaPipe...';
    try {
        const pose = new Pose({ locateFile: (file) => `/mediapipe/${file}` });
        pose.setOptions({ modelComplexity: 1, smoothLandmarks: true, minDetectionConfidence: 0.5, minTrackingConfidence: 0.5 });
        pose.onResults((results) => {
            if (!poseActive) return;
            poseFrameCount++;
            const now = Date.now();
            if (now - poseFrameTimer > 1000) {
                el.poseDebugInfo.textContent = poseFrameCount + ' fps';
                poseFrameCount = 0; poseFrameTimer = now;
            }
            poseCtx.clearRect(0, 0, poseCanvas.width, poseCanvas.height);
            if (results.poseLandmarks) {
                lastLandmarks = results.poseLandmarks;
                ultimosAngulosFIG = FIG.analyzeAngles(results.poseLandmarks);
                drawConnectors(poseCtx, results.poseLandmarks, Pose.POSE_CONNECTIONS, '#00FF00');
                drawLandmarks(poseCtx, results.poseLandmarks, '#FF0000');
                updateFIGAnalysis(results.poseLandmarks);
            }
        });
        poseModel = pose;
        el.poseDebugInfo.textContent = 'Pose listo. Reproduce el video.';
        return true;
    } catch (e) {
        console.error('Error loading MediaPipe:', e);
        el.poseDebugInfo.textContent = 'Error: ' + e.message;
        return false;
    }
}

function drawConnectors(ctx, lms, conns, color) {
    ctx.strokeStyle = color; ctx.lineWidth = 2;
    for (const [i, j] of conns) {
        const a = lms[i], b = lms[j];
        if (a && b && a.visibility > 0.5 && b.visibility > 0.5) {
            ctx.beginPath(); ctx.moveTo(a.x * poseCanvas.width, a.y * poseCanvas.height);
            ctx.lineTo(b.x * poseCanvas.width, b.y * poseCanvas.height); ctx.stroke();
        }
    }
}

function drawLandmarks(ctx, lms, color) {
    ctx.fillStyle = color;
    const indices = [11, 12, 13, 14, 15, 16, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32];
    for (const i of indices) {
        const lm = lms[i];
        if (lm && lm.visibility > 0.5) {
            ctx.beginPath(); ctx.arc(lm.x * poseCanvas.width, lm.y * poseCanvas.height, 4, 0, 2 * Math.PI); ctx.fill();
        }
    }
}

function updateFIGAnalysis(landmarks) {
    if (typeof FIG === 'undefined') return;
    const angles = FIG.analyzeAngles(landmarks);
    const detected = FIG.detectPosition(angles);
    el.totalAngulos.textContent = angles.length;
    FIG.renderAnglePanel('figAnglesPanel', angles, detected);

    if (detected && detected.confianza !== 'baja') {
        const info = FIG.positions[detected.position];
        const color = detected.confianza === 'alta' ? '#10b981' : '#f59e0b';
        document.getElementById('figPositionPanel').innerHTML = `
            <div class="text-center p-2 rounded" style="background:${color}15;border:1px solid ${color};">
                <small class="text-muted">Posición Detectada</small>
                <div style="color:${color};font-weight:700;font-size:1.1rem;">
                    <i class="fas fa-robot"></i> ${detected.nombre}
                    <span class="badge badge-${detected.confianza === 'alta' ? 'success' : 'warning'}">${detected.confianza}</span>
                </div>
                ${info ? `<small class="text-muted d-block mt-1">${info.description}</small>` : ''}
            </div>`;
    } else {
        document.getElementById('figPositionPanel').innerHTML = '<p class="text-muted text-center py-3 mb-0">Esperando landmarks...</p>';
    }
}

async function processPoseFrame() {
    if (!poseActive || !poseModel || poseProcessing) return;
    if (video.paused || video.ended || video.readyState < 2) {
        poseAnimFrame = requestAnimationFrame(processPoseFrame);
        return;
    }
    poseProcessing = true;
    if (poseErrorCount < 5 && Date.now() - poseInitTime < 3000) {
        await new Promise(r => setTimeout(r, 200));
    }
    try {
        await poseModel.send({ image: video });
        if (poseErrorCount > 0) {
            poseErrorCount = 0;
            el.poseDebugInfo.textContent = 'Pose ok';
        }
    } catch (e) {
        poseErrorCount++;
        const elapsed = ((Date.now() - poseInitTime) / 1000).toFixed(1);
        console.warn('Pose error #' + poseErrorCount + ' (' + elapsed + 's):', e.message);
        el.poseDebugInfo.textContent = 'Iniciando... (' + elapsed + 's)';
    }
    poseProcessing = false;
    if (poseActive) poseAnimFrame = requestAnimationFrame(processPoseFrame);
}

function deactivatePose() {
    poseActive = false; poseProcessing = false;
    if (poseAnimFrame) { cancelAnimationFrame(poseAnimFrame); poseAnimFrame = null; }
    poseCanvas.style.display = 'none';
    poseCtx.clearRect(0, 0, poseCanvas.width, poseCanvas.height);
    el.poseBtnText.textContent = 'Activar MediaPipe Pose';
    el.togglePoseBtn.classList.remove('btn-info');
    el.togglePoseBtn.classList.add('btn-outline-info');
    el.poseStatus.innerHTML = '<i class="fas fa-circle mr-1" style="color:#858796;"></i> Pose: inactivo';
    document.getElementById('figPositionPanel').innerHTML = '<p class="text-muted text-center py-3 mb-0">MediaPipe desactivado</p>';
    document.getElementById('figAnglesPanel').innerHTML = '<p class="text-muted text-center py-3 mb-0 small">Activa MediaPipe para ver el análisis FIG</p>';
    document.getElementById('angulosDetectadosPanel').style.display = 'none';
    document.getElementById('angulosDetectados').value = '';
    el.totalAngulos.textContent = '0';
}

el.togglePoseBtn.addEventListener('click', async () => {
    if (!poseActive) {
        if (!poseModel) {
            el.togglePoseBtn.disabled = true; el.poseBtnText.textContent = 'Cargando...';
            const loaded = await loadPoseModel();
            el.togglePoseBtn.disabled = false;
            if (!loaded) { alert('No se pudo cargar MediaPipe Pose. Revisa la consola.'); return; }
        }
        if (!video.src) { alert('Primero carga un video.'); return; }
        poseActive = true; poseErrorCount = 0; poseInitTime = Date.now();
        poseCanvas.style.display = 'block';
        el.poseBtnText.textContent = 'Desactivar MediaPipe';
        el.togglePoseBtn.classList.remove('btn-outline-info');
        el.togglePoseBtn.classList.add('btn-info');
        el.poseStatus.innerHTML = '<i class="fas fa-circle mr-1" style="color:#1cc88a;"></i> Pose: activo';
        el.poseDebugInfo.textContent = 'Pose activo. Reproduce el video...';
        if (!video.paused) processPoseFrame();
    } else {
        deactivatePose();
    }
});

el.btnDebugPose.addEventListener('click', () => {
    const info = [
        'Pose activo: ' + poseActive,
        'Pose modelo: ' + (poseModel ? 'cargado' : 'no cargado'),
        'Video: ' + (video.src ? 'cargado' : 'vacio'),
        'Duracion: ' + (video.duration || 0),
        'readyState: ' + video.readyState,
        'Pausado: ' + video.paused,
        'Errores: ' + poseErrorCount,
        'FIG: ' + (typeof FIG !== 'undefined' ? 'ok' : 'no definido'),
        'Pose global: ' + (typeof Pose !== 'undefined' ? 'ok' : 'no definido'),
    ].join('\n');
    alert(info);
    console.log(info);
});

video.addEventListener('play', () => {
    if (poseActive) processPoseFrame();
    document.getElementById('angulosDetectadosPanel').style.display = 'none';
});
video.addEventListener('pause', () => {
    if (poseAnimFrame) { cancelAnimationFrame(poseAnimFrame); poseAnimFrame = null; }
    if (ultimosAngulosFIG.length > 0) {
        mostrarAngulosDetectados(ultimosAngulosFIG);
    }
});

// ===== IA =====
const observacionesForm = document.getElementById('observacionesForm');
const submitBtn = document.getElementById('submitBtn');
const loadingIndicator = document.getElementById('loadingIndicator');
const aiResponseContainer = document.getElementById('aiResponseContainer');

observacionesForm.addEventListener('submit', function(e) {
    e.preventDefault();
    const obs = document.getElementById('observaciones').value.trim();
    if (!obs) { showError('Ingresa observaciones.'); return; }
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';
    loadingIndicator.style.display = 'block';
    aiResponseContainer.innerHTML = '';
    const formData = new FormData();
    formData.append('observaciones', obs);
    formData.append('_token', '{{ csrf_token() }}');
    const aparatoId = document.getElementById('aparatoSelect').value;
    if (aparatoId) formData.append('ID_Aparato', aparatoId);
    const angulosInput = document.getElementById('angulosDetectados').value;
    if (angulosInput) formData.append('angulos_detectados', angulosInput);
    fetch('{{ route("analisis.openai") }}', { method: 'POST', body: formData })
        .then(r => r.json())
        .then(data => {
            loadingIndicator.style.display = 'none';
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-robot"></i> Analizar con IA';
            data.success ? mostrarEvaluacion(data.evaluacion) : showError(data.error);
        })
        .catch(err => {
            loadingIndicator.style.display = 'none';
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-robot"></i> Analizar con IA';
            showError('Error: ' + err.message);
        });
});

function mostrarEvaluacion(eval) {
    let html = `<div class="card mt-3 border-${eval.confianza === 'alta' ? 'success' : eval.confianza === 'media' ? 'warning' : 'secondary'}">`;
    html += `<div class="card-header d-flex justify-content-between align-items-center bg-${eval.confianza === 'alta' ? 'success' : eval.confianza === 'media' ? 'warning' : 'secondary'} text-white">`;
    html += `<span><i class="fas fa-gavel"></i> Evaluación FIG</span>`;
    const badgeColor = eval.confianza === 'alta' ? 'success' : eval.confianza === 'media' ? 'warning' : 'secondary';
    html += `<span class="badge badge-${badgeColor}">Confianza: ${eval.confianza}</span></div>`;
    html += `<div class="card-body">`;

    if (eval.analisis_paso_a_paso) {
        html += `<div class="mb-3 p-3 rounded" style="background:rgba(0,0,0,0.03);">`;
        html += `<h6><i class="fas fa-list-ol text-primary"></i> Análisis paso a paso</h6>`;
        html += `<p class="small mb-0" style="white-space:pre-wrap;">${eval.analisis_paso_a_paso}</p>`;
        html += `</div>`;
    }

    if (eval.deducciones && eval.deducciones.length > 0) {
        html += `<h6 class="mb-3"><i class="fas fa-table text-danger"></i> Tabla de Deducciones</h6>`;
        html += `<div class="table-responsive"><table class="table table-sm table-bordered">`;
        html += `<thead class="thead-dark"><tr><th>#</th><th>Error</th><th>Valor</th><th>Regla FIG</th></tr></thead><tbody>`;
        eval.deducciones.forEach((d, i) => {
            const color = d.valor >= 0.50 ? 'danger' : d.valor >= 0.30 ? 'warning' : 'info';
            html += `<tr>
                <td>${i + 1}</td>
                <td>${d.error}</td>
                <td><span class="badge badge-${color}">-${d.valor.toFixed(2)} pts</span></td>
                <td class="small">${d.regla}</td>
            </tr>`;
        });
        html += `</tbody></table></div>`;
        html += `<div class="text-right mt-3 p-2 rounded" style="background:rgba(220,53,69,0.08);">`;
        html += `<h5 class="mb-0"><strong>Total descontado: <span class="text-danger">-${eval.total_descontado.toFixed(2)} pts</span></strong></h5>`;
        html += `</div>`;
    } else {
        html += `<div class="alert alert-info mb-0"><i class="fas fa-info-circle"></i> No se aplicaron deducciones.</div>`;
    }

    html += `</div></div>`;
    aiResponseContainer.innerHTML = html;
}

function showError(msg) {
    const div = document.createElement('div');
    div.className = 'alert alert-danger';
    div.innerHTML = '<h6><i class="fas fa-exclamation-triangle"></i> Error:</h6>' + msg;
    aiResponseContainer.appendChild(div);
}

// Init
updateProgressBar();
console.log('Analizador de Ángulos FIG cargado.');
</script>
@endsection
