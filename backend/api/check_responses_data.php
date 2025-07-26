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
    
    // Get all responses
    $query = "SELECT fr.id, fr.question_id, fr.user_id, fr.response_text, 
                     fr.created_at, fr.updated_at,
                     COALESCE(u.username, 'Anonymous User') as username
              FROM forum_responses fr
              LEFT JOIN users u ON fr.user_id = u.id
              ORDER BY fr.created_at DESC
              LIMIT 10";
    
    $result = $conn->query($query);
    
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
    
    // Get total count
    $countQuery = "SELECT COUNT(*) as total FROM forum_responses";
    $countResult = $conn->query($countQuery);
    $totalCount = $countResult->fetch_assoc()['total'];
    
    $conn->close();
    
    echo json_encode([
        'success' => true,
        'total_responses' => $totalCount,
        'recent_responses' => $responses,
        'message' => 'Data responses berhasil diambil'
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?> 