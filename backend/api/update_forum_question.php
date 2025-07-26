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
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $desc = isset($_POST['desc']) ? trim($_POST['desc']) : '';
    
    if ($id <= 0 || $user_id <= 0 || empty($title) || empty($desc)) {
        echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
        exit();
    }
    
    // Gunakan waktu lokal pengguna jika dikirim, atau gunakan waktu server
    $current_time = isset($_POST['current_time']) ? $_POST['current_time'] : date('Y-m-d H:i:s');
    
    // Pastikan hanya user yang membuat pertanyaan yang bisa edit
    $stmt = $conn->prepare('UPDATE forum_questions SET title = ?, `desc` = ?, updated_at = ? WHERE id = ? AND user_id = ?');
    if ($stmt) {
        $stmt->bind_param('sssii', $title, $desc, $current_time, $id, $user_id);
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo json_encode(['success' => true, 'message' => 'Pertanyaan berhasil diupdate']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Pertanyaan tidak ditemukan atau bukan milik user']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal mengupdate pertanyaan: ' . $stmt->error]);
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