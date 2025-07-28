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

if (!$sender_id || !$receiver_id || !$sender_role) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields']);
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
    // Tentukan user_id dan admin_id berdasarkan sender_role
    $user_id = ($sender_role === 'user') ? $sender_id : $receiver_id;
    $admin_id = ($sender_role === 'user') ? $receiver_id : $sender_id;
    
    // Cek atau buat conversation
    $conv_stmt = $conn->prepare("SELECT id FROM conversations WHERE user_id = ? AND admin_id = ?");
    $conv_stmt->bind_param('ii', $user_id, $admin_id);
    $conv_stmt->execute();
    $conv_result = $conv_stmt->get_result();
    
    if ($conv_result->num_rows > 0) {
        $conversation = $conv_result->fetch_assoc();
        $conversation_id = $conversation['id'];
    } else {
        // Buat conversation baru
        $create_conv_stmt = $conn->prepare("INSERT INTO conversations (user_id, admin_id) VALUES (?, ?)");
        $create_conv_stmt->bind_param('ii', $user_id, $admin_id);
        $create_conv_stmt->execute();
        $conversation_id = $create_conv_stmt->insert_id;
        $create_conv_stmt->close();
    }
    $conv_stmt->close();
    
    // Simpan pesan ke database
    $stmt = $conn->prepare("INSERT INTO messages (conversation_id, sender_id, sender_role, message, file_url, file_type, file_name) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('iisssss', $conversation_id, $sender_id, $sender_role, $message, $file_url, $file_type, $file_name);
    
    if ($stmt->execute()) {
        $message_id = $stmt->insert_id;
        
        // Gunakan waktu client dengan zona waktu yang benar
        $current_time = date('Y-m-d H:i:s');
        
        // Jika client mengirim waktu dengan zona waktu, konversi ke UTC
        if (isset($_POST['current_time']) && isset($_POST['timezone'])) {
            $client_time = $_POST['current_time'];
            $client_timezone = $_POST['timezone'];
            
            try {
                // Buat DateTime object dengan zona waktu client
                $client_datetime = new DateTime($client_time, new DateTimeZone($client_timezone));
                // Konversi ke UTC untuk disimpan di database
                $client_datetime->setTimezone(new DateTimeZone('UTC'));
                $current_time = $client_datetime->format('Y-m-d H:i:s');
            } catch (Exception $e) {
                // Jika gagal, gunakan waktu server
                error_log("Timezone conversion failed: " . $e->getMessage());
                $current_time = date('Y-m-d H:i:s');
            }
        } elseif (isset($_POST['current_time'])) {
            // Jika hanya ada waktu tanpa zona waktu, asumsikan UTC
            $current_time = $_POST['current_time'];
        }
        
        // Update conversation updated_at dengan waktu yang akurat
        $update_conv_stmt = $conn->prepare("UPDATE conversations SET updated_at = ? WHERE id = ?");
        $update_conv_stmt->bind_param('si', $current_time, $conversation_id);
        $update_conv_stmt->execute();
        $update_conv_stmt->close();
        
        // Cek apakah ini pesan pertama user ke admin
        if ($sender_role === 'user') {
            $check_stmt = $conn->prepare("SELECT COUNT(*) as cnt FROM messages WHERE conversation_id = ?");
            $check_stmt->bind_param('i', $conversation_id);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            $row = $check_result->fetch_assoc();
            $check_stmt->close();
            
            if ($row['cnt'] == 1) { // Pesan pertama
                // Ambil nama user
                $user_stmt = $conn->prepare("SELECT username, fullName FROM users WHERE id = ?");
                $user_stmt->bind_param('i', $sender_id);
                $user_stmt->execute();
                $user_result = $user_stmt->get_result();
                $user_row = $user_result->fetch_assoc();
                $user_stmt->close();
                
                $user_name = $user_row['fullName'] ?: $user_row['username'];
                $agent_message = "Halo $user_name, Perkenalkan nama saya Mindhelp, Apakah ada yang bisa saya bantu?";
                
                // Kirim pesan agent
                $agent_stmt = $conn->prepare("INSERT INTO messages (conversation_id, sender_id, sender_role, message) VALUES (?, 0, 'agent', ?)");
                $agent_stmt->bind_param('is', $conversation_id, $agent_message);
                $agent_stmt->execute();
                $agent_stmt->close();
            }
        }
        
        echo json_encode([
            'success' => true, 
            'message_id' => $message_id,
            'conversation_id' => $conversation_id
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to send message', 'details' => $stmt->error]);
    }
    
    $stmt->close();
    
} catch (Exception $e) {
    error_log("send_message_new.php error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Database error', 'details' => $e->getMessage()]);
} finally {
    $conn->close();
} 