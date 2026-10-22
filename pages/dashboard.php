<?php
// pages/dashboard.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';

// Protect page: redirect unauthenticated users to index.php
require_login();

$user_id   = $_SESSION['user_id'];
$user_name = $_SESSION['fullname'] ?? 'Believer';
$user_city = $_SESSION['city'] ?? 'Sokoto';
$today     = date('Y-m-d');

// Fetch today's completed habits count
$stmt_habits = $pdo->prepare("SELECT COUNT(*) FROM user_habits WHERE user_id = ? AND log_date = ? AND status = 1");
$stmt_habits->execute([$user_id, $today]);
$completed_habits = $stmt_habits->fetchColumn();

// Fetch total dhikr count logged today
$stmt_dhikr = $pdo->prepare("SELECT SUM(count_achieved) FROM dhikr_logs WHERE user_id = ? AND log_date = ?");
$stmt_dhikr->execute([$user_id, $today]);
$today_dhikr = $stmt_dhikr->fetchColumn() ?: 0;

// Fetch Quran progress bookmark
$stmt_quran = $pdo->prepare("SELECT surah_number, ayah_number, surah_name FROM quran_progress WHERE user_id = ?");
$stmt_quran->execute([$user_id]);
$quran_progress = $stmt_quran->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>NurFlow — Dashboard</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    .stat-card {
      background: var(--bg-card);
      border: 1px solid var(--border-color);
      border-radius: var(--radius-md);
      padding: 20px;
      display: flex;
      align-items: center;
      gap: 15px;
      box-shadow: var(--shadow-subtle);
    }
    .stat-icon {
      width: 50px;
      height: 50px;
      border-radius: 50%;
      background: var(--emerald-light);
      color: var(--primary-emerald);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.4rem;
      font-weight: bold;
    }
    .stat-number {
      font-size: 1.5rem;
      font-weight: 800;
      color: var(--primary-emerald);
    }
    .stat-label {
      font-size: 0.85rem;
      color: var(--text-muted);
    }
    .hero-banner {
      background: linear-gradient(135deg, var(--primary-emerald) 0%, #09442A 100%);
      color: var(--text-light);
      border-radius: var(--radius-md);
      padding: 30px;
      margin-bottom: 25px;
      box-shadow: var(--shadow-subtle);
      position: relative;
      overflow: hidden;
    }
    .hero-banner h1 {
      font-size: 1.8rem;
      margin-bottom: 8px;
    }
    .hero-banner p {
      color: rgba(255, 255, 255, 0.85);
      font-size: 0.95rem;
      max-width: 600px;
    }
    .hero-badge {
      display: inline-block;
      background: var(--accent-gold);
      color: #000;
      font-weight: 700;
      padding: 4px 12px;
      border-radius: 20px;
      font-size: 0.8rem;
      margin-bottom: 12px;
    }
    .verse-box {
      background: var(--gold-light);
      border-left: 4px solid var(--accent-gold);
      padding: 20px;
      border-radius: var(--radius-sm);
      margin-top: 20px;
    }
  </style>
</head>
<body>

<div id="app-container">
  
  <?php include '../includes/sidebar.php'; ?>

  <div id="main-wrapper">
    
    <?php include '../includes/header.php'; ?>

    <div class="page-body">
      
      <!-- Welcome Hero Section -->
      <div class="hero-banner">
        <span class="hero-badge">Daily Spiritual Focus</span>
        <h1>Welcome Back, <?= htmlspecialchars($user_name); ?></h1>
        <p>Stay steadfast in your worship today. Monitor your 5 daily prayers, track your dhikr goals, and continue your Quranic reading.</p>
      </div>

      <!-- Live Statistics Grid -->
      <div class="card-grid">
        
        <div class="stat-card">
          <div class="stat-icon">📅</div>
          <div>
            <div class="stat-number"><?= (int)$completed_habits; ?> / 5</div>
            <div class="stat-label">Daily Prayers & Habits Logged</div>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon">📿</div>
          <div>
            <div class="stat-number"><?= number_format($today_dhikr); ?></div>
            <div class="stat-label">Total Dhikr Recited Today</div>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon">📖</div>
          <div>
            <div class="stat-number">
              <?= $quran_progress ? 'Surah ' . $quran_progress['surah_number'] . ':' . $quran_progress['ayah_number'] : 'Surah 1:1'; ?>
            </div>
            <div class="stat-label">Last Saved Quran Bookmark</div>
          </div>
        </div>

      </div>

      <!-- Daily Inspiration & Quick Links -->
      <div class="card-grid" style="margin-top: 25px;">
        
        <div class="card" style="grid-column: span 2;">
          <div class="card-header">
            <span class="card-title">📖 Verse of the Day</span>
            <span style="font-size: 0.85rem; color: var(--text-muted);">Verified Source</span>
          </div>
          <div class="verse-box">
            <p class="arabic-text">فَإِنَّ مَعَ الْعُسْرِ يُسْرًا • إِنَّ مَعَ الْعُسْرِ يُسْرًا</p>
            <p style="margin-top: 10px; font-weight: 600; color: var(--text-dark);">
              "For indeed, with hardship will be ease. Indeed, with hardship will be ease."
            </p>
            <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 6px;">— Surah Ash-Sharh (94:5-6)</p>
          </div>
        </div>

        <div class="card">
          <div class="card-header">
            <span class="card-title">🚀 Quick Actions</span>
          </div>
          <div style="display: flex; flex-direction: column; gap: 10px; margin-top: 10px;">
            <a href="habits.php" class="btn btn-primary" style="justify-content: flex-start; gap: 10px;">
              <span>📅</span> Log Today's Prayers
            </a>
            <a href="tasbeeh.php" class="btn btn-gold" style="justify-content: flex-start; gap: 10px;">
              <span>📿</span> Open Digital Tasbeeh
            </a>
            <a href="quran.php" class="btn" style="background: var(--emerald-light); color: var(--primary-emerald); justify-content: flex-start; gap: 10px;">
              <span>📖</span> Continue Reading Quran
            </a>
          </div>
        </div>

      </div>

    </div><!-- /.page-body -->

    <?php include '../includes/footer.php'; ?>

  </div><!-- /#main-wrapper -->

</div><!-- /#app-container -->

</body>
</html>