<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method tidak diizinkan']);
    exit;
}

try {
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input || !isset($input['pertanyaan']) || empty(trim($input['pertanyaan']))) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Pertanyaan wajib diisi']);
        exit;
    }

    $pertanyaan = trim($input['pertanyaan']);
    $kategori = isset($input['kategori']) ? $input['kategori'] : 'neurosis';
    
    // Validasi kategori
    $validKategori = ['neurosis', 'psikotik', 'ptsd'];
    if (!in_array($kategori, $validKategori)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Kategori tidak valid']);
        exit;
    }

    // Dapatkan nomor terakhir
    $stmt = $conn->prepare("SELECT MAX(nomor) as max_nomor FROM pertanyaan_tesdiri");
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $next_nomor = ($row['max_nomor'] ?? 0) + 1;
    
    // Insert pertanyaan baru dengan kategori
    $stmt = $conn->prepare("INSERT INTO pertanyaan_tesdiri (nomor, pertanyaan, kategori) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $next_nomor, $pertanyaan, $kategori);
    
    if ($stmt->execute()) {
        http_response_code(201);
        echo json_encode([
            'success' => true, 
            'message' => 'Pertanyaan berhasil ditambahkan',
            'data' => [
                'nomor' => $next_nomor,
                'pertanyaan' => $pertanyaan,
                'kategori' => $kategori
            ]
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Gagal menambahkan pertanyaan ke database']);
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