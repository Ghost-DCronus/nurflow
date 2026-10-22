<?php
// pages/history.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';

require_login();

// Curated timeline events
$history_events = [
    [
        'era' => '610 CE – 632 CE',
        'title' => 'Prophetic Era & The Revelation',
        'category' => 'Prophetic History',
        'summary' => 'The beginning of divine revelation to Prophet Muhammad ﷺ in Cave Hira, the establishment of the first Islamic state in Madinah (Hijrah, 622 CE), and the farewell pilgrimage.'
    ],
    [
        'era' => '632 CE – 661 CE',
        'title' => 'The Rightly Guided Caliphate (Rashidun)',
        'category' => 'Caliphate Era',
        'summary' => 'The leadership of Abu Bakr As-Siddiq, Umar ibn Al-Khattab, Uthman ibn Affan, and Ali ibn Abi Talib (RA). Compilation of the official Quranic mushaf and rapid expansion.'
    ],
    [
        'era' => '750 CE – 1258 CE',
        'title' => 'The Islamic Golden Age & House of Wisdom',
        'category' => 'Science & Philosophy',
        'summary' => 'Establishment of Bayt al-Hikmah in Baghdad. Pioneers like Al-Khwarizmi (algebra), Ibn Sina (medicine), Ibn al-Haytham (optics), and Fatima al-Fihri (founding University of Al-Qarawiyyin).'
    ],
    [
        'era' => '711 CE – 1492 CE',
        'title' => 'Al-Andalus & Islamic Spain',
        'category' => 'Architecture & Knowledge',
        'summary' => 'Centuries of cultural, scientific, and architectural flourishing in Granada, Cordoba, and Seville, building global centers of learning and tolerance.'
    ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>NurFlow — Islamic History</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    .timeline {
      position: relative;
      margin-top: 20px;
      padding-left: 20px;
      border-left: 3px solid var(--primary-emerald);
    }
    .timeline-item {
      position: relative;
      margin-bottom: 30px;
      padding-left: 20px;
    }
    .timeline-item::before {
      content: '';
      position: absolute;
      left: -29px;
      top: 4px;
      width: 14px;
      height: 14px;
      border-radius: 50%;
      background: var(--accent-gold);
      border: 3px solid var(--bg-card);
    }
    .timeline-era {
      font-size: 0.85rem;
      font-weight: 700;
      color: var(--gold-hover);
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    .timeline-title {
      font-size: 1.2rem;
      font-weight: 700;
      color: var(--primary-emerald);
      margin: 4px 0 8px 0;
    }
    .timeline-badge {
      display: inline-block;
      background: var(--emerald-light);
      color: var(--primary-emerald);
      font-size: 0.75rem;
      font-weight: 700;
      padding: 3px 10px;
      border-radius: 12px;
      margin-bottom: 8px;
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
        <h1 style="color: var(--primary-emerald);">🕌 Islamic History & Golden Age</h1>
        <p style="color: var(--text-muted); font-size: 0.95rem;">
          Discover key epochs, major achievements in science, mathematics, literature, and Islamic civilizational milestones.
        </p>
      </div>

      <div class="card">
        <div class="card-header">
          <span class="card-title">Chronological History Timeline</span>
        </div>

        <div class="timeline">
          <?php foreach ($history_events as $event): ?>
            <div class="timeline-item">
              <span class="timeline-era"><?= $event['era']; ?></span>
              <h3 class="timeline-title"><?= $event['title']; ?></h3>
              <span class="timeline-badge"><?= $event['category']; ?></span>
              <p style="color: var(--text-dark); line-height: 1.6; font-size: 0.95rem; margin-top: 6px;">
                <?= $event['summary']; ?>
              </p>
            </div>
          <?php endforeach; ?>
        </div>

      </div>

    </div><!-- /.page-body -->

    <?php include __DIR__ . '/../includes/footer.php'; ?>

  </div><!-- /#main-wrapper -->

</div><!-- /#app-container -->

</body>
</html>
