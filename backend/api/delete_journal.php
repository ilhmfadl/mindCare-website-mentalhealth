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

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

require_once '../config/db.php';

try {
    // Get journal ID from URL parameter
    $id = intval($_GET['id'] ?? 0);
    
    if ($id <= 0) {
        throw new Exception('ID jurnal tidak valid');
    }
    
    // Use the connection from db.php
    global $conn;
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    $conn->set_charset("utf8mb4");
    
    // Check if journal exists
    $stmt = $conn->prepare("SELECT id, kategori, sumber, kutipan FROM jurnal WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception('Jurnal tidak ditemukan');
    }
    
    $journal = $result->fetch_assoc();
    
    // Delete journal
    $stmt = $conn->prepare("DELETE FROM jurnal WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if (!$stmt->execute()) {
        throw new Exception("Gagal menghapus jurnal: " . $stmt->error);
    }
    
    if ($stmt->affected_rows === 0) {
        throw new Exception('Jurnal tidak berhasil dihapus');
    }
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Jurnal berhasil dihapus',
        'data' => [
            'id' => $id,
            'kategori' => $journal['kategori'],
            'sumber' => $journal['sumber'],
            'kutipan' => $journal['kutipan']
        ]
    ]);
    
} catch (Exception $e) {
    error_log("Error in delete_journal.php: " . $e->getMessage());
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