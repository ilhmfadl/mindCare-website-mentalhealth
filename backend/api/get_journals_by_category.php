<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

require_once '../config/db.php';

try {
    // Get category from query parameter
    $category = $_GET['category'] ?? '';
    
    if (empty($category)) {
        throw new Exception('Category parameter is required');
    }
    
    // Map category names to database values
    $categoryMap = [
        'neurosis' => 'neurosis',
        'Neurosis' => 'neurosis',
        'psikotik' => 'psikotik', 
        'Psikotik' => 'psikotik',
        'psychotic' => 'psikotik',
        'Psychotic' => 'psikotik',
        'ptsd' => 'ptsd',
        'PTSD' => 'ptsd'
    ];
    
    $dbCategory = $categoryMap[$category] ?? $category;
    
    // Use the connection from db.php
    global $conn;
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    $conn->set_charset("utf8mb4");
    
    // Get 3 most recent articles for the category (highest IDs)
    $stmt = $conn->prepare("SELECT id, kategori, judul, sumber, kutipan, created_at, updated_at FROM jurnal WHERE kategori = ? ORDER BY id DESC LIMIT 3");
    $stmt->bind_param("s", $dbCategory);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if (!$result) {
        throw new Exception("Query failed: " . $conn->error);
    }
    
    $journals = [];
    while ($row = $result->fetch_assoc()) {
        $journals[] = [
            'id' => $row['id'],
            'kategori' => $row['kategori'],
            'judul' => $row['judul'],
            'sumber' => $row['sumber'],
            'kutipan' => $row['kutipan'],
            'link' => $row['sumber'], // Use sumber as link
            'created_at' => $row['created_at'],
            'updated_at' => $row['updated_at']
        ];
    }
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'data' => [
            'journals' => $journals,
            'category' => $dbCategory,
            'count' => count($journals)
        ]
    ]);
    
} catch (Exception $e) {
    error_log("Error in get_journals_by_category.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Internal server error: ' . $e->getMessage()]);
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($conn)) {
        $conn->close();
    }
}
?> 