<?php
// api/save_progress.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];
$data    = json_decode(file_get_contents('php://input'), true);
$type    = $data['type'] ?? '';

// Save Habit
if ($type === 'habit') {
    $habit_name = trim($data['habit_name'] ?? '');
    $status     = !empty($data['status']) ? 1 : 0;
    $date       = date('Y-m-d');

    if (empty($habit_name)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid habit name']);
        exit();
    }

    $stmt = $pdo->prepare("
        INSERT INTO user_habits (user_id, habit_name, status, log_date) 
        VALUES (?, ?, ?, ?) 
        ON DUPLICATE KEY UPDATE status = VALUES(status)
    ");
    $stmt->execute([$user_id, $habit_name, $status, $date]);

    echo json_encode(['status' => 'success', 'message' => 'Habit status updated']);
    exit();
}

// Save Dhikr
if ($type === 'dhikr') {
    $dhikr_title    = trim($data['dhikr_title'] ?? 'General Dhikr');
    $count_achieved = (int)($data['count'] ?? 0);
    $target_count   = (int)($data['target'] ?? 33);
    $date           = date('Y-m-d');

    $stmt = $pdo->prepare("
        INSERT INTO dhikr_logs (user_id, dhikr_title, count_achieved, target_count, log_date) 
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->execute([$user_id, $dhikr_title, $count_achieved, $target_count, $date]);

    echo json_encode(['status' => 'success', 'message' => 'Dhikr saved successfully']);
    exit();
}
?>
