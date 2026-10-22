<?php
// api/hadith_fetch.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json');

$book = $_GET['book'] ?? 'bukhari';

// Map book slugs to the official free CDN endpoints
$map = [
    'bukhari'  => 'eng-bukhari',
    'muslim'   => 'eng-muslim',
    'tirmidhi' => 'eng-tirmidhi'
];

$edition = $map[$book] ?? 'eng-bukhari';
$apiUrl = "https://cdn.jsdelivr.net/gh/fawazahmed0/hadith-api@1/editions/{$edition}.json";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');
$response = curl_exec($ch);
curl_close($ch);

if ($response) {
    // Return first 30 hadiths for clean performance
    $data = json_decode($response, true);
    if (isset($data['hadiths'])) {
        $data['hadiths'] = array_slice($data['hadiths'], 0, 30);
    }
    echo json_encode($data);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to connect to Hadith CDN']);
}
exit();
?>
