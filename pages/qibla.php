<?php
// pages/qibla.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';

require_login();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>NurFlow — Qibla Compass</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    .qibla-card {
      background: var(--bg-card);
      border: 1px solid var(--border-color);
      border-radius: var(--radius-md);
      padding: 40px;
      text-align: center;
      max-width: 500px;
      margin: 0 auto;
      box-shadow: var(--shadow-subtle);
    }
    .compass-circle {
      width: 250px;
      height: 250px;
      border-radius: 50%;
      border: 6px solid var(--primary-emerald);
      margin: 30px auto;
      position: relative;
      display: flex;
      align-items: center;
      justify-content: center;
      background: radial-gradient(circle, var(--emerald-light) 0%, var(--bg-card) 80%);
    }
    .compass-needle {
      width: 8px;
      height: 140px;
      background: var(--accent-gold);
      position: absolute;
      top: 20px;
      border-radius: 4px;
      transform-origin: bottom center;
      transition: transform 0.3s ease;
    }
  </style>
</head>
<body>

<div id="app-container">

  <?php include __DIR__ . '/../includes/sidebar.php'; ?>

  <div id="main-wrapper">

    <?php include __DIR__ . '/../includes/header.php'; ?>

    <div class="page-body">

      <div style="margin-bottom: 25px;">
        <h1 style="color: var(--primary-emerald);">🧭 Qibla Direction Compass</h1>
        <p style="color: var(--text-muted); font-size: 0.95rem;">
          Find the precise bearing towards the Holy Kaaba in Makkah from your current location.
        </p>
      </div>

      <div class="qibla-card">
        <h3 style="color: var(--primary-emerald); margin-bottom: 10px;">Kaaba Bearing Direction</h3>
        <p style="color: var(--text-muted); font-size: 0.9rem;" id="locationStatus">Detecting your GPS location...</p>

        <div class="compass-circle">
          <div class="compass-needle" id="compassNeedle"></div>
          <div style="position: absolute; top: 10px; font-weight: 700; color: var(--primary-emerald); font-size: 0.8rem;">N</div>
          <div style="position: absolute; bottom: 10px; font-weight: 700; color: var(--text-muted); font-size: 0.8rem;">S</div>
          <div style="position: absolute; left: 10px; font-weight: 700; color: var(--text-muted); font-size: 0.8rem;">W</div>
          <div style="position: absolute; right: 10px; font-weight: 700; color: var(--text-muted); font-size: 0.8rem;">E</div>
        </div>

        <div style="font-size: 1.4frem; font-weight: 800; color: var(--primary-emerald);" id="bearingDisplay">--° From North</div>
      </div>

    </div>

    <?php include __DIR__ . '/../includes/footer.php'; ?>

  </div>
</div>

<script>
  // Kaaba Coordinates: 21.4225° N, 39.8262° E
  const kaabaLat = 21.4225 * Math.PI / 180;
  const kaabaLon = 39.8262 * Math.PI / 180;

  document.addEventListener('DOMContentLoaded', () => {
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(position => {
        const userLat = position.coords.latitude * Math.PI / 180;
        const userLon = position.coords.longitude * Math.PI / 180;

        const dLon = kaabaLon - userLon;
        const y = Math.sin(dLon) * Math.cos(kaabaLat);
        const x = Math.cos(userLat) * Math.sin(kaabaLat) - Math.sin(userLat) * Math.cos(kaabaLat) * Math.cos(dLon);
        
        let brng = Math.atan2(y, x) * 180 / Math.PI;
        brng = (brng + 360) % 360;

        document.getElementById('locationStatus').innerText = "GPS Location Acquired Successfully";
        document.getElementById('bearingDisplay').innerText = brng.toFixed(1) + '° Qibla Bearing';
        document.getElementById('compassNeedle').style.transform = `rotate(${brng}deg)`;
      }, error => {
        document.getElementById('locationStatus').innerText = "Using default Sokoto Qibla calculation (~65.4°)";
        document.getElementById('compassNeedle').style.transform = 'rotate(65.4deg)';
        document.getElementById('bearingDisplay').innerText = '65.4° Qibla Bearing';
      });
    } else {
      document.getElementById('locationStatus').innerText = "Geolocation not supported by browser.";
    }
  });
</script>

</body>
</html>
