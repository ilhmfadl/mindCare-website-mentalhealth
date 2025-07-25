<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config/db.php';

// Debug: log data POST
file_put_contents('debug_register.txt', json_encode($_POST));

$username = $_POST['username'] ?? '';
$email    = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$fullName = $_POST['fullName'] ?? '';

if (!$username || !$email || !$password) {
    echo json_encode(['success' => false, 'message' => 'Semua field wajib diisi!']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Format email tidak valid!']);
    exit;
}

if (strlen($password) < 6) {
    echo json_encode(['success' => false, 'message' => 'Password minimal 6 karakter!']);
    exit;
}

// Cek apakah username/email sudah terdaftar
$stmt = $conn->prepare("SELECT id FROM users WHERE username=? OR email=?");
$stmt->bind_param("ss", $username, $email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Username atau email sudah terdaftar!']);
    exit;
}

// Hash password
$hashed = password_hash($password, PASSWORD_DEFAULT);

// Insert user baru
$stmt = $conn->prepare("INSERT INTO users (username, email, password, fullName) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $username, $email, $hashed, $fullName);
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Registrasi berhasil!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Registrasi gagal! Error: ' . $stmt->error]);
}
?>
