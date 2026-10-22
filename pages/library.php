<?php
// pages/library.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';

require_login();

// High-quality public online PDF links (Internet Archive / Public Islamic Libraries)
$books = [
    [
        'title'       => 'The Sealed Nectar (Ar-Raheeq Al-Makhtum)',
        'author'      => 'Safiur Rahman Mubarakpuri',
        'category'    => 'General Knowledge',
        'description' => 'Complete award-winning biography (Seerah) of Prophet Muhammad ﷺ containing maps, timelines, and full historical accounts.',
        'pdf_url'     => 'https://archive.org/download/DiscoverIslam-Books/Discover%20Islam%20-%20Books/The-Sealed-Nectar.pdf'
    ],
    [
        'title'       => 'Stories of the Prophets for Children',
        'author'      => 'Ibn Kathir (Adapted)',
        'category'    => 'Kids & Stories',
        'description' => 'Illustrated authentic stories of Prophet Adam, Nuh, Ibrahim, Musa, and Isa (AS) formatted for young readers.',
        'pdf_url'     => 'https://archive.org/download/StoriesOfTheProphetsIbnKathir/Stories%20of%20the%20Prophets%20Ibn%20Kathir.pdf'
    ],
    [
        'title'       => 'Fiqh of Purification & Salah',
        'author'      => 'Sheikh Muhammad Al-Jibaly',
        'category'    => 'Fiqh & Worship',
        'description' => 'Comprehensive step-by-step illustrated manual on Wudu, Ghusl, and perfecting the 5 daily prayers.',
        'pdf_url'     => 'https://archive.org/download/FiqhOfPurification/Fiqh%20of%20Purification.pdf'
    ],
    [
        'title'       => 'Fortress of the Muslim (Hisn al-Muslim)',
        'author'      => 'Sa\'id ibn Ali ibn Wahf al-Qahtani',
        'category'    => 'Daily Adhkar',
        'description' => 'The complete original pocketbook of daily authentic supplications with Arabic, transliteration, and English meanings.',
        'pdf_url'     => 'https://archive.org/download/DiscoverIslam-Books/Discover%20Islam%20-%20Books/FortressOfTheMuslim.pdf'
    ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>NurFlow — Islamic Library & Kids Hub</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    .book-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 20px;
    }
    .book-card {
      background: var(--bg-card);
      border: 1px solid var(--border-color);
      border-radius: var(--radius-md);
      padding: 22px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      box-shadow: var(--shadow-subtle);
    }
    .badge-category {
      background: var(--emerald-light);
      color: var(--primary-emerald);
      padding: 4px 10px;
      border-radius: 12px;
      font-weight: 700;
      font-size: 0.8rem;
      display: inline-block;
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
        <h1 style="color: var(--primary-emerald);">📚 Islamic Library & Kids Hub</h1>
        <p style="color: var(--text-muted); font-size: 0.95rem;">
          Access full, unabridged PDF books complete with original artwork, maps, and illustrations.
        </p>
      </div>

      <div class="book-grid">
        <?php foreach ($books as $book): ?>
          <div class="book-card">
            <div>
              <span class="badge-category">
                <?= htmlspecialchars($book['category']); ?>
              </span>
              <h3 style="color: var(--primary-emerald); margin: 12px 0 6px 0; font-size: 1.1rem;"><?= htmlspecialchars($book['title']); ?></h3>
              <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 12px; font-weight: 600;">By <?= htmlspecialchars($book['author']); ?></p>
              <p style="font-size: 0.9rem; color: var(--text-dark); line-height: 1.5; margin-bottom: 20px;">
                <?= htmlspecialchars($book['description']); ?>
              </p>
            </div>
            
            <a href="<?= htmlspecialchars($book['pdf_url']); ?>" target="_blank" rel="noopener noreferrer" class="btn btn-primary" style="width: 100%; text-decoration: none; text-align: center;">
              📖 Read Full PDF Book
            </a>
          </div>
        <?php endforeach; ?>
      </div>

    </div>

    <?php include __DIR__ . '/../includes/footer.php'; ?>

  </div>
</div>

</body>
</html>
