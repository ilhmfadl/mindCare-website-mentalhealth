<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method tidak diizinkan']);
    exit;
}

try {
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input || !isset($input['nomor'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Nomor pertanyaan wajib diisi']);
        exit;
    }

    $nomor = intval($input['nomor']);

    // Cek apakah pertanyaan dengan nomor tersebut ada
    $stmt = $conn->prepare("SELECT nomor FROM pertanyaan_tesdiri WHERE nomor = ?");
    $stmt->bind_param("i", $nomor);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Pertanyaan dengan nomor ' . $nomor . ' tidak ditemukan']);
        exit;
    }
    
    // Hapus pertanyaan
    $stmt = $conn->prepare("DELETE FROM pertanyaan_tesdiri WHERE nomor = ?");
    $stmt->bind_param("i", $nomor);
    
    if ($stmt->execute()) {
        // Reorder nomor setelah penghapusan
        $stmt = $conn->prepare("SET @rank = 0");
        $stmt->execute();
        
        $stmt = $conn->prepare("UPDATE pertanyaan_tesdiri SET nomor = (@rank := @rank + 1) ORDER BY nomor");
        $stmt->execute();
        
        http_response_code(200);
        echo json_encode([
            'success' => true, 
            'message' => 'Pertanyaan berhasil dihapus',
            'data' => [
                'nomor' => $nomor
            ]
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Gagal menghapus pertanyaan dari database']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error server: ' . $e->getMessage()]);
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($conn)) {
        $conn->close();
    }
}
?> 