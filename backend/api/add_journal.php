<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

require_once '../config/db.php';

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        throw new Exception('Invalid JSON input');
    }
    
    // Validate required fields
    $kategori = trim($input['kategori'] ?? '');
    $judul = trim($input['judul'] ?? '');
    $sumber = trim($input['sumber'] ?? '');
    $kutipan = trim($input['kutipan'] ?? '');
    
    if (empty($kategori)) {
        throw new Exception('Kategori tidak boleh kosong');
    }
    
    if (empty($judul)) {
        throw new Exception('Judul tidak boleh kosong');
    }
    
    if (empty($sumber)) {
        throw new Exception('Sumber tidak boleh kosong');
    }
    
    if (empty($kutipan)) {
        throw new Exception('Kutipan tidak boleh kosong');
    }
    
    // Validate kategori
    $allowedCategories = ['neurosis', 'psikotik', 'ptsd'];
    if (!in_array($kategori, $allowedCategories)) {
        throw new Exception('Kategori tidak valid. Pilih: neurosis, psikotik, atau ptsd');
    }
    
    // Use the connection from db.php
    global $conn;
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    $conn->set_charset("utf8mb4");
    
    // Insert new journal
    $stmt = $conn->prepare("INSERT INTO jurnal (kategori, judul, sumber, kutipan) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $kategori, $judul, $sumber, $kutipan);
    
    if (!$stmt->execute()) {
        throw new Exception("Gagal menambahkan jurnal: " . $stmt->error);
    }
    
    $journalId = $conn->insert_id;
    
    // Get the newly created journal
    $stmt = $conn->prepare("SELECT id, kategori, judul, sumber, kutipan, created_at, updated_at FROM jurnal WHERE id = ?");
    $stmt->bind_param("i", $journalId);
    $stmt->execute();
    $result = $stmt->get_result();
    $newJournal = $result->fetch_assoc();
    
    http_response_code(201);
    echo json_encode([
        'success' => true,
        'message' => 'Jurnal berhasil ditambahkan',
        'data' => $newJournal
    ]);
    
} catch (Exception $e) {
    error_log("Error in add_journal.php: " . $e->getMessage());
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($conn)) {
        $conn->close();
    }
}
?> 