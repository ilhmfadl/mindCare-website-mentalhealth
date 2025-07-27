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
$user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : null;
$admin_id = isset($_POST['admin_id']) ? intval($_POST['admin_id']) : null;

if (!$user_id || !$admin_id) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing user_id or admin_id']);
    exit;
}

try {
    // Cek apakah conversation sudah ada
    $check_stmt = $conn->prepare("SELECT id FROM conversations WHERE user_id = ? AND admin_id = ?");
    $check_stmt->bind_param('ii', $user_id, $admin_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Conversation sudah ada, return ID yang ada
        $row = $result->fetch_assoc();
        echo json_encode([
            'success' => true,
            'conversation_id' => $row['id'],
            'message' => 'Conversation already exists'
        ]);
    } else {
        // Buat conversation baru
        $stmt = $conn->prepare("INSERT INTO conversations (user_id, admin_id) VALUES (?, ?)");
        $stmt->bind_param('ii', $user_id, $admin_id);
        
        if ($stmt->execute()) {
            $conversation_id = $stmt->insert_id;
            echo json_encode([
                'success' => true,
                'conversation_id' => $conversation_id,
                'message' => 'Conversation created successfully'
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to create conversation', 'details' => $stmt->error]);
        }
        $stmt->close();
    }
    
    $check_stmt->close();
    
} catch (Exception $e) {
    error_log("get_or_create_conversation.php error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Database error', 'details' => $e->getMessage()]);
} finally {
    $conn->close();
} 