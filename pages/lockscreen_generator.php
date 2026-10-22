<?php
// pages/lockscreen_generator.php
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
  <title>NurFlow — Scenic Islamic Wallpaper Builder</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    .builder-grid {
      display: grid;
      grid-template-columns: 340px 1fr;
      gap: 25px;
    }
    .preview-container {
      background: var(--bg-card);
      border: 1px solid var(--border-color);
      border-radius: var(--radius-md);
      padding: 25px;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      box-shadow: var(--shadow-subtle);
    }
    #wallpaperCanvas {
      border-radius: 16px;
      box-shadow: 0 12px 35px rgba(0,0,0,0.25);
      max-width: 100%;
      height: auto;
    }
    .form-group {
      margin-bottom: 16px;
    }
    .form-group label {
      display: block;
      font-size: 0.85rem;
      font-weight: 600;
      color: var(--text-dark);
      margin-bottom: 6px;
    }
    .form-control {
      width: 100%;
      padding: 10px 12px;
      border-radius: var(--radius-sm);
      border: 1px solid var(--border-color);
      background: var(--bg-main);
      font-size: 0.9rem;
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
        <h1 style="color: var(--primary-emerald);">🖼️ Scenic Islamic Wallpaper Builder</h1>
        <p style="color: var(--text-muted); font-size: 0.95rem;">
          Generate mobile wallpapers using high-definition landscapes, mountains, and seascapes.
        </p>
      </div>

      <div class="builder-grid">

        <!-- Controls Panel -->
        <div class="card">
          <div class="card-header">
            <span class="card-title">Customization Options</span>
          </div>

          <div class="form-group">
            <label>Arabic Verse / Dhikr</label>
            <textarea id="arabicTextInput" class="form-control" rows="2" oninput="renderWallpaper()">فَإِنَّ مَعَ الْعُسْرِ يُسْرًا</textarea>
          </div>

          <div class="form-group">
            <label>English Translation / Quote</label>
            <textarea id="englishTextInput" class="form-control" rows="2" oninput="renderWallpaper()">For indeed, with hardship will be ease.</textarea>
          </div>

          <div class="form-group">
            <label>Source Citation</label>
            <input type="text" id="sourceInput" class="form-control" value="Surah Ash-Sharh (94:5)" oninput="renderWallpaper()">
          </div>

          <div class="form-group">
            <label>Scenic Background Style</label>
            <select id="bgSelect" class="form-control" onchange="renderWallpaper()">
              <option value="mountains">🌄 Majestic Mountains</option>
              <option value="seascape">🌊 Calm Seascape & Ocean</option>
              <option value="desert">🏜️ Golden Desert Dunes</option>
              <option value="forest">🌲 Misty Forest</option>
            </select>
          </div>

          <button class="btn btn-gold" style="width: 100%; margin-top: 15px;" onclick="downloadWallpaper()">
            📥 Download Wallpaper
          </button>
        </div>

        <!-- Canvas Preview -->
        <div class="preview-container">
          <canvas id="wallpaperCanvas" width="400" height="700"></canvas>
        </div>

      </div>

    </div>

    <?php include __DIR__ . '/../includes/footer.php'; ?>

  </div>
</div>

<script>
  const bgImages = {
    mountains: 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=800&q=80',
    seascape: 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=800&q=80',
    desert: 'https://images.unsplash.com/photo-1509316975850-ff9c5deb0cd9?auto=format&fit=crop&w=800&q=80',
    forest: 'https://images.unsplash.com/photo-1448375240586-882707db888b?auto=format&fit=crop&w=800&q=80'
  };

  const loadedImages = {};

  document.addEventListener('DOMContentLoaded', () => {
    preloadImages();
  });

  function preloadImages() {
    let loadedCount = 0;
    const keys = Object.keys(bgImages);

    keys.forEach(key => {
      const img = new Image();
      img.crossOrigin = "Anonymous";
      img.src = bgImages[key];
      img.onload = () => {
        loadedImages[key] = img;
        loadedCount++;
        if (loadedCount === keys.length) {
          renderWallpaper();
        }
      };
    });
  }

  function renderWallpaper() {
    const canvas = document.getElementById('wallpaperCanvas');
    const ctx = canvas.getContext('2d');

    const arabic = document.getElementById('arabicTextInput').value;
    const english = document.getElementById('englishTextInput').value;
    const source = document.getElementById('sourceInput').value;
    const bgKey = document.getElementById('bgSelect').value;

    const img = loadedImages[bgKey];

    if (img) {
      ctx.drawImage(img, 0, 0, 400, 700);
    } else {
      ctx.fillStyle = '#0F172A';
      ctx.fillRect(0, 0, 400, 700);
    }

    // Dark Overlay Filter for Readability
    ctx.fillStyle = 'rgba(0, 0, 0, 0.45)';
    ctx.fillRect(0, 0, 400, 700);

    // Decorative Gold Border
    ctx.strokeStyle = '#D4AF37';
    ctx.lineWidth = 2;
    ctx.strokeRect(20, 20, 360, 660);

    // Text Header
    ctx.fillStyle = '#D4AF37';
    ctx.font = 'bold 13px sans-serif';
    ctx.textAlign = 'center';
    ctx.fillText('✨ NURFLOW REMINDER', 200, 60);

    // Arabic Text
    ctx.fillStyle = '#FFFFFF';
    ctx.font = '26px serif';
    ctx.fillText(arabic, 200, 320);

    // English Text
    ctx.fillStyle = '#FAF8F5';
    ctx.font = '15px sans-serif';
    ctx.fillText(`"${english}"`, 200, 390);

    // Citation
    ctx.fillStyle = '#D4AF37';
    ctx.font = 'italic 13px sans-serif';
    ctx.fillText(`— ${source}`, 200, 430);
  }

  function downloadWallpaper() {
    const canvas = document.getElementById('wallpaperCanvas');
    const link = document.createElement('a');
    link.download = 'nurflow-scenic-wallpaper.png';
    link.href = canvas.toDataURL('image/png');
    link.click();
  }
</script>

</body>
</html>
