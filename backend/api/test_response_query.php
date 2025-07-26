<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

require_once '../config/db.php';

try {
    $conn = new mysqli($host, $user, $pass, $db);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // Get question_id from query parameter
    $question_id = isset($_GET['question_id']) ? (int)$_GET['question_id'] : 0;
    
    if (!$question_id) {
        throw new Exception("Question ID is required");
    }
    
    // Test 1: Get all responses
    $allQuery = "SELECT * FROM forum_responses ORDER BY created_at DESC LIMIT 10";
    $allResult = $conn->query($allQuery);
    $allResponses = [];
    while ($row = $allResult->fetch_assoc()) {
        $allResponses[] = $row;
    }
    
    // Test 2: Get responses for specific question_id
    $specificQuery = "SELECT * FROM forum_responses WHERE question_id = $question_id ORDER BY created_at ASC";
    $specificResult = $conn->query($specificQuery);
    $specificResponses = [];
    while ($row = $specificResult->fetch_assoc()) {
        $specificResponses[] = $row;
    }
    
    // Test 3: Get responses for specific question_id with prepared statement
    $preparedQuery = "SELECT * FROM forum_responses WHERE question_id = ? ORDER BY created_at ASC";
    $stmt = $conn->prepare($preparedQuery);
    $stmt->bind_param('i', $question_id);
    $stmt->execute();
    $preparedResult = $stmt->get_result();
    $preparedResponses = [];
    while ($row = $preparedResult->fetch_assoc()) {
        $preparedResponses[] = $row;
    }
    $stmt->close();
    
    // Test 4: Check table structure
    $structureQuery = "DESCRIBE forum_responses";
    $structureResult = $conn->query($structureQuery);
    $structure = [];
    while ($row = $structureResult->fetch_assoc()) {
        $structure[] = $row;
    }
    
    $conn->close();
    
    echo json_encode([
        'success' => true,
        'message' => 'Test query results',
        'debug' => [
            'question_id_requested' => $question_id,
            'question_id_type' => gettype($question_id),
            'all_responses_count' => count($allResponses),
            'specific_responses_count' => count($specificResponses),
            'prepared_responses_count' => count($preparedResponses),
            'all_responses' => $allResponses,
            'specific_responses' => $specificResponses,
            'prepared_responses' => $preparedResponses,
            'table_structure' => $structure
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?> 