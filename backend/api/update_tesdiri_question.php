<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: PUT, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method tidak diizinkan']);
    exit;
}

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Debug log
    error_log("Update request received: " . json_encode($input));

    if (!$input || !isset($input['nomor']) || !isset($input['pertanyaan']) || empty(trim($input['pertanyaan']))) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Nomor dan pertanyaan wajib diisi']);
        exit;
    }

    $nomor = intval($input['nomor']);
    $pertanyaan = trim($input['pertanyaan']);
    $kategori = isset($input['kategori']) ? $input['kategori'] : null;
    
    // Validasi kategori jika disediakan
    if ($kategori !== null) {
        $validKategori = ['neurosis', 'psikotik', 'ptsd'];
        if (!in_array($kategori, $validKategori)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Kategori tidak valid']);
            exit;
        }
    }
    
    // Debug log
    error_log("Processing update for nomor: $nomor, pertanyaan: $pertanyaan, kategori: $kategori");

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
    
    // Update pertanyaan dengan atau tanpa kategori
    if ($kategori !== null) {
        $stmt = $conn->prepare("UPDATE pertanyaan_tesdiri SET pertanyaan = ?, kategori = ? WHERE nomor = ?");
        $stmt->bind_param("ssi", $pertanyaan, $kategori, $nomor);
    } else {
        $stmt = $conn->prepare("UPDATE pertanyaan_tesdiri SET pertanyaan = ? WHERE nomor = ?");
        $stmt->bind_param("si", $pertanyaan, $nomor);
    }
    
    if ($stmt->execute()) {
        // Verify the update was successful
        $stmt = $conn->prepare("SELECT nomor, pertanyaan, kategori FROM pertanyaan_tesdiri WHERE nomor = ?");
        $stmt->bind_param("i", $nomor);
        $stmt->execute();
        $verify_result = $stmt->get_result();
        $updated_row = $verify_result->fetch_assoc();
        
        http_response_code(200);
        echo json_encode([
            'success' => true, 
            'message' => 'Pertanyaan berhasil diupdate',
            'data' => [
                'nomor' => $updated_row['nomor'],
                'pertanyaan' => $updated_row['pertanyaan'],
                'kategori' => $updated_row['kategori']
            ]
        ]);
        
        // Debug log
        error_log("Update successful for nomor: $nomor");
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Gagal mengupdate pertanyaan di database']);
        error_log("Update failed for nomor: $nomor");
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error server: ' . $e->getMessage()]);
    error_log("Update error: " . $e->getMessage());
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($conn)) {
        $conn->close();
    }
}
?> 