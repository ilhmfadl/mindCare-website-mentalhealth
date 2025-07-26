<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
    
    if ($id <= 0 || $user_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID atau user_id tidak valid']);
        exit();
    }
    
    // Cek data yang ada di database
    $checkQuery = "SELECT user_id FROM forum_questions WHERE id = ?";
    $checkStmt = $conn->prepare($checkQuery);
    $debug_info = [];
    
    if ($checkStmt) {
        $checkStmt->bind_param('i', $id);
        $checkStmt->execute();
        $result = $checkStmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $db_user_id = $row['user_id'];
            $debug_info = [
                'request_id' => $id,
                'request_user_id' => $user_id,
                'db_user_id' => $db_user_id,
                'match' => ($db_user_id == $user_id)
            ];
        } else {
            $debug_info = [
                'request_id' => $id,
                'request_user_id' => $user_id,
                'db_user_id' => 'not_found',
                'match' => false
            ];
        }
        $checkStmt->close();
    }
    
    // Pastikan hanya user yang membuat pertanyaan yang bisa hapus
    $stmt = $conn->prepare('DELETE FROM forum_questions WHERE id = ? AND user_id = ?');
    if ($stmt) {
        $stmt->bind_param('ii', $id, $user_id);
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo json_encode(['success' => true, 'message' => 'Pertanyaan berhasil dihapus']);
            } else {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Pertanyaan tidak ditemukan atau bukan milik user',
                    'debug' => $debug_info
                ]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menghapus pertanyaan: ' . $stmt->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Prepare statement gagal: ' . $conn->error]);
    }
    $conn->close();
    exit();
}
echo json_encode(['success' => false, 'message' => 'Metode tidak diizinkan']);
?> 