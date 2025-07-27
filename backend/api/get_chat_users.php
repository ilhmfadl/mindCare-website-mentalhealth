<?php
require_once '../config/db.php';
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Get admin_id from query parameter
    $admin_id = isset($_GET['admin_id']) ? intval($_GET['admin_id']) : null;
    
    if (!$admin_id) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing admin_id parameter']);
        exit;
    }
    
    // Debug: Log admin_id
    error_log("get_chat_users.php: Processing admin_id = " . $admin_id);
    
    // Get users who have conversations with this admin
    $sql = "
        SELECT DISTINCT 
            u.id,
            u.username,
            u.fullName,
            u.photo,
            c.id as conversation_id,
            c.status as conversation_status,
            c.updated_at as last_activity
        FROM conversations c
        JOIN users u ON c.user_id = u.id
        WHERE c.admin_id = ? AND c.status != 'deleted'
        ORDER BY c.updated_at DESC
    ";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $admin_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $users = [];
    while ($row = $result->fetch_assoc()) {
        // Debug: Log conversation data
        error_log("Processing conversation_id: " . $row['conversation_id'] . " for user: " . $row['username']);
        
        // Get last message for this conversation
        $last_message_sql = "
            SELECT message, created_at, sender_role, is_read
            FROM messages 
            WHERE conversation_id = ? 
            ORDER BY created_at DESC 
            LIMIT 1
        ";
        
        $last_stmt = $conn->prepare($last_message_sql);
        $last_stmt->bind_param('i', $row['conversation_id']);
        $last_stmt->execute();
        $last_result = $last_stmt->get_result();
        $last_message = $last_result->fetch_assoc();
        
        // Debug: Log last message data
        if ($last_message) {
            error_log("Last message for conversation " . $row['conversation_id'] . ": " . $last_message['message']);
        } else {
            error_log("No last message found for conversation " . $row['conversation_id']);
        }
        
        // Count unread messages from user
        $unread_sql = "
            SELECT COUNT(*) as unread_count
            FROM messages 
            WHERE conversation_id = ? 
            AND sender_role = 'user' 
            AND is_read = 0
        ";
        
        $unread_stmt = $conn->prepare($unread_sql);
        $unread_stmt->bind_param('i', $row['conversation_id']);
        $unread_stmt->execute();
        $unread_result = $unread_stmt->get_result();
        $unread_count = $unread_result->fetch_assoc();
        
        // Handle photo URL
        $photoUrl = null;
        if ($row['photo']) {
            // Jika photo adalah URL lengkap, gunakan langsung
            if (filter_var($row['photo'], FILTER_VALIDATE_URL)) {
                $photoUrl = $row['photo'];
            } else {
                // Jika photo adalah path relatif, tambahkan base URL
                $photoPath = $row['photo'];
                if (strpos($photoPath, 'uploads/') === 0) {
                    $photoPath = substr($photoPath, 8); // Hapus 'uploads/' dari awal
                }
                $photoUrl = 'https://mindcareindependent.com/uploads/' . $photoPath;
            }
        }

        $user_data = [
            'id' => $row['id'],
            'username' => $row['username'],
            'fullName' => $row['fullName'],
            'profile_photo' => $photoUrl, // URL lengkap untuk foto profil
            'conversation_id' => $row['conversation_id'],
            'conversation_status' => $row['conversation_status'],
            'last_activity' => $row['last_activity'],
            'last_message' => $last_message ? $last_message['message'] : '',
            'last_message_time' => $last_message ? $last_message['created_at'] : '',
            'last_message_sender' => $last_message ? $last_message['sender_role'] : '',
            'unread_count' => $unread_count['unread_count']
        ];
        
        // Debug: Log user data
        error_log("User data: " . json_encode($user_data));
        error_log("Photo URL for user " . $row['username'] . ": " . $photoUrl);
        
        $users[] = $user_data;
        
        $last_stmt->close();
        $unread_stmt->close();
    }
    
    $stmt->close();
    
    $response = [
        'success' => true,
        'users' => $users,
        'total_users' => count($users)
    ];
    
    // Debug: Log final response
    error_log("Final response: " . json_encode($response));
    
    echo json_encode($response);
    
} catch (Exception $e) {
    error_log("get_chat_users.php error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Database error', 'details' => $e->getMessage()]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
} 