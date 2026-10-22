<?php
// pages/habits.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';

require_login();

$user_id = $_SESSION['user_id'];
$today   = date('Y-m-d');

// Defined core spiritual habits
$habits_list = [
    'fajr'     => '🕌 Fajr Prayer',
    'dhuhr'    => '🕌 Dhuhr Prayer',
    'asr'      => '🕌 Asr Prayer',
    'maghrib'  => '🕌 Maghrib Prayer',
    'isha'     => '🕌 Isha Prayer',
    'tahajjud' => '🌌 Tahajjud (Night Prayer)',
    'duha'     => '☀️ Duha Prayer',
    'adhkar'   => '📿 Morning & Evening Adhkar'
];

// Fetch existing logs for today
$stmt = $pdo->prepare("SELECT habit_name, status FROM user_habits WHERE user_id = ? AND log_date = ?");
$stmt->execute([$user_id, $today]);
$today_habits = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>NurFlow — Spiritual Habits</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    .habit-item {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 16px 20px;
      background: var(--bg-card);
      border: 1px solid var(--border-color);
      border-radius: var(--radius-sm);
      margin-bottom: 12px;
      transition: all 0.2s ease;
    }
    .habit-item:hover {
      border-color: var(--primary-emerald);
    }
    .habit-info {
      display: flex;
      align-items: center;
      gap: 12px;
      font-weight: 600;
      font-size: 1rem;
      color: var(--text-dark);
    }
    .toggle-checkbox {
      width: 24px;
      height: 24px;
      accent-color: var(--primary-emerald);
      cursor: pointer;
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
        <h1 style="color: var(--primary-emerald);">Spiritual Habit Tracker</h1>
        <p style="color: var(--text-muted); font-size: 0.95rem;">
          Track your daily mandatory prayers and Sunnah worship routines for <?= date('F j, Y'); ?>.
        </p>
      </div>

      <div class="card" style="max-width: 700px;">
        <div class="card-header">
          <span class="card-title">Today's Daily Worship</span>
          <span style="font-size: 0.85rem; color: var(--text-muted);">Auto-saves on check</span>
        </div>

        <div style="margin-top: 15px;">
          <?php foreach ($habits_list as $key => $label): ?>
            <?php $is_checked = !empty($today_habits[$key]); ?>
            <div class="habit-item">
              <div class="habit-info">
                <span><?= $label; ?></span>
              </div>
              <input 
                type="checkbox" 
                class="toggle-checkbox" 
                data-habit="<?= $key; ?>" 
                <?= $is_checked ? 'checked' : ''; ?> 
                onchange="saveHabitStatus(this)"
              >
            </div>
          <?php endforeach; ?>
        </div>
      </div>

    </div><!-- /.page-body -->

    <?php include __DIR__ . '/../includes/footer.php'; ?>

  </div><!-- /#main-wrapper -->

</div><!-- /#app-container -->

<script>
  function saveHabitStatus(checkbox) {
    const habitName = checkbox.getAttribute('data-habit');
    const isChecked = checkbox.checked;

    fetch('../api/save_progress.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        type: 'habit',
        habit_name: habitName,
        status: isChecked
      })
    })
    .then(res => res.json())
    .then(data => {
      if(data.status !== 'success') {
        alert('Error saving habit. Please try again.');
        checkbox.checked = !isChecked; // Revert on error
      }
    })
    .catch(err => console.error('Error saving habit:', err));
  }
</script>

</body>
</html>
