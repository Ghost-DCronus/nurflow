<?php
// pages/zakat.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';

require_login();

$user_id = $_SESSION['user_id'];

// Default Nisab baseline (85 grams of gold standard in USD/Local currency)
$gold_price_per_gram = 132.50; 
$nisab_threshold     = 85 * $gold_price_per_gram; // ~$11,262.50
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>NurFlow — Zakat Calculator</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    .zakat-grid {
      display: grid;
      grid-template-columns: 1fr 340px;
      gap: 25px;
    }
    .input-group {
      margin-bottom: 16px;
    }
    .input-group label {
      display: block;
      font-size: 0.85rem;
      font-weight: 600;
      color: var(--text-dark);
      margin-bottom: 6px;
    }
    .form-input {
      width: 100%;
      padding: 10px 14px;
      border-radius: var(--radius-sm);
      border: 1px solid var(--border-color);
      background: var(--bg-main);
      color: var(--text-dark);
      font-size: 0.95rem;
      outline: none;
    }
    .form-input:focus {
      border-color: var(--primary-emerald);
    }
    .summary-card {
      background: var(--bg-card);
      border: 1px solid var(--border-color);
      border-radius: var(--radius-md);
      padding: 24px;
      position: sticky;
      top: 90px;
      box-shadow: var(--shadow-subtle);
    }
    .summary-row {
      display: flex;
      justify-content: space-between;
      padding: 10px 0;
      border-bottom: 1px solid var(--border-color);
      font-size: 0.95rem;
    }
    .summary-total {
      font-size: 1.3rem;
      font-weight: 800;
      color: var(--primary-emerald);
      margin-top: 15px;
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
        <h1 style="color: var(--primary-emerald);">💰 Precise Zakat Calculator</h1>
        <p style="color: var(--text-muted); font-size: 0.95rem;">
          Calculate your 2.5% annual Zakat obligation accurately according to Shariah rules.
        </p>
      </div>

      <div class="zakat-grid">

        <!-- Asset Form Inputs -->
        <div class="card">
          <div class="card-header">
            <span class="card-title">Assets & Liabilities (Held for 1 Lunar Year)</span>
          </div>

          <div class="input-group">
            <label>1. Cash in Hand & Bank Accounts</label>
            <input type="number" id="cashInput" class="form-input" placeholder="0.00" oninput="calculateZakat()">
          </div>

          <div class="input-group">
            <label>2. Gold & Silver Value (Market Value)</label>
            <input type="number" id="preciousMetalsInput" class="form-input" placeholder="0.00" oninput="calculateZakat()">
          </div>

          <div class="input-group">
            <label>3. Business Assets & Inventory Value</label>
            <input type="number" id="businessInput" class="form-input" placeholder="0.00" oninput="calculateZakat()">
          </div>

          <div class="input-group">
            <label>4. Stocks, Shares & Investments</label>
            <input type="number" id="stocksInput" class="form-input" placeholder="0.00" oninput="calculateZakat()">
          </div>

          <hr style="border: 0; border-top: 1px solid var(--border-color); margin: 20px 0;">

          <div class="input-group">
            <label style="color: #b91c1c;">5. Immediate Short-term Debts / Liabilities (Deductible)</label>
            <input type="number" id="debtsInput" class="form-input" placeholder="0.00" oninput="calculateZakat()">
          </div>
        </div>

        <!-- Summary Column -->
        <div class="summary-card">
          <h3 style="color: var(--primary-emerald); margin-bottom: 15px;">Calculation Summary</h3>

          <div class="summary-row">
            <span>Gold Nisab Standard:</span>
            <strong>$<?= number_format($nisab_threshold, 2); ?></strong>
          </div>

          <div class="summary-row">
            <span>Gross Assets:</span>
            <strong id="grossAssetsDisplay">$0.00</strong>
          </div>

          <div class="summary-row">
            <span>Net Wealth:</span>
            <strong id="netWealthDisplay">$0.00</strong>
          </div>

          <div class="summary-row">
            <span>Nisab Status:</span>
            <span id="nisabStatusBadge" style="font-weight: 700; color: var(--text-muted);">Below Nisab</span>
          </div>

          <div class="summary-total" style="text-align: center; margin-top: 25px; padding-top: 15px; border-top: 2px solid var(--primary-emerald);">
            <div style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600;">YOUR ZAKAT DUE (2.5%)</div>
            <div id="zakatDueDisplay" style="font-size: 2rem; color: var(--primary-emerald); margin-top: 5px;">$0.00</div>
          </div>
        </div>

      </div>

    </div><!-- /.page-body -->

    <?php include __DIR__ . '/../includes/footer.php'; ?>

  </div><!-- /#main-wrapper -->

</div><!-- /#app-container -->

<script>
  const nisabThreshold = <?= (float)$nisab_threshold; ?>;

  function calculateZakat() {
    const cash = parseFloat(document.getElementById('cashInput').value) || 0;
    const metals = parseFloat(document.getElementById('preciousMetalsInput').value) || 0;
    const business = parseFloat(document.getElementById('businessInput').value) || 0;
    const stocks = parseFloat(document.getElementById('stocksInput').value) || 0;
    const debts = parseFloat(document.getElementById('debtsInput').value) || 0;

    const gross = cash + metals + business + stocks;
    const net = gross - debts;

    document.getElementById('grossAssetsDisplay').innerText = '$' + gross.toLocaleString('en-US', {minimumFractionDigits: 2});
    document.getElementById('netWealthDisplay').innerText = '$' + net.toLocaleString('en-US', {minimumFractionDigits: 2});

    const badge = document.getElementById('nisabStatusBadge');
    const zakatDisplay = document.getElementById('zakatDueDisplay');

    if (net >= nisabThreshold) {
      badge.innerText = '✅ Nisab Met';
      badge.style.color = 'var(--primary-emerald)';
      const zakatDue = net * 0.025;
      zakatDisplay.innerText = '$' + zakatDue.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    } else {
      badge.innerText = 'Below Nisab';
      badge.style.color = 'var(--text-muted)';
      zakatDisplay.innerText = '$0.00';
    }
  }
</script>

</body>
</html>
