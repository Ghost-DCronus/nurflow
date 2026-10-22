<?php
// config/auth.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db.php';

// Helper function to guard internal pages
function require_login() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../index.php");
        exit();
    }
}

// 1. HANDLE REGISTRATION
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'register') {
    $fullname = trim($_POST['fullname'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $city     = trim($_POST['city'] ?? 'Sokoto');
    $password = $_POST['password'] ?? '';

    if (empty($fullname) || empty($email) || empty($password)) {
        header("Location: ../index.php?error=empty_fields");
        exit();
    }

    // Secure password hashing
    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    try {
        $stmt = $pdo->prepare("INSERT INTO users (fullname, email, password_hash, city) VALUES (?, ?, ?, ?)");
        $stmt->execute([$fullname, $email, $password_hash, $city]);
        
        // Auto-login after successful registration
        $_SESSION['user_id']  = $pdo->lastInsertId();
        $_SESSION['fullname'] = $fullname;
        $_SESSION['city']     = $city;
        
        header("Location: ../pages/dashboard.php");
        exit();
    } catch (\PDOException $e) {
        if ($e->getCode() == 23000) { // Email duplicate entry constraint
            header("Location: ../index.php?error=email_exists");
        } else {
            header("Location: ../index.php?error=db_error");
        }
        exit();
    }
}

// 2. HANDLE LOGIN
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        header("Location: ../index.php?error=empty_fields");
        exit();
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id']  = $user['id'];
        $_SESSION['fullname'] = $user['fullname'];
        $_SESSION['city']     = $user['city'];

        header("Location: ../pages/dashboard.php");
        exit();
    } else {
        header("Location: ../index.php?error=invalid_credentials");
        exit();
    }
}
?>
