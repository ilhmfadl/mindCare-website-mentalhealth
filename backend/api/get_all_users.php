<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once '../config/db.php';

try {
    $result = $conn->query("SELECT id, username, email, role, created_at, photo, fullName, address FROM users ORDER BY created_at DESC");
    
    if (!$result) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Gagal mengambil data dari database']);
        exit;
    }
    
    $users = [];
    while ($row = $result->fetch_assoc()) {
        // Format tanggal
        $created_at = new DateTime($row['created_at']);
        $row['date'] = $created_at->format('d/m/Y');
        $row['time'] = $created_at->format('h:i A');
        
        // Handle photo URL
        if ($row['photo']) {
            // Jika photo adalah URL lengkap, gunakan langsung
            if (filter_var($row['photo'], FILTER_VALIDATE_URL)) {
                // URL sudah lengkap, tidak perlu diubah
            } else {
                // Jika photo adalah path relatif, tambahkan base URL
                // Hapus 'uploads/' jika sudah ada di path
                $photoPath = $row['photo'];
                if (strpos($photoPath, 'uploads/') === 0) {
                    $photoPath = substr($photoPath, 8); // Hapus 'uploads/' dari awal
                }
                $row['photo'] = 'https://mindcareindependent.com/uploads/' . $photoPath;
            }
        } else {
            $row['photo'] = null;
        }
        
        // Default role jika tidak ada
        if (!$row['role']) {
            $row['role'] = 'User';
        }
        
        $users[] = $row;
    }
    
    http_response_code(200);
    echo json_encode(['success' => true, 'users' => $users]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error server: ' . $e->getMessage()]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?> 