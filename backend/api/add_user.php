<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method tidak diizinkan']);
    exit;
}

try {
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input || !isset($input['username']) || !isset($input['email']) || !isset($input['password'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Username, email, dan password wajib diisi']);
        exit;
    }

    $username = trim($input['username']);
    $email = trim($input['email']);
    $password = $input['password'];
    $fullName = isset($input['fullName']) ? trim($input['fullName']) : '';
    $address = isset($input['address']) ? trim($input['address']) : '';
    $role = isset($input['role']) ? trim($input['role']) : 'User';

    // Validasi email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Format email tidak valid']);
        exit;
    }

    // Cek apakah username sudah ada
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Username sudah digunakan']);
        exit;
    }

    // Cek apakah email sudah ada
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Email sudah digunakan']);
        exit;
    }

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert user baru
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, fullName, address, role) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $username, $email, $hashedPassword, $fullName, $address, $role);
    
    if ($stmt->execute()) {
        $newUserId = $conn->insert_id;
        
        // Ambil data user yang baru dibuat
        $stmt = $conn->prepare("SELECT id, username, email, role, created_at, photo, fullName, address FROM users WHERE id = ?");
        $stmt->bind_param("i", $newUserId);
        $stmt->execute();
        $result = $stmt->get_result();
        $newUser = $result->fetch_assoc();
        
        // Format tanggal
        $created_at = new DateTime($newUser['created_at']);
        $newUser['date'] = $created_at->format('d/m/Y');
        $newUser['time'] = $created_at->format('h:i A');
        
        // Biarkan photo kosong jika tidak ada di database
        if (!$newUser['photo']) {
            $newUser['photo'] = null;
        }
        
        http_response_code(201);
        echo json_encode([
            'success' => true, 
            'message' => 'User berhasil ditambahkan',
            'data' => $newUser
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Gagal menambahkan user ke database']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error server: ' . $e->getMessage()]);
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($conn)) {
        $conn->close();
    }
}
?> 