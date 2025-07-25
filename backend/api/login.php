<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

require_once '../config/db.php';

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

if (!$username || !$password) {
    echo json_encode(['success' => false, 'message' => 'Username/email dan password wajib diisi!']);
    exit;
}

// Cari user berdasarkan username atau email
$stmt = $conn->prepare("SELECT id, username, email, password, role FROM users WHERE username=? OR email=?");
$stmt->bind_param("ss", $username, $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user && password_verify($password, $user['password'])) {
    unset($user['password']); // Jangan kirim hash password ke frontend
    echo json_encode(['success' => true, 'user' => $user]);
} else {
    echo json_encode(['success' => false, 'message' => 'Username/email atau password salah!']);
}
?>
