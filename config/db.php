<?php
// config/db.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Read dynamic environment variables (provided automatically by Railway)
$host    = getenv('MYSQLHOST') ?: 'localhost';
$db      = getenv('MYSQLDATABASE') ?: 'nurflow_db';
$user    = getenv('MYSQLUSER') ?: 'root';
$pass    = getenv('MYSQLPASSWORD') ?: '';
$port    = getenv('MYSQLPORT') ?: '3306';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
?>
