<?php
require_once '../config/db.php';
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Get parameters from POST data
    $conversation_id = isset($_POST['conversation_id']) ? intval($_POST['conversation_id']) : null;
    $admin_id = isset($_POST['admin_id']) ? intval($_POST['admin_id']) : null;
    
    if (!$conversation_id || !$admin_id) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required parameters']);
        exit;
    }
    
    // Debug: Log parameters
    error_log("mark_messages_read.php: Processing conversation_id = " . $conversation_id . ", admin_id = " . $admin_id);
    
    // Verify conversation exists and belongs to this admin
    $verify_sql = "SELECT id FROM conversations WHERE id = ? AND admin_id = ? AND status != 'deleted'";
    $verify_stmt = $conn->prepare($verify_sql);
    $verify_stmt->bind_param('ii', $conversation_id, $admin_id);
    $verify_stmt->execute();
    $verify_result = $verify_stmt->get_result();
    
    if ($verify_result->num_rows === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'Conversation not found or access denied']);
        exit;
    }
    
    // Mark all unread messages from user as read
    $update_sql = "
        UPDATE messages 
        SET is_read = 1 
        WHERE conversation_id = ? 
        AND sender_role = 'user' 
        AND is_read = 0
    ";
    
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param('i', $conversation_id);
    $update_stmt->execute();
    
    $affected_rows = $update_stmt->affected_rows;
    
    // Debug: Log result
    error_log("mark_messages_read.php: Marked " . $affected_rows . " messages as read");
    
    $response = [
        'success' => true,
        'message' => 'Messages marked as read successfully',
        'affected_rows' => $affected_rows
    ];
    
    echo json_encode($response);
    
} catch (Exception $e) {
    error_log("mark_messages_read.php error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Database error', 'details' => $e->getMessage()]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
} 