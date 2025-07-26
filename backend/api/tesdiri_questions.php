<?php
require_once '../config/db.php';
header('Content-Type: application/json');

$result = $conn->query("SELECT nomor, pertanyaan FROM pertanyaan_tesdiri ORDER BY nomor ASC");
$questions = [];
while ($row = $result->fetch_assoc()) {
    $questions[] = $row;
}
echo json_encode(['success' => true, 'questions' => $questions]);
?>