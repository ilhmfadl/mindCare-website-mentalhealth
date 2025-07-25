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
    $response_id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
    $response_text = isset($_POST['response_text']) ? trim($_POST['response_text']) : '';
    $current_time = isset($_POST['current_time']) ? $_POST['current_time'] : date('Y-m-d H:i:s');
    
    // Validation
    if (!$response_id) {
        throw new Exception("Response ID is required");
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
    
    // Check if response exists and belongs to user
    $checkQuery = "SELECT id FROM forum_responses WHERE id = ? AND user_id = ?";
    $checkStmt = $conn->prepare($checkQuery);
    if (!$checkStmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $checkStmt->bind_param('ii', $response_id, $user_id);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows === 0) {
        throw new Exception("Respon tidak ditemukan atau bukan milik user");
    }
    
    $checkStmt->close();
    
    // Update response
    $query = "UPDATE forum_responses SET response_text = ?, updated_at = ? WHERE id = ? AND user_id = ?";
    
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param('ssii', $response_text, $current_time, $response_id, $user_id);
    
    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        
        echo json_encode([
            'success' => true,
            'message' => 'Respon berhasil diupdate',
            'data' => [
                'id' => $response_id,
                'user_id' => $user_id,
                'response_text' => $response_text,
                'updated_at' => $current_time
            ]
        ]);
    } else {
        throw new Exception("Failed to update response: " . $stmt->error);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?> 