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
    // Get user_id from POST data
    $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : null;
    
    if (!$user_id) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing user_id parameter']);
        exit;
    }
    
    // Get user role from database
    $user_sql = "SELECT role, username, fullName FROM users WHERE id = ?";
    $user_stmt = $conn->prepare($user_sql);
    $user_stmt->bind_param('i', $user_id);
    $user_stmt->execute();
    $user_result = $user_stmt->get_result();
    
    if ($user_result->num_rows === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'User not found']);
        exit;
    }
    
    $user_data = $user_result->fetch_assoc();
    $user_role = $user_data['role'];
    $username = $user_data['username'];
    $fullName = $user_data['fullName'];
    
    // Debug: Log logout attempt with role info
    error_log("logout.php: User ID " . $user_id . " (" . $username . ") with role '" . $user_role . "' attempting to logout");
    
    // Update last logout time in database
    $update_sql = "UPDATE users SET last_logout = NOW() WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param('i', $user_id);
    $update_stmt->execute();
    
    // Log successful logout with role
    error_log("logout.php: " . ucfirst($user_role) . " logout successful - User ID: " . $user_id . ", Username: " . $username . ", Name: " . $fullName);
    
    $response = [
        'success' => true,
        'message' => ucfirst($user_role) . ' logout successful',
        'user_role' => $user_role,
        'username' => $username
    ];
    
    echo json_encode($response);
    
} catch (Exception $e) {
    error_log("logout.php error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Database error', 'details' => $e->getMessage()]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>
