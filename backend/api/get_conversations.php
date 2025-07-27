<?php
require_once '../config/db.php';
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : null;

if (!$user_id) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing user_id']);
    exit;
}

try {
    // Ambil semua conversation untuk user (baik sebagai user maupun admin)
    $stmt = $conn->prepare("
        SELECT 
            c.id as conversation_id,
            c.user_id,
            c.admin_id,
            c.status,
            c.created_at,
            c.updated_at,
            u.username as user_name,
            u.fullName as user_full_name,
            a.username as admin_name,
            a.fullName as admin_full_name,
            (SELECT COUNT(*) FROM messages m WHERE m.conversation_id = c.id) as message_count,
            (SELECT m.message FROM messages m WHERE m.conversation_id = c.id ORDER BY m.created_at DESC LIMIT 1) as last_message,
            (SELECT m.created_at FROM messages m WHERE m.conversation_id = c.id ORDER BY m.created_at DESC LIMIT 1) as last_message_time
        FROM conversations c
        JOIN users u ON c.user_id = u.id
        JOIN users a ON c.admin_id = a.id
        WHERE c.user_id = ? OR c.admin_id = ?
        ORDER BY c.updated_at DESC
    ");
    
    $stmt->bind_param('ii', $user_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $conversations = [];
    while ($row = $result->fetch_assoc()) {
        $conversations[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'conversations' => $conversations,
        'total' => count($conversations)
    ]);
    
    $stmt->close();
    
} catch (Exception $e) {
    error_log("get_conversations.php error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Database error', 'details' => $e->getMessage()]);
} finally {
    $conn->close();
} 