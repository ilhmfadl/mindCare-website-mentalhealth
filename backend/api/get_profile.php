<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

require_once '../config/db.php';

// Ambil user id dari request (POST atau GET)
$user_id = $_POST['id'] ?? $_GET['id'] ?? null;

if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'ID user wajib dikirim!']);
    exit;
}

$stmt = $conn->prepare("SELECT id, username, email, role, created_at, photo, fullName, address FROM users WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user) {
    echo json_encode(['success' => true, 'user' => $user]);
} else {
    echo json_encode(['success' => false, 'message' => 'User tidak ditemukan!']);
}
?>
