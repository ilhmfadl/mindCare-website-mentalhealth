<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/db.php';

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// GET: Read questions
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        // Query untuk mengambil pertanyaan dengan username dan count responses
        $query = "SELECT fq.id, fq.user_id, fq.title, fq.desc, fq.created_at, fq.updated_at, 
                         COALESCE(u.username, 'Anonymous User') as username,
                         CASE 
                             WHEN u.photo IS NOT NULL AND u.photo != '' THEN 
                                 CONCAT('https://mindcareindependent.com/', u.photo)
                             ELSE 'https://randomuser.me/api/portraits/lego/1.jpg'
                         END as avatar,
                         COALESCE(resp.response_count, 0) as response_count
                  FROM forum_questions fq
                  LEFT JOIN users u ON fq.user_id = u.id
                  LEFT JOIN (
                      SELECT question_id, COUNT(*) as response_count 
                      FROM forum_responses 
                      GROUP BY question_id
                  ) resp ON fq.id = resp.question_id
                  ORDER BY fq.created_at DESC";
        $result = $conn->query($query);
        
        if ($result) {
            $questions = [];
            while ($row = $result->fetch_assoc()) {
                $questions[] = [
                    'id' => $row['id'],
                    'user_id' => $row['user_id'],
                    'title' => $row['title'],
                    'desc' => $row['desc'],
                    'created_at' => $row['created_at'],
                    'updated_at' => $row['updated_at'],
                    'username' => $row['username'],
                    'avatar' => $row['avatar'],
                    'response_count' => $row['response_count']
                ];
            }
            
            echo json_encode([
                'success' => true,
                'data' => $questions,
                'pagination' => [
                    'total' => count($questions),
                    'limit' => count($questions),
                    'offset' => 0,
                    'has_more' => false
                ],
                'message' => 'Pertanyaan berhasil diambil'
            ]);
            
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Query gagal: ' . $conn->error
            ]);
        }
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
}

// POST: Create new question
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
        $title = isset($_POST['title']) ? trim($_POST['title']) : '';
        $desc = isset($_POST['desc']) ? trim($_POST['desc']) : '';
        
        if (empty($title) || empty($desc) || $user_id <= 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Data tidak lengkap'
            ]);
            exit();
        }
        
        // Gunakan waktu lokal pengguna jika dikirim, atau gunakan waktu server
        $current_time = isset($_POST['current_time']) ? $_POST['current_time'] : date('Y-m-d H:i:s');
        
        $query = "INSERT INTO forum_questions (user_id, title, `desc`, created_at, updated_at) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        
        if ($stmt) {
            $stmt->bind_param('issss', $user_id, $title, $desc, $current_time, $current_time);
            
            if ($stmt->execute()) {
                $new_id = $conn->insert_id;
                echo json_encode([
                    'success' => true,
                    'message' => 'Pertanyaan berhasil ditambahkan',
                    'id' => $new_id
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Gagal menambahkan pertanyaan: ' . $stmt->error
                ]);
            }
            
            $stmt->close();
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Prepare statement gagal: ' . $conn->error
            ]);
        }
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
}

$conn->close();
?> 