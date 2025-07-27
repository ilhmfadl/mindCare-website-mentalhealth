<?php
require_once '../config/db.php';
header('Content-Type: application/json');

$sql = "SELECT id FROM users WHERE role = 'admin' ORDER BY id ASC LIMIT 1";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$conn->close();
echo json_encode(['admin_id' => $row ? $row['id'] : null]); 