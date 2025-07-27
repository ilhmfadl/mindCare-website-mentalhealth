<?php
require_once '../config/db.php';
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Cek metode
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Validasi input
$sender_id = isset($_POST['sender_id']) ? intval($_POST['sender_id']) : null;
$receiver_id = isset($_POST['receiver_id']) ? intval($_POST['receiver_id']) : null;
$sender_role = isset($_POST['sender_role']) ? $_POST['sender_role'] : null;
$message = isset($_POST['message']) ? $_POST['message'] : null;

// Debug log
error_log("send_message.php - sender_id: $sender_id, receiver_id: $receiver_id, sender_role: $sender_role, message: $message");

if (!$sender_id || !$sender_role || !$receiver_id) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields', 'sender_id' => $sender_id, 'receiver_id' => $receiver_id, 'sender_role' => $sender_role]);
    exit;
}

$file_url = null;
$file_type = null;
$file_name = null;

// File upload handling
if (isset($_FILES['file'])) {
    $file = $_FILES['file'];
    $file_type = mime_content_type($file['tmp_name']);
    $file_name = basename($file['name']);
    $file_size = $file['size'];

    $maxFileSize = 10 * 1024 * 1024; // 10MB
    $maxImageSize = 3 * 1024 * 1024; // 3MB

    if (strpos($file_type, 'image/') === 0) {
        if ($file_size > $maxImageSize) {
            http_response_code(400);
            echo json_encode(['error' => 'Image size exceeds 3MB']);
            exit;
        }
    } else {
        if ($file_size > $maxFileSize) {
            http_response_code(400);
            echo json_encode(['error' => 'File size exceeds 10MB']);
            exit;
        }
    }

    $upload_dir = '../../uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    $target_file = $upload_dir . uniqid() . '_' . $file_name;
    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        $file_url = str_replace('../../', '/', $target_file);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to upload file']);
        exit;
    }
}

try {
    // Simpan pesan ke database
    $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, sender_role, message, file_url, file_type, file_name) VALUES (?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(['error' => 'Prepare failed', 'details' => $conn->error]);
        $conn->close();
        exit;
    }

    $stmt->bind_param('iisssss', $sender_id, $receiver_id, $sender_role, $message, $file_url, $file_type, $file_name);
    
    if ($stmt->execute()) {
        $message_id = $stmt->insert_id;
        
        // Cek apakah ini pesan pertama user ke admin
        if ($sender_role === 'user') {
            // Cek jumlah pesan user ke admin
            $check_stmt = $conn->prepare("SELECT COUNT(*) as cnt FROM messages WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)");
            if ($check_stmt) {
                $check_stmt->bind_param('iiii', $sender_id, $receiver_id, $receiver_id, $sender_id);
                $check_stmt->execute();
                $check_result = $check_stmt->get_result();
                $row = $check_result->fetch_assoc();
                $check_stmt->close();
                
                if ($row['cnt'] == 1) { // Pesan pertama
                    // Ambil nama user
                    $user_stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
                    if ($user_stmt) {
                        $user_stmt->bind_param('i', $sender_id);
                        $user_stmt->execute();
                        $user_result = $user_stmt->get_result();
                        $user_row = $user_result->fetch_assoc();
                        $user_stmt->close();
                        $user_name = $user_row ? $user_row['username'] : 'Pengguna';
                        $agent_message = "Halo $user_name, Perkenalkan nama saya Mindhelp, Apakah ada yang bisa saya bantu?";
                        
                        // Kirim pesan agent
                        $agent_stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, sender_role, message) VALUES (?, ?, 'agent', ?)");
                        if ($agent_stmt) {
                            $agent_sender_id = 0;
                            $agent_stmt->bind_param('iis', $agent_sender_id, $sender_id, $agent_message);
                            $agent_stmt->execute();
                            $agent_stmt->close();
                        }
                    }
                }
            }
        }
        
        echo json_encode(['success' => true, 'message_id' => $message_id]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to send message', 'details' => $stmt->error]);
    }
    
    $stmt->close();
} catch (Exception $e) {
    error_log("send_message.php error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Database error', 'details' => $e->getMessage()]);
} finally {
    $conn->close();
} 