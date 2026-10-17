/**
 * FIG Gymnastics Analysis Module
 * Shared across analisis/video, analisis/angulo, and juez/evaluar
 */

const FIG = {
    // Key gymnastics positions and their ideal angle ranges (degrees)
    positions: {
        handstand: { name: 'Parada de Manos', ideal: 180, tolerance: 10, deduction: 0.10,
            joints: ['Hombro', 'Cadera', 'Tobillo'], description: 'Cuerpo totalmente extendido y alineado verticalmente' },
        split: { name: 'Split / Spagat', ideal: 180, tolerance: 15, deduction: 0.10,
            joints: ['Cadera'], description: 'Apertura completa de piernas a 180°' },
        layout: { name: 'Layout / Plancha', ideal: 180, tolerance: 10, deduction: 0.10,
            joints: ['Cadera', 'Hombro'], description: 'Cuerpo extendido sin doblar caderas ni rodillas' },
        pike: { name: 'Pike / Carpado', ideal: 90, tolerance: 10, deduction: 0.10,
            joints: ['Cadera'], description: 'Flexión de cadera a 90°, piernas extendidas' },
        tuck: { name: 'Tuck / Agrupado', ideal: 45, tolerance: 10, deduction: 0.10,
            joints: ['Cadera', 'Rodilla'], description: 'Máxima flexión de cadera y rodillas' },
        straddle: { name: 'Straddle / Compás', ideal: 120, tolerance: 15, deduction: 0.10,
            joints: ['Cadera'], description: 'Apertura lateral de piernas, tronco inclinado' },
        arabesque: { name: 'Arabesque', ideal: 135, tolerance: 15, deduction: 0.10,
            joints: ['Cadera'], description: 'Equilibrio sobre una pierna, tronco horizontal' },
        star: { name: 'Estrella / Rondat', ideal: 180, tolerance: 20, deduction: 0.10,
            joints: ['Hombro', 'Cadera'], description: 'Apertura total del cuerpo en plano frontal' },
    },

    // MediaPipe landmark indices for gymnastics
    landmarks: {
        leftShoulder: 11, rightShoulder: 12,
        leftElbow: 13, rightElbow: 14,
        leftWrist: 15, rightWrist: 16,
        leftHip: 23, rightHip: 24,
        leftKnee: 25, rightKnee: 26,
        leftAnkle: 27, rightAnkle: 28,
        leftFoot: 29, rightFoot: 30,
    },

    // Angle definitions for gymnastics analysis
    angleDefs: {
        'Codo Izquierdo': { type: 'flexion', joints: ['leftShoulder', 'leftElbow', 'leftWrist'], fig_ref: 180 },
        'Codo Derecho': { type: 'flexion', joints: ['rightShoulder', 'rightElbow', 'rightWrist'], fig_ref: 180 },
        'Rodilla Izquierda': { type: 'flexion', joints: ['leftHip', 'leftKnee', 'leftAnkle'], fig_ref: 180 },
        'Rodilla Derecha': { type: 'flexion', joints: ['rightHip', 'rightKnee', 'rightAnkle'], fig_ref: 180 },
        'Cadera Izquierda': { type: 'flexion', joints: ['leftShoulder', 'leftHip', 'leftKnee'], fig_ref: 180 },
        'Cadera Derecha': { type: 'flexion', joints: ['rightShoulder', 'rightHip', 'rightKnee'], fig_ref: 180 },
        'Hombro Izquierdo': { type: 'flexion', joints: ['leftElbow', 'leftShoulder', 'leftHip'], fig_ref: 180 },
        'Hombro Derecho': { type: 'flexion', joints: ['rightElbow', 'rightShoulder', 'rightHip'], fig_ref: 180 },
        'Tobillo Izquierdo': { type: 'flexion', joints: ['leftKnee', 'leftAnkle', 'leftFoot'], fig_ref: 90 },
        'Tobillo Derecho': { type: 'flexion', joints: ['rightKnee', 'rightAnkle', 'rightFoot'], fig_ref: 90 },
        'Tronco Vertical (Izq)': { type: 'alignment', joints: ['leftShoulder', 'leftHip', 'leftAnkle'], fig_ref: 180 },
        'Tronco Vertical (Der)': { type: 'alignment', joints: ['rightShoulder', 'rightHip', 'rightAnkle'], fig_ref: 180 },
    },

    /**
     * Calculate angle between 3 points
     */
    calcAngle: function(A, B, C) {
        const ab = { x: A.x - B.x, y: A.y - B.y };
        const cb = { x: C.x - B.x, y: C.y - B.y };
        const dot = ab.x * cb.x + ab.y * cb.y;
        const ma = Math.sqrt(ab.x ** 2 + ab.y ** 2);
        const mb = Math.sqrt(cb.x ** 2 + cb.y ** 2);
        if (ma === 0 || mb === 0) return 0;
        return Math.round(Math.acos(Math.max(-1, Math.min(1, dot / (ma * mb)))) * 180 / Math.PI);
    },

    /**
     * Get angle value from MediaPipe landmarks by definition key
     */
    getAngle: function(landmarks, defKey) {
        const def = this.angleDefs[defKey];
        if (!def) return null;
        const pts = def.joints.map(j => {
            const idx = this.landmarks[j];
            return landmarks[idx];
        });
        if (pts.some(p => !p || p.visibility === 0)) return null;
        return this.calcAngle(pts[0], pts[1], pts[2]);
    },

    /**
     * Analyze all angles and return detected deviations from FIG norms
     */
    analyzeAngles: function(landmarks) {
        const results = [];
        Object.entries(this.angleDefs).forEach(([name, def]) => {
            const angle = this.getAngle(landmarks, name);
            if (angle === null) return;
            const deviation = Math.abs(angle - def.fig_ref);
            results.push({
                name: name,
                detected: angle,
                ideal: def.fig_ref,
                deviation: deviation,
                status: deviation <= 5 ? 'perfecto' : deviation <= 10 ? 'aceptable' : deviation <= 20 ? 'deficiente' : 'critico',
                deduction: deviation > 5 ? Math.min(0.5, Math.round(deviation / 10 * 0.1 * 100) / 100) : 0
            });
        });
        return results;
    },

    /**
     * Detect gymnastics positions from angles
     */
    detectPosition: function(angles) {
        const hipL = angles.find(a => a.name === 'Cadera Izquierda');
        const hipR = angles.find(a => a.name === 'Cadera Derecha');
        const kneeL = angles.find(a => a.name === 'Rodilla Izquierda');
        const kneeR = angles.find(a => a.name === 'Rodilla Derecha');
        const shoulderL = angles.find(a => a.name === 'Hombro Izquierdo');
        const shoulderR = angles.find(a => a.name === 'Hombro Derecho');
        const trunkL = angles.find(a => a.name === 'Tronco Vertical (Izq)');
        const trunkR = angles.find(a => a.name === 'Tronco Vertical (Der)');

        const avgHip = hipL && hipR ? (hipL.detected + hipR.detected) / 2 : (hipL || hipR)?.detected || null;
        const avgKnee = kneeL && kneeR ? (kneeL.detected + kneeR.detected) / 2 : (kneeL || kneeR)?.detected || null;
        const avgShoulder = shoulderL && shoulderR ? (shoulderL.detected + shoulderR.detected) / 2 : (shoulderL || shoulderR)?.detected || null;
        const avgTrunk = trunkL && trunkR ? (trunkL.detected + trunkR.detected) / 2 : (trunkL || trunkR)?.detected || null;

        if (avgTrunk !== null && avgHip !== null && avgKnee !== null) {
            if (avgTrunk >= 170 && avgHip >= 170 && avgKnee >= 170)
                return { position: 'handstand', nombre: 'Parada de Manos', confianza: 'alta' };
            if (avgHip <= 100 && avgKnee >= 160)
                return { position: 'pike', nombre: 'Carpado (Pike)', confianza: 'alta' };
            if (avgHip <= 60 && avgKnee <= 100)
                return { position: 'tuck', nombre: 'Agrupado (Tuck)', confianza: 'alta' };
            if (avgHip >= 160 && avgKnee >= 160 && avgTrunk >= 160)
                return { position: 'layout', nombre: 'Plancha (Layout)', confianza: 'alta' };
            if (avgHip >= 120 && avgKnee >= 160)
                return { position: 'straddle', nombre: 'Compás (Straddle)', confianza: 'media' };
        }
        return { position: 'desconocido', nombre: 'Posición no identificada', confianza: 'baja' };
    },

    /**
     * Render angle analysis results into a container
     */
    renderAnglePanel: function(containerId, angles, detectedPos) {
        const container = document.getElementById(containerId);
        if (!container) return;
        if (!angles || angles.length === 0) {
            container.innerHTML = '<div class="text-center text-muted py-3"><small>No hay datos de ángulos disponibles</small></div>';
            return;
        }

        let html = '';
        if (detectedPos) {
            const posInfo = this.positions[detectedPos.position];
            const color = detectedPos.confianza === 'alta' ? '#10b981' : '#f59e0b';
            html += `
                <div class="text-center mb-3 p-2 rounded" style="background:${color}15; border:1px solid ${color};">
                    <small class="text-muted">Posición Detectada</small>
                    <div style="color:${color};font-weight:700;font-size:1.1rem;">
                        <i class="fas fa-robot"></i> ${detectedPos.nombre}
                        <span class="badge badge-${detectedPos.confianza === 'alta' ? 'success' : 'warning'} ml-1">${detectedPos.confianza}</span>
                    </div>
                </div>`;
        }

        html += '<div class="small">';
        angles.forEach(a => {
            const statusClass = a.status === 'perfecto' ? 'success' : a.status === 'aceptable' ? 'info' : a.status === 'deficiente' ? 'warning' : 'danger';
            const icon = a.status === 'perfecto' ? 'fa-check-circle' : a.status === 'aceptable' ? 'fa-info-circle' : a.status === 'deficiente' ? 'fa-exclamation-triangle' : 'fa-times-circle';
            html += `
                <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                    <span>${a.name}</span>
                    <div class="text-right">
                        <span class="badge badge-${statusClass}">
                            <i class="fas ${icon}"></i> ${a.detected}°
                        </span>
                        ${a.deviation > 5 ? `<br><small class="text-danger">-${a.deviation}° del ideal</small>` : ''}
                    </div>
                </div>`;
        });
        html += '</div>';
        html += `
            <hr class="my-2">
            <div class="d-flex justify-content-between text-muted small">
                <span>Deducción estimada total:</span>
                <span class="text-danger font-weight-bold">
                    -${angles.reduce((s, a) => s + a.deduction, 0).toFixed(2)} pts
                </span>
            </div>`;

        container.innerHTML = html;
    },

    /**
     * Get color for angle status
     */
    statusColor: function(status) {
        const map = { perfecto: '#10b981', aceptable: '#3b82f6', deficiente: '#f59e0b', critico: '#ef4444' };
        return map[status] || '#6c757d';
    }
};
