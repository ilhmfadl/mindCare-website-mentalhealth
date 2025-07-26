<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../config/db.php';

try {
    $conn = new mysqli($host, $user, $pass, $db);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // Check if it's a POST request
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Only POST method is allowed");
    }
    
    // Get POST data
    $question_id = isset($_POST['question_id']) ? (int)$_POST['question_id'] : 0;
    $user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
    $response_text = isset($_POST['response_text']) ? trim($_POST['response_text']) : '';
    $current_time = isset($_POST['current_time']) ? $_POST['current_time'] : date('Y-m-d H:i:s');
    
    // Validation
    if (!$question_id) {
        throw new Exception("Question ID is required");
    }
    
    if (!$user_id) {
        throw new Exception("User ID is required");
    }
    
    if (empty($response_text)) {
        throw new Exception("Response text is required");
    }
    
    if (strlen($response_text) > 1000) {
        throw new Exception("Response text is too long (max 1000 characters)");
    }
    
    // Check if question exists
    $checkQuery = "SELECT id FROM forum_questions WHERE id = ?";
    $checkStmt = $conn->prepare($checkQuery);
    if (!$checkStmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $checkStmt->bind_param('i', $question_id);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows === 0) {
        throw new Exception("Question not found");
    }
    
    $checkStmt->close();
    
    // Insert new response
    $query = "INSERT INTO forum_responses (question_id, user_id, response_text, created_at, updated_at) 
              VALUES (?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param('iisss', $question_id, $user_id, $response_text, $current_time, $current_time);
    
    if ($stmt->execute()) {
        $response_id = $conn->insert_id;
        
        // Get username for the response
        $usernameQuery = "SELECT COALESCE(u.username, 'Anonymous User') as username 
                         FROM users u 
                         WHERE u.id = ?";
        $usernameStmt = $conn->prepare($usernameQuery);
        $usernameStmt->bind_param('i', $user_id);
        $usernameStmt->execute();
        $usernameResult = $usernameStmt->get_result();
        $username = $usernameResult->fetch_assoc()['username'];
        $usernameStmt->close();
        
        $stmt->close();
        $conn->close();
        
        echo json_encode([
            'success' => true,
            'message' => 'Respon berhasil dibuat',
            'data' => [
                'id' => $response_id,
                'question_id' => $question_id,
                'user_id' => $user_id,
                'username' => $username,
                'response_text' => $response_text,
                'created_at' => $current_time,
                'updated_at' => $current_time
            ]
        ]);
    } else {
        throw new Exception("Failed to create response: " . $stmt->error);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?> 