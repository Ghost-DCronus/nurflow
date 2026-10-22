<?php
// includes/header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user_name =$_SESSION['fullname'] ?? 'Believer';
$user_city =$_SESSION['city'] ?? 'Sokoto';
?>
<header id="top-header">
  <div class="header-left">
    <button class="sidebar-toggle" id="menuToggle">☰</button>
    <div class="header-date">
      📅 <span id="currentDateDisplay"><?= date('l, F j, Y'); ?></span> • <span style="color: var(--accent-gold); font-weight: 700;">Location: <?= htmlspecialchars($user_city); ?></span>
    </div>
  </div>

  <div class="header-right">
    <div style="background: var(--emerald-light); color: var(--primary-emerald); padding: 6px 14px; border-radius: 20px; font-weight: 700; font-size: 0.85rem;" id="nextPrayerBadge">
      🕋 Calculating Prayer Times...
    </div>
    <div class="user-profile" style="margin-left: 15px;">
      <span>👤 <?= htmlspecialchars($user_name); ?></span>
      <a href="../logout.php" style="color: #ef4444; margin-left: 10px; font-size: 0.85rem; text-decoration: underline;">Logout</a>
    </div>
  </div>
</header>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    fetchPrayerTimes('<?= addslashes($user_city); ?>');
  });

  function fetchPrayerTimes(city) {
    fetch(`https://api.aladhan.com/v1/timingsByCity?city=${encodeURIComponent(city)}&country=Nigeria&method=2`)
      .then(res => res.json())
      .then(data => {
        if (data.code === 200) {
          const timings = data.data.timings;
          const next = calculateNextPrayer(timings);
          document.getElementById('nextPrayerBadge').innerText = `🕋 Next Prayer: ${next.name} (${next.time})`;
        }
      })
      .catch(err => {
        console.error('Prayer API Error:', err);
        document.getElementById('nextPrayerBadge').innerText = '🕋 Prayer Times Active';
      });
  }

  function calculateNextPrayer(timings) {
    const now = new Date();
    const currentMins = now.getHours() * 60 + now.getMinutes();

    const prayers = [
      { name: 'Fajr', time: timings.Fajr },
      { name: 'Dhuhr', time: timings.Dhuhr },
      { name: 'Asr', time: timings.Asr },
      { name: 'Maghrib', time: timings.Maghrib },
      { name: 'Isha', time: timings.Isha }
    ];

    for (let p of prayers) {
      const [h, m] = p.time.split(':').map(Number);
      const prayerMins = h * 60 + m;
      if (prayerMins > currentMins) {
        return p;
      }
    }
    return { name: 'Fajr (Tomorrow)', time: timings.Fajr };
  }
</script>
