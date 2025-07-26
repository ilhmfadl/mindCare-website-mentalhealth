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
    
    // Test query untuk mengecek tabel forum_responses
    $query = "SELECT COUNT(*) as total FROM forum_responses";
    $result = $conn->query($query);
    
    if ($result) {
        $row = $result->fetch_assoc();
        echo json_encode([
            'success' => true,
            'message' => 'Database connection successful',
            'total_responses' => $row['total'],
            'database_info' => [
                'host' => $host,
                'database' => $db,
                'user' => $user
            ]
        ]);
    } else {
        throw new Exception("Query failed: " . $conn->error);
    }
    
    $conn->close();
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?> 