<?php
// pages/fasting.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';

require_login();

$user_id = $_SESSION['user_id'];
$today   = date('Y-m-d');
$day_of_week = date('l');

// Determine if today is a Sunnah Fasting Day
$is_monday_or_thursday = in_array($day_of_week, ['Monday', 'Thursday']);

// Fetch user's fasting status for today
$stmt = $pdo->prepare("SELECT fast_type, status FROM fasting_logs WHERE user_id = ? AND fast_date = ?");
$stmt->execute([$user_id, $today]);
$today_fast = $stmt->fetch();
$is_fasting = !empty($today_fast['status']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>NurFlow — Fasting Hub</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    .fasting-banner {
      background: var(--gold-light);
      border: 1px solid var(--accent-gold);
      border-radius: var(--radius-md);
      padding: 24px;
      margin-bottom: 25px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    .fasting-status-card {
      background: var(--bg-card);
      border: 1px solid var(--border-color);
      border-radius: var(--radius-md);
      padding: 24px;
      box-shadow: var(--shadow-subtle);
    }
    .toggle-switch {
      display: flex;
      align-items: center;
      gap: 15px;
      margin-top: 15px;
    }
    .badge-sunnah {
      background: var(--emerald-light);
      color: var(--primary-emerald);
      padding: 4px 12px;
      border-radius: 20px;
      font-weight: 700;
      font-size: 0.85rem;
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
        <h1 style="color: var(--primary-emerald);">🌙 Islamic Fasting Hub</h1>
        <p style="color: var(--text-muted); font-size: 0.95rem;">
          Track Sunnah fasts, Ramadan completion, and voluntary fasts with automatic Hijri day highlights.
        </p>
      </div>

      <!-- Today's Sunnah Alert Banner -->
      <div class="fasting-banner">
        <div>
          <span class="badge-sunnah">Sunnah Fasting Tracker</span>
          <h2 style="color: var(--primary-emerald); margin-top: 8px;">
            Today is <?= date('l, F j, Y'); ?>
          </h2>
          <p style="color: var(--text-dark); margin-top: 4px; font-size: 0.95rem;">
            <?php if ($is_monday_or_thursday): ?>
              ✨ <strong>Today is a Sunnah Fasting Day!</strong> (<?= $day_of_week; ?> Fasting as practiced by Prophet Muhammad ﷺ).
            <?php else: ?>
              Prepare for the upcoming Monday & Thursday Sunnah fasts and Ayyam al-Beed (White Days).
            <?php endif; ?>
          </p>
        </div>
      </div>

      <div class="card-grid">

        <!-- Today's Fast Logging Card -->
        <div class="fasting-status-card">
          <div class="card-header">
            <span class="card-title">Log Today's Fast</span>
          </div>

          <p style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 15px;">
            Check the box below if you are observing a fast today.
          </p>

          <div class="toggle-switch">
            <input 
              type="checkbox" 
              id="fastingCheckbox" 
              style="width: 24px; height: 24px; accent-color: var(--primary-emerald); cursor: pointer;"
              <?= $is_fasting ? 'checked' : ''; ?>
              onchange="toggleFasting(this)"
            >
            <label for="fastingCheckbox" style="font-weight: 700; font-size: 1.05rem; cursor: pointer; color: var(--text-dark);">
              <?= $is_fasting ? '✅ Fasting Recorded for Today' : 'Mark Today as Fasting'; ?>
            </label>
          </div>
        </div>

        <!-- Virtues of Fasting Reference Card -->
        <div class="card">
          <div class="card-header">
            <span class="card-title">📜 Virtues of Sunnah Fasting</span>
          </div>
          <p style="font-size: 0.9rem; color: var(--text-dark); line-height: 1.6;">
            The Messenger of Allah ﷺ said: 
            <em>"Deeds are presented on Mondays and Thursdays, and I love that my deeds be presented while I am fasting."</em>
          </p>
          <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 8px;">
            — Jami` at-Tirmidhi (747)
          </p>
        </div>

      </div>

    </div><!-- /.page-body -->

    <?php include __DIR__ . '/../includes/footer.php'; ?>

  </div><!-- /#main-wrapper -->

</div><!-- /#app-container -->

<script>
  function toggleFasting(checkbox) {
    const isChecked = checkbox.checked;

    fetch('../api/save_progress.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        type: 'fasting',
        fast_type: 'Sunnah Fast',
        status: isChecked
      })
    })
    .then(res => res.json())
    .then(data => {
      if (data.status === 'success') {
        location.reload(); // Refresh to update badge status cleanly
      } else {
        alert('Failed to update fasting log.');
        checkbox.checked = !isChecked;
      }
    })
    .catch(err => console.error('Error saving fast:', err));
  }
</script>

</body>
</html>
