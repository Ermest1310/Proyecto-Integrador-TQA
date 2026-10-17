const videoEl = document.getElementById('video');
const canvasEl = document.getElementById('outputCanvas');
const ctx2 = canvasEl.getContext('2d');

const pose = new Pose.Pose({
  locateFile: (file) => `https://cdn.jsdelivr.net/npm/@mediapipe/pose@0.5/${file}`
});

pose.setOptions({
  modelComplexity: 1,
  smoothLandmarks: true,
  minDetectionConfidence: 0.5,
  minTrackingConfidence: 0.5
});

pose.onResults((results) => {
  ctx2.clearRect(0, 0, canvasEl.width, canvasEl.height);
  ctx2.drawImage(results.image, 0, 0, canvasEl.width, canvasEl.height);

  if (results.poseLandmarks) {
    Pose.drawConnectors(ctx2, results.poseLandmarks, Pose.POSE_CONNECTIONS, { color: '#00FF00' });
    Pose.drawLandmarks(ctx2, results.poseLandmarks, { color: '#FF0000', lineWidth: 2 });
  }
});

document.getElementById('videoInput').addEventListener('change', (e) => {
  const file = e.target.files[0];
  if (file) {
    videoEl.src = URL.createObjectURL(file);
    videoEl.load();
  }
});

videoEl.addEventListener('play', () => {
  const processFrame = async () => {
    if (!videoEl.paused && !videoEl.ended) {
      await pose.send({ image: videoEl });
      requestAnimationFrame(processFrame);
    }
  };
  processFrame();
});
