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
    // Use the connection from db.php
    global $conn;
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    $conn->set_charset("utf8mb4");
    
    // Ambil semua jurnal
    $stmt = $conn->prepare("SELECT id, kategori, sumber, kutipan, created_at, updated_at FROM jurnal ORDER BY created_at DESC");
    $stmt->execute();
    $result = $stmt->get_result();
    
    $journals = [];
    while ($row = $result->fetch_assoc()) {
        $journals[] = [
            'id' => $row['id'],
            'kategori' => $row['kategori'],
            'sumber' => $row['sumber'],
            'kutipan' => $row['kutipan'],
            'created_at' => $row['created_at'],
            'updated_at' => $row['updated_at']
        ];
    }
    
    // Hitung jumlah per kategori
    $stmt = $conn->prepare("SELECT kategori, COUNT(*) as count FROM jurnal GROUP BY kategori");
    $stmt->execute();
    $result = $stmt->get_result();
    
    $categoryCounts = [];
    while ($row = $result->fetch_assoc()) {
        $categoryCounts[$row['kategori']] = $row['count'];
    }
    
    // Pastikan semua kategori ada (meskipun kosong)
    $allCategories = ['neurosis', 'psikotik', 'ptsd'];
    foreach ($allCategories as $category) {
        if (!isset($categoryCounts[$category])) {
            $categoryCounts[$category] = 0;
        }
    }
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'data' => [
            'journals' => $journals,
            'category_counts' => $categoryCounts
        ]
    ]);
    
} catch (Exception $e) {
    error_log("Error in get_journals.php: " . $e->getMessage());
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