<?php
// pages/tasbeeh.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';

require_login();

$user_id = $_SESSION['user_id'];$today   = date('Y-m-d');

// Fetch today's saved dhikr logs from database
$stmt =$pdo->prepare("SELECT dhikr_title, count_achieved, created_at FROM dhikr_logs WHERE user_id = ? AND log_date = ? ORDER BY id DESC");
$stmt->execute([$user_id,$today]);
$today_logs =$stmt->fetchAll();

// Calculate total recitations today
$stmt_total =$pdo->prepare("SELECT SUM(count_achieved) FROM dhikr_logs WHERE user_id = ? AND log_date = ?");
$stmt_total->execute([$user_id,$today]);
$grand_total =$stmt_total->fetchColumn() ?: 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>NurFlow — Digital Tasbeeh Counter</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    .tasbeeh-grid {
      display: grid;
      grid-template-columns: 1fr 340px;
      gap: 25px;
    }
    .counter-card {
      background: var(--bg-card);
      border: 1px solid var(--border-color);
      border-radius: var(--radius-md);
      padding: 35px;
      text-align: center;
      box-shadow: var(--shadow-subtle);
    }
    .counter-btn {
      width: 180px;
      height: 180px;
      border-radius: 50%;
      background: linear-gradient(135deg, var(--primary-emerald), #052A1A);
      color: #fff;
      font-size: 3.2rem;
      font-weight: 800;
      border: 6px solid var(--gold-light);
      cursor: pointer;
      margin: 25px auto;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 10px 25px rgba(13, 92, 58, 0.3);
      transition: transform 0.1s ease;
    }
    .counter-btn:active {
      transform: scale(0.95);
    }
    .preset-btn {
      padding: 8px 16px;
      border-radius: 20px;
      border: 1px solid var(--border-color);
      background: var(--bg-main);
      color: var(--text-dark);
      font-weight: 600;
      cursor: pointer;
      font-size: 0.85rem;
      margin: 4px;
    }
    .preset-btn:hover {
      background: var(--primary-emerald);
      color: #fff;
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
        <h1 style="color: var(--primary-emerald);">📿 Digital Tasbeeh Counter</h1>
        <p style="color: var(--text-muted); font-size: 0.95rem;">
          Tap the counter to recite. Click "Save Recitation" to save your count directly to your daily log.
        </p>
      </div>

      <div class="tasbeeh-grid">

        <!-- Counter Area -->
        <div class="counter-card">
          <h2 style="color: var(--primary-emerald);" id="dhikrTitleDisplay">SubhanAllah (سبحان الله)</h2>
          <p style="color: var(--text-muted); font-size: 0.88rem;">Target: <span id="targetDisplay">33</span> Recitations</p>

          <button class="counter-btn" id="counterBtn" onclick="incrementCounter()">
            <span id="counterDisplay">0</span>
          </button>

          <div style="margin-bottom: 20px;">
            <button class="preset-btn" onclick="setDhikr('SubhanAllah (سبحان الله)', 33)">SubhanAllah (33)</button>
            <button class="preset-btn" onclick="setDhikr('Alhamdulillah (الحمد لله)', 33)">Alhamdulillah (33)</button>
            <button class="preset-btn" onclick="setDhikr('Allahu Akbar (الله أكبر)', 34)">Allahu Akbar (34)</button>
            <button class="preset-btn" onclick="setDhikr('Astaghfirullah (أستغفر الله)', 100)">Astaghfirullah (100)</button>
          </div>

          <div style="display: flex; gap: 10px; justify-content: center;">
            <button class="btn btn-primary" onclick="saveDhikrLog()">💾 Save Recitation</button>
            <button class="btn" style="background: #ef4444; color: #fff;" onclick="resetCounter()">🔄 Reset</button>
          </div>
        </div>

        <!-- Summary Panel -->
        <div class="card">
          <div class="card-header">
            <span class="card-title">Today's Dhikr Log</span>
          </div>

          <div style="text-align: center; margin-bottom: 20px; padding: 15px; background: var(--emerald-light); border-radius: var(--radius-sm);">
            <div style="font-size: 0.8rem; color: var(--primary-emerald); font-weight: 700;">TOTAL RECITATIONS TODAY</div>
            <div style="font-size: 2.2rem; font-weight: 800; color: var(--primary-emerald); margin-top: 4px;" id="grandTotalDisplay"><?= number_format($grand_total); ?></div>
          </div>

          <div id="logsContainer">
            <?php if (empty($today_logs)): ?>
              <p style="text-align: center; color: var(--text-muted); padding: 20px;">No recitations logged today yet.</p>
            <?php else: ?>
              <?php foreach ($today_logs as$log): ?>
                <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid var(--border-color); font-size: 0.9rem;">
                  <div>
                    <strong style="color: var(--primary-emerald);"><?= htmlspecialchars($log['dhikr_title']); ?></strong>
                    <div style="font-size: 0.75rem; color: var(--text-muted);"><?= date('h:i A', strtotime($log['created_at'])); ?></div>
                  </div>
                  <span style="font-weight: 700; color: var(--gold-hover);">+<?= $log['count_achieved']; ?></span>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>

      </div>

    </div>

    <?php include __DIR__ . '/../includes/footer.php'; ?>

  </div>
</div>

<script>
  let count = 0;
  let target = 33;
  let currentTitle = 'SubhanAllah (سبحان الله)';

  function setDhikr(title, targetVal) {
    currentTitle = title;
    target = targetVal;
    count = 0;
    document.getElementById('dhikrTitleDisplay').innerText = currentTitle;
    document.getElementById('targetDisplay').innerText = target;
    document.getElementById('counterDisplay').innerText = count;
  }

  function incrementCounter() {
    count++;
    document.getElementById('counterDisplay').innerText = count;
  }

  function resetCounter() {
    count = 0;
    document.getElementById('counterDisplay').innerText = count;
  }

  function saveDhikrLog() {
    if (count === 0) {
      alert('Please tap the counter before saving!');
      return;
    }

    fetch('../api/save_progress.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        type: 'dhikr',
        dhikr_title: currentTitle,
        count: count,
        target: target
      })
    })
    .then(res => res.json())
    .then(data => {
      if (data.status === 'success') {
        alert(`✅ Saved ${count} recitations of ${currentTitle}!`);
        location.reload();
      } else {
        alert('Failed to save dhikr.');
      }
    })
    .catch(err => console.error('Save Dhikr Error:', err));
  }
</script>

</body>
</html>
