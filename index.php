<?php
// index.php
session_start();

// If user is already logged in, redirect straight to dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: pages/dashboard.php");
    exit();
}

$error = $_GET['error'] ?? '';
$msg   = $_GET['msg'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>NurFlow — Login & Sign Up</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    body {
      background-color: var(--bg-main);
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      padding: 20px;
    }

    .auth-card {
      background-color: var(--bg-card);
      border: 1px solid var(--border-color);
      border-radius: var(--radius-md);
      box-shadow: var(--shadow-subtle);
      width: 100%;
      max-width: 420px;
      padding: 35px 30px;
      text-align: center;
    }

    .auth-brand {
      font-size: 2rem;
      font-weight: 800;
      color: var(--primary-emerald);
      margin-bottom: 5px;
    }

    .auth-brand span {
      color: var(--accent-gold);
    }

    .auth-subtitle {
      font-size: 0.9rem;
      color: var(--text-muted);
      margin-bottom: 25px;
    }

    .form-group {
      text-align: left;
      margin-bottom: 18px;
    }

    .form-group label {
      display: block;
      font-size: 0.85rem;
      font-weight: 600;
      color: var(--text-dark);
      margin-bottom: 6px;
    }

    .form-control {
      width: 100%;
      padding: 12px 14px;
      border-radius: var(--radius-sm);
      border: 1px solid var(--border-color);
      background-color: var(--bg-main);
      color: var(--text-dark);
      font-size: 0.95rem;
      outline: none;
      transition: border-color 0.2s ease;
    }

    .form-control:focus {
      border-color: var(--primary-emerald);
    }

    .alert {
      padding: 10px 14px;
      border-radius: var(--radius-sm);
      font-size: 0.85rem;
      margin-bottom: 20px;
      text-align: left;
    }

    .alert-danger {
      background-color: #fee2e2;
      color: #991b1b;
      border: 1px solid #fca5a5;
    }

    .alert-success {
      background-color: var(--emerald-light);
      color: var(--primary-emerald);
      border: 1px solid #a7f3d0;
    }

    .btn-block {
      width: 100%;
      padding: 12px;
      font-size: 1rem;
    }

    .auth-toggle {
      margin-top: 20px;
      font-size: 0.88rem;
      color: var(--text-muted);
      cursor: pointer;
      text-decoration: underline;
      font-weight: 600;
    }

    .auth-toggle:hover {
      color: var(--primary-emerald);
    }
  </style>
</head>
<body>

<div class="auth-card">
  <div class="auth-brand">✨ Nur<span>Flow</span></div>
  <p class="auth-subtitle">Your Islamic Habit & Educational Companion</p>

  <?php if ($error === 'empty_fields'): ?>
    <div class="alert alert-danger">⚠️ Please fill in all required fields.</div>
  <?php elseif ($error === 'invalid_credentials'): ?>
    <div class="alert alert-danger">❌ Invalid email or password. Please try again.</div>
  <?php elseif ($error === 'email_exists'): ?>
    <div class="alert alert-danger">⚠️ This email is already registered. Please login.</div>
  <?php elseif ($msg === 'logged_out'): ?>
    <div class="alert alert-success">✅ You have been logged out safely.</div>
  <?php endif; ?>

  <!-- LOGIN FORM -->
  <form id="loginForm" action="config/auth.php" method="POST">
    <input type="hidden" name="action" value="login">
    
    <div class="form-group">
      <label>Email Address</label>
      <input type="email" name="email" class="form-control" required placeholder="name@example.com">
    </div>

    <div class="form-group">
      <label>Password</label>
      <input type="password" name="password" class="form-control" required placeholder="••••••••">
    </div>

    <button type="submit" class="btn btn-primary btn-block">Login to Account</button>
  </form>

  <!-- REGISTRATION FORM (Hidden by default) -->
  <form id="registerForm" action="config/auth.php" method="POST" style="display: none;">
    <input type="hidden" name="action" value="register">

    <div class="form-group">
      <label>Full Name</label>
      <input type="text" name="fullname" class="form-control" required placeholder="e.g. Sanusi Mansur">
    </div>

    <div class="form-group">
      <label>Email Address</label>
      <input type="email" name="email" class="form-control" required placeholder="name@example.com">
    </div>

    <div class="form-group">
      <label>City Location (for Prayer Times)</label>
      <input type="text" name="city" class="form-control" placeholder="e.g. Sokoto" value="Sokoto">
    </div>

    <div class="form-group">
      <label>Password</label>
      <input type="password" name="password" class="form-control" required placeholder="••••••••">
    </div>

    <button type="submit" class="btn btn-gold btn-block">Create Free Account</button>
  </form>

  <div class="auth-toggle" id="toggleBtn" onclick="toggleAuth()">Don't have an account? Sign Up</div>
</div>

<script>
  function toggleAuth() {
    const login = document.getElementById('loginForm');
    const register = document.getElementById('registerForm');
    const toggle = document.getElementById('toggleBtn');

    if (login.style.display === 'none') {
      login.style.display = 'block';
      register.style.display = 'none';
      toggle.innerText = "Don't have an account? Sign Up";
    } else {
      login.style.display = 'none';
      register.style.display = 'block';
      toggle.innerText = "Already have an account? Login";
    }
  }
</script>

</body>
</html>