<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once '../config/db.php';

try {
    $result = $conn->query("SELECT nomor, pertanyaan, kategori FROM pertanyaan_tesdiri ORDER BY nomor ASC");
    
    if (!$result) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Gagal mengambil data dari database']);
        exit;
    }
    
    $questions = [];
    while ($row = $result->fetch_assoc()) {
        $questions[] = $row;
    }
    
    http_response_code(200);
    echo json_encode(['success' => true, 'questions' => $questions]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error server: ' . $e->getMessage()]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>