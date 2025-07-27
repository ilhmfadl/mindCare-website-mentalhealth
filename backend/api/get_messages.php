<?php
require_once '../config/db.php';
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Validate and sanitize input
$conversation_id = isset($_GET['conversation_id']) ? intval($_GET['conversation_id']) : null;

if (!$conversation_id || $conversation_id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid or missing conversation_id']);
    exit;
}

try {
    // Verify conversation exists and user has access
    $verify_stmt = $conn->prepare("
        SELECT id, user_id, admin_id, status 
        FROM conversations 
        WHERE id = ? AND status != 'deleted'
    ");
    $verify_stmt->bind_param('i', $conversation_id);
    $verify_stmt->execute();
    $conv_check = $verify_stmt->get_result()->fetch_assoc();
    
    if (!$conv_check) {
        http_response_code(404);
        echo json_encode(['error' => 'Conversation not found or access denied']);
        exit;
    }
    
    // Ambil semua pesan dalam conversation yang valid
    $stmt = $conn->prepare("
        SELECT 
            m.id,
            m.conversation_id,
            m.sender_id,
            m.sender_role,
            m.message,
            m.file_url,
            m.file_type,
            m.file_name,
            m.is_read,
            m.created_at,
            u.username as sender_name,
            u.fullName as sender_full_name
        FROM messages m
        LEFT JOIN users u ON m.sender_id = u.id
        WHERE m.conversation_id = ? 
        AND m.message IS NOT NULL 
        AND m.message != ''
        ORDER BY m.created_at ASC, m.id ASC
    ");
    
    $stmt->bind_param('i', $conversation_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $messages = [];
    while ($row = $result->fetch_assoc()) {
        // Sanitize message content
        $row['message'] = htmlspecialchars($row['message'], ENT_QUOTES, 'UTF-8');
        $messages[] = $row;
    }
    
    // Ambil info conversation yang lengkap
    $conv_stmt = $conn->prepare("
        SELECT 
            c.id,
            c.user_id,
            c.admin_id,
            c.status,
            c.created_at,
            c.updated_at,
            u.username as user_name,
            u.fullName as user_full_name,
            a.username as admin_name,
            a.fullName as admin_full_name
        FROM conversations c
        JOIN users u ON c.user_id = u.id
        JOIN users a ON c.admin_id = a.id
        WHERE c.id = ? AND c.status != 'deleted'
    ");
    
    $conv_stmt->bind_param('i', $conversation_id);
    $conv_stmt->execute();
    $conv_result = $conv_stmt->get_result();
    $conversation = $conv_result->fetch_assoc();
    
    if (!$conversation) {
        http_response_code(404);
        echo json_encode(['error' => 'Conversation not found']);
        exit;
    }
    
    echo json_encode([
        'success' => true,
        'conversation' => $conversation,
        'messages' => $messages,
        'total_messages' => count($messages)
    ]);
    
    $stmt->close();
    $conv_stmt->close();
    $verify_stmt->close();
    
} catch (Exception $e) {
    error_log("get_messages.php error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Database error', 'details' => $e->getMessage()]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
} 