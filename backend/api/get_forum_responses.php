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
    
    // Get question_id from query parameter
    $question_id = isset($_GET['question_id']) ? (int)$_GET['question_id'] : 0;
    
    if (!$question_id) {
        throw new Exception("Question ID is required");
    }
    
    // Get user_id from query parameter (optional)
    $current_user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;
    
    // Query to get responses with username, vote, and user vote status
    $query = "SELECT fr.id, fr.question_id, fr.user_id, fr.response_text, 
                     fr.created_at, fr.updated_at, fr.vote,
                     COALESCE(u.username, 'Anonymous User') as username,
                     CASE 
                         WHEN u.photo IS NOT NULL AND u.photo != '' THEN 
                             CONCAT('https://mindcareindependent.com/', u.photo)
                         ELSE 'https://randomuser.me/api/portraits/lego/1.jpg'
                     END as avatar,
                     CASE WHEN frv.vote_type IS NOT NULL THEN frv.vote_type ELSE NULL END as user_vote_type
              FROM forum_responses fr
              LEFT JOIN users u ON fr.user_id = u.id
              LEFT JOIN forum_response_votes frv ON fr.id = frv.response_id AND frv.user_id = ?
              WHERE fr.question_id = ?
              ORDER BY fr.vote DESC, fr.created_at ASC";
    
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param('ii', $current_user_id, $question_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $responses = [];
    while ($row = $result->fetch_assoc()) {
        $responses[] = [
            'id' => $row['id'],
            'question_id' => $row['question_id'],
            'user_id' => $row['user_id'],
            'username' => $row['username'],
            'avatar' => $row['avatar'],
            'response_text' => $row['response_text'],
            'created_at' => $row['created_at'],
            'updated_at' => $row['updated_at'],
            'vote' => (int)$row['vote'] ?? 0,
            'user_vote_type' => $row['user_vote_type'] ?? null
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
            'responses_count' => count($responses)
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