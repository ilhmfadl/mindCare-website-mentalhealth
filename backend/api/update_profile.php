<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');
require_once '../config/db.php';

$user_id  = $_POST['id'] ?? null;
$fullName = $_POST['fullName'] ?? '';
$email    = $_POST['email'] ?? '';
$address  = $_POST['address'] ?? '';
$photo    = null;

if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'ID user wajib dikirim!']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Format email tidak valid!']);
    exit;
}

// Proses upload foto jika ada
if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/profile/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
    $filename = 'user_' . $user_id . '_' . time() . '.' . $ext;
    $targetFile = $uploadDir . $filename;
    if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetFile)) {
        $photo = 'uploads/profile/' . $filename;
    }
}

if ($photo) {
    $stmt = $conn->prepare("UPDATE users SET fullName=?, email=?, address=?, photo=? WHERE id=?");
    $stmt->bind_param("ssssi", $fullName, $email, $address, $photo, $user_id);
} else {
    $stmt = $conn->prepare("UPDATE users SET fullName=?, email=?, address=? WHERE id=?");
    $stmt->bind_param("sssi", $fullName, $email, $address, $user_id);
}

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Profile berhasil diupdate!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Gagal update profile! Error: ' . $stmt->error]);
}
?>