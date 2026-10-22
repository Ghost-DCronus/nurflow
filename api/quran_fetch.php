<?php
// api/quran_fetch.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? 'surah_list';

// 1. GET /api/v4/chapters - Fetch List of All 114 Surahs
if ($action === 'surah_list') {
    $apiUrl = "https://api.quran.com/api/v4/chapters?language=en";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');
    $response = curl_exec($ch);
    curl_close($ch);

    if ($response) {
        echo $response;
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Unable to connect to Quran API']);
    }
    exit();
}

// 2. GET /api/v4/verses/by_chapter/{id} - Fetch Verses & Translations for Selected Surah
if ($action === 'fetch_verses') {
    $surah = (int)($_GET['surah'] ?? 1);
    $apiUrl = "https://api.quran.com/api/v4/verses/by_chapter/{$surah}?language=en&words=false&translations=131&fields=text_uthmani&page=1&per_page=300";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');
    $response = curl_exec($ch);
    curl_close($ch);

    if ($response) {
        echo $response;
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to load verses']);
    }
    exit();
}

// 3. POST - Save Reading Bookmark to MySQL Database
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'save_bookmark') {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
        exit();
    }

    $user_id = $_SESSION['user_id'];
    $data    = json_decode(file_get_contents('php://input'), true);

    $surah_number = (int)($data['surah_number'] ?? 1);
    $ayah_number  = (int)($data['ayah_number'] ?? 1);
    $surah_name   = trim($data['surah_name'] ?? 'Al-Fatiha');

    try {
        $stmt = $pdo->prepare("
            INSERT INTO quran_progress (user_id, surah_number, ayah_number, surah_name) 
            VALUES (?, ?, ?, ?) 
            ON DUPLICATE KEY UPDATE 
                surah_number = VALUES(surah_number), 
                ayah_number = VALUES(ayah_number), 
                surah_name = VALUES(surah_name)
        ");
        $stmt->execute([$user_id, $surah_number, $ayah_number, $surah_name]);

        echo json_encode(['status' => 'success', 'message' => 'Bookmark saved successfully']);
    } catch (\PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit();
}
?>
