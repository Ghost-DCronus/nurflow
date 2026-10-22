<?php
// pages/quran.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';

require_login();

$user_id =$_SESSION['user_id'];

// Get existing user bookmark if present
$stmt =$pdo->prepare("SELECT surah_number, ayah_number, surah_name FROM quran_progress WHERE user_id = ?");
$stmt->execute([$user_id]);
$bookmark =$stmt->fetch();

$active_surah = $bookmark ? (int)$bookmark['surah_number'] : 1;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>NurFlow — Holy Quran Reader</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    .quran-container {
      display: grid;
      grid-template-columns: 320px 1fr;
      gap: 20px;
    }
    .surah-list-box {
      background: var(--bg-card);
      border: 1px solid var(--border-color);
      border-radius: var(--radius-md);
      height: 78vh;
      overflow-y: auto;
      padding: 15px;
    }
    .surah-item {
      padding: 12px 14px;
      border-radius: var(--radius-sm);
      margin-bottom: 8px;
      cursor: pointer;
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: 0.9rem;
      font-weight: 600;
      color: var(--text-dark);
      border: 1px solid var(--border-color);
      background: var(--bg-main);
      transition: all 0.2s ease;
    }
    .surah-item:hover, .surah-item.active {
      background: var(--emerald-light);
      color: var(--primary-emerald);
      border-color: var(--primary-emerald);
    }
    .reader-box {
      background: var(--bg-card);
      border: 1px solid var(--border-color);
      border-radius: var(--radius-md);
      height: 78vh;
      overflow-y: auto;
      padding: 30px;
    }
    .verse-card {
      padding: 24px 0;
      border-bottom: 1px solid var(--border-color);
    }
    .verse-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 15px;
    }
    .verse-badge {
      background: var(--emerald-light);
      color: var(--primary-emerald);
      padding: 4px 12px;
      border-radius: 12px;
      font-size: 0.85rem;
      font-weight: 700;
    }
    .bookmark-btn {
      background: transparent;
      border: 1px solid var(--accent-gold);
      color: var(--gold-hover);
      padding: 6px 14px;
      border-radius: 6px;
      font-size: 0.85rem;
      cursor: pointer;
      font-weight: 600;
    }
    .bookmark-btn:hover {
      background: var(--gold-light);
    }
    .translation-text {
      color: var(--text-dark);
      font-size: 1rem;
      line-height: 1.7;
      margin-top: 14px;
    }
  </style>
</head>
<body>

<div id="app-container">

  <?php include __DIR__ . '/../includes/sidebar.php'; ?>

  <div id="main-wrapper">

    <?php include __DIR__ . '/../includes/header.php'; ?>

    <div class="page-body">

      <div style="margin-bottom: 20px;">
        <h1 style="color: var(--primary-emerald);">📖 Holy Quran Reader</h1>
        <p style="color: var(--text-muted); font-size: 0.95rem;">
          Verified Uthmani script direct from authenticated Quran API endpoints.
        </p>
      </div>

      <div class="quran-container">
        <!-- Left Column: Surah List -->
        <div class="surah-list-box" id="surahListBox">
          <p style="text-align: center; color: var(--text-muted); padding: 20px;">Loading Surahs...</p>
        </div>

        <!-- Right Column: Verse Reader -->
        <div class="reader-box" id="readerBox">
          <p style="text-align: center; color: var(--text-muted); padding: 40px;">Select a Surah from the list to start reading.</p>
        </div>
      </div>

    </div>

    <?php include __DIR__ . '/../includes/footer.php'; ?>

  </div>
</div>

<script>
  let activeSurahId = <?= $active_surah; ?>;

  document.addEventListener('DOMContentLoaded', () => {
    loadSurahList();
  });

  function loadSurahList() {
    fetch('../api/quran_fetch.php?action=surah_list')
      .then(res => res.json())
      .then(data => {
        const listContainer = document.getElementById('surahListBox');
        listContainer.innerHTML = '';

        if (!data.chapters || data.chapters.length === 0) {
          listContainer.innerHTML = '<p style="text-align:center; color:red; padding: 20px;">Unable to load Surah list. Please check internet connection.</p>';
          return;
        }

        data.chapters.forEach(surah => {
          const item = document.createElement('div');
          item.className = `surah-item ${surah.id === activeSurahId ? 'active' : ''}`;
          item.innerHTML = `
            <div>
              <div style="font-weight:700;">${surah.id}. ${surah.name_simple}</div>
              <div style="font-size:0.75rem; color:var(--text-muted);">${surah.translated_name ? surah.translated_name.name : ''}</div>
            </div>
            <span class="arabic-text" style="font-size: 1.2rem; color: var(--primary-emerald);">${surah.name_arabic}</span>
          `;
          item.onclick = () => {
            document.querySelectorAll('.surah-item').forEach(el => el.classList.remove('active'));
            item.classList.add('active');
            selectSurah(surah.id, surah.name_simple);
          };
          listContainer.appendChild(item);
        });

        // Load verses for current active Surah
        const initialSurah = data.chapters.find(c => c.id === activeSurahId) || data.chapters[0];
        selectSurah(initialSurah.id, initialSurah.name_simple);
      })
      .catch(err => {
        console.error('Error fetching Surahs:', err);
        document.getElementById('surahListBox').innerHTML = '<p style="text-align:center; color:red; padding: 20px;">Network error while fetching Surah list.</p>';
      });
  }

  function selectSurah(surahId, surahName) {
    activeSurahId = surahId;
    const reader = document.getElementById('readerBox');
    reader.innerHTML = `<p style="text-align: center; color: var(--primary-emerald); padding: 40px; font-weight:600;">Loading Surah ${surahId}: ${surahName}...</p>`;

    fetch(`../api/quran_fetch.php?action=fetch_verses&surah=${surahId}`)
      .then(res => res.json())
      .then(data => {
        reader.innerHTML = '';

        if (!data.verses || data.verses.length === 0) {
          reader.innerHTML = '<p style="text-align: center; color: var(--text-muted); padding: 40px;">No verses available for this Surah.</p>';
          return;
        }

        // Display Header Banner for Surah
        const headerCard = document.createElement('div');
        headerCard.style.cssText = 'text-align: center; padding: 15px; margin-bottom: 20px; background: var(--gold-light); border: 1px solid var(--accent-gold); border-radius: var(--radius-sm);';
        headerCard.innerHTML = `<h2 style="color: var(--primary-emerald); margin: 0;">${surahName}</h2><p style="margin: 5px 0 0 0; font-size: 0.85rem; color: var(--text-dark);">Total Verses: ${data.verses.length}</p>`;
        reader.appendChild(headerCard);

        data.verses.forEach(verse => {
          const verseKey = verse.verse_key;
          const ayahNum = verseKey.split(':')[1];
          const arabicText = verse.text_uthmani;
          const translation = (verse.translations && verse.translations[0]) ? verse.translations[0].text : '';

          const card = document.createElement('div');
          card.className = 'verse-card';
          card.innerHTML = `
            <div class="verse-header">
              <span class="verse-badge">Surah ${verseKey}</span>
              <button class="bookmark-btn" onclick="saveBookmark(${surahId}, ${ayahNum}, '${surahName.replace(/'/g, "\\'")}')">🔖 Bookmark Ayah ${ayahNum}</button>
            </div>
            <div class="arabic-text" style="text-align: right; margin-top: 15px; font-size: 1.8rem; line-height: 2.2; color: var(--primary-emerald);">${arabicText}</div>
            <div class="translation-text">${translation}</div>
          `;
          reader.appendChild(card);
        });
      })
      .catch(err => {
        console.error('Error fetching verses:', err);
        reader.innerHTML = '<p style="text-align: center; color: red; padding: 40px;">Failed to load verses from Quran API.</p>';
      });
  }

  function saveBookmark(surahNum, ayahNum, surahName) {
    fetch('../api/quran_fetch.php?action=save_bookmark', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        surah_number: surahNum,
        ayah_number: ayahNum,
        surah_name: surahName
      })
    })
    .then(res => res.json())
    .then(data => {
      if (data.status === 'success') {
        alert(`✅ Bookmark saved for ${surahName} (${surahNum}:${ayahNum})!`);
      } else {
        alert('Failed to save bookmark.');
      }
    })
    .catch(err => console.error('Bookmark error:', err));
  }
</script>

</body>
</html>
