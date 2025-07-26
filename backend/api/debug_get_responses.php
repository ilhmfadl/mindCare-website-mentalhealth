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
    
    // Test query untuk mengecek apakah ada data di tabel
    $testQuery = "SELECT COUNT(*) as total FROM forum_responses";
    $testResult = $conn->query($testQuery);
    $totalResponses = $testResult->fetch_assoc()['total'];
    
    // Query untuk mendapatkan responses
    $query = "SELECT fr.id, fr.question_id, fr.user_id, fr.response_text, 
                     fr.created_at, fr.updated_at,
                     COALESCE(u.username, 'Anonymous User') as username
              FROM forum_responses fr
              LEFT JOIN users u ON fr.user_id = u.id
              WHERE fr.question_id = ?
              ORDER BY fr.created_at ASC";
    
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param('i', $question_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $responses = [];
    while ($row = $result->fetch_assoc()) {
        $responses[] = [
            'id' => $row['id'],
            'question_id' => $row['question_id'],
            'user_id' => $row['user_id'],
            'username' => $row['username'],
            'response_text' => $row['response_text'],
            'created_at' => $row['created_at'],
            'updated_at' => $row['updated_at']
        ];
    }
    
    $stmt->close();
    $conn->close();
    
    echo json_encode([
        'success' => true,
        'data' => $responses,
        'message' => 'Respon berhasil diambil',
        'debug' => [
            'question_id' => $question_id,
            'responses_count' => count($responses),
            'total_responses_in_table' => $totalResponses,
            'database_connected' => true
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage(),
        'debug' => [
            'error_type' => get_class($e),
            'error_line' => $e->getLine()
        ]
    ]);
}
?> 