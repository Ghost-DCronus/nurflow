<?php
// pages/hadith.php
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
  <title>NurFlow — Hadith & Sunnah</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    .book-selector {
      display: flex;
      gap: 12px;
      margin-bottom: 25px;
      flex-wrap: wrap;
    }
    .book-tab {
      padding: 12px 22px;
      border-radius: var(--radius-sm);
      border: 1px solid var(--border-color);
      background: var(--bg-card);
      color: var(--text-dark);
      font-weight: 600;
      cursor: pointer;
      font-size: 0.95rem;
      transition: all 0.2s ease;
    }
    .book-tab.active, .book-tab:hover {
      background: var(--primary-emerald);
      color: #fff;
      border-color: var(--primary-emerald);
    }
    .hadith-card {
      background: var(--bg-card);
      border: 1px solid var(--border-color);
      border-radius: var(--radius-md);
      padding: 24px;
      margin-bottom: 20px;
      box-shadow: var(--shadow-subtle);
    }
    .hadith-number {
      background: var(--gold-light);
      color: var(--gold-hover);
      padding: 4px 12px;
      border-radius: 12px;
      font-weight: 700;
      font-size: 0.85rem;
      display: inline-block;
      margin-bottom: 12px;
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
        <h1 style="color: var(--primary-emerald);">📜 Hadith & Sunnah Library</h1>
        <p style="color: var(--text-muted); font-size: 0.95rem;">
          Verified authentic narrations from Sahih al-Bukhari, Sahih Muslim, and Jami` at-Tirmidhi.
        </p>
      </div>

      <!-- Book Selector Tabs -->
      <div class="book-selector">
        <button class="book-tab active" onclick="loadHadithBook('bukhari', this)">Sahih al-Bukhari</button>
        <button class="book-tab" onclick="loadHadithBook('muslim', this)">Sahih Muslim</button>
        <button class="book-tab" onclick="loadHadithBook('tirmidhi', this)">Jami` at-Tirmidhi</button>
      </div>

      <!-- Hadiths List Container -->
      <div id="hadithListContainer">
        <p style="text-align: center; color: var(--text-muted); padding: 40px;">Loading authentic Hadiths...</p>
      </div>

    </div>

    <?php include __DIR__ . '/../includes/footer.php'; ?>

  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    fetchHadiths('bukhari');
  });

  function loadHadithBook(bookKey, element) {
    document.querySelectorAll('.book-tab').forEach(tab => tab.classList.remove('active'));
    element.classList.add('active');
    fetchHadiths(bookKey);
  }

  function fetchHadiths(book) {
    const container = document.getElementById('hadithListContainer');
    container.innerHTML = '<p style="text-align: center; color: var(--text-muted); padding: 40px;">Fetching Hadiths from CDN...</p>';

    fetch(`../api/hadith_fetch.php?book=${book}`)
      .then(res => res.json())
      .then(data => {
        container.innerHTML = '';
        const hadiths = data.hadiths || [];

        if (hadiths.length === 0) {
          container.innerHTML = '<p style="text-align: center; color: var(--text-muted);">No Hadiths found.</p>';
          return;
        }

        hadiths.forEach(h => {
          const card = document.createElement('div');
          card.className = 'hadith-card';
          card.innerHTML = `
            <span class="hadith-number">Hadith #${h.hadithnumber || h.number}</span>
            <p style="color: var(--text-dark); line-height: 1.7; font-size: 1rem; margin-top: 5px;">${h.text}</p>
          `;
          container.appendChild(card);
        });
      })
      .catch(err => {
        console.error('Hadith error:', err);
        container.innerHTML = '<p style="text-align: center; color: red;">Error connecting to Hadith service.</p>';
      });
  }
</script>

</body>
</html>
