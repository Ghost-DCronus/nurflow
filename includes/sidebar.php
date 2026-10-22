<?php
// includes/sidebar.php
// Detect active page for automatic menu item highlighting
$current_page = basename($_SERVER['PHP_SELF']);
?>
<nav id="sidebar">
  <div class="brand-logo">
    ✨ Nur<span>Flow</span>
  </div>
  <ul class="nav-list">
    <li class="nav-item <?= ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
      <a href="dashboard.php"><span>🏠</span> Dashboard</a>
    </li>
    <li class="nav-item <?= ($current_page == 'habits.php') ? 'active' : ''; ?>">
      <a href="habits.php"><span>📅</span> Spiritual Habits</a>
    </li>
    <li class="nav-item <?= ($current_page == 'fasting.php') ? 'active' : ''; ?>">
      <a href="fasting.php"><span>🌙</span> Fasting Hub</a>
    </li>
    <li class="nav-item <?= ($current_page == 'quran.php') ? 'active' : ''; ?>">
      <a href="quran.php"><span>📖</span> Holy Quran</a>
    </li>
    <li class="nav-item <?= ($current_page == 'hadith.php') ? 'active' : ''; ?>">
      <a href="hadith.php"><span>📜</span> Hadith & Sunnah</a>
    </li>
    <li class="nav-item <?= ($current_page == 'history.php') ? 'active' : ''; ?>">
      <a href="history.php"><span>🕌</span> Islamic History</a>
    </li>
    <li class="nav-item <?= ($current_page == 'library.php') ? 'active' : ''; ?>">
      <a href="library.php"><span>📚</span> eBooks & Kids</a>
    </li>
    <li class="nav-item <?= ($current_page == 'tasbeeh.php') ? 'active' : ''; ?>">
      <a href="tasbeeh.php"><span>📿</span> Digital Tasbeeh</a>
    </li>
    <li class="nav-item <?= ($current_page == 'zakat.php') ? 'active' : ''; ?>">
      <a href="zakat.php"><span>💰</span> Zakat Calculator</a>
    </li>
    <li class="nav-item <?= ($current_page == 'lockscreen_generator.php') ? 'active' : ''; ?>">
      <a href="lockscreen_generator.php"><span>🖼️</span> Wallpaper Builder</a>
    </li>
    <li class="nav-item <?= ($current_page == 'qibla.php') ? 'active' : ''; ?>">
      <a href="qibla.php"><span>🧭</span> Qibla Compass</a>
    </li>
  </ul>
  <div style="padding: 20px; border-top: 1px solid var(--border-color);">
    <a href="../logout.php" style="color: #ef4444; text-decoration: none; font-weight: 600; display: flex; align-items: center; gap: 8px; font-size: 0.9rem;">
      <span>🚪</span> Logout
    </a>
  </div>
</nav>