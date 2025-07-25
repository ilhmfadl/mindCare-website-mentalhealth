<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');
require_once '../config/db.php';

$user_id = isset($_POST['user_id']) && $_POST['user_id'] !== '' ? intval($_POST['user_id']) : null;
$jawaban = $_POST['jawaban'] ?? null; // array ["YA","TIDAK",...]
if (!$jawaban) {
    echo json_encode(['success' => false, 'message' => 'Data tidak lengkap!']);
    exit;
}
$jawabanArr = json_decode($jawaban, true);
if (!is_array($jawabanArr) || count($jawabanArr) < 29) {
    echo json_encode(['success' => false, 'message' => 'Jawaban tidak valid!']);
    exit;
}

// Interpretasi hasil dan severity
$ya_1_21 = 0;
foreach (range(0, 20) as $i) {
    $ans = strtoupper(strval($jawabanArr[$i]));
    if ($ans === 'YA' || $ans === '1') $ya_1_21++;
}
$ya_22_24 = 0;
foreach (range(21, 23) as $i) {
    $ans = strtoupper(strval($jawabanArr[$i]));
    if ($ans === 'YA' || $ans === '1') $ya_22_24++;
}
$ya_25_29 = 0;
foreach (range(24, 28) as $i) {
    $ans = strtoupper(strval($jawabanArr[$i]));
    if ($ans === 'YA' || $ans === '1') $ya_25_29++;
}

$hasil = 'Normal';
$severity = 'Normal';
if ($ya_25_29 > 0) {
    $hasil = 'Gejala PTSD';
    if ($ya_25_29 == 1) $severity = 'Ringan';
    elseif ($ya_25_29 <= 3) $severity = 'Sedang';
    else $severity = 'Berat';
} elseif ($ya_22_24 > 0) {
    $hasil = 'Gejala Psikotik';
    if ($ya_22_24 == 1) $severity = 'Ringan';
    elseif ($ya_22_24 == 2) $severity = 'Sedang';
    else $severity = 'Berat';
} elseif ($ya_1_21 >= 5) {
    $hasil = 'Gejala Neurosis';
    if ($ya_1_21 <= 7) $severity = 'Ringan';
    elseif ($ya_1_21 <= 10) $severity = 'Sedang';
    else $severity = 'Berat';
} else {
    $hasil = 'Normal';
    $severity = 'Normal';
}

// Simpan ke database
if ($user_id !== null) {
    $stmt = $conn->prepare("INSERT INTO hasil_tesdiri (user_id, jawaban, hasil, severity) VALUES (?, ?, ?, ?)");
    $jawabanJson = json_encode($jawabanArr);
    $stmt->bind_param("isss", $user_id, $jawabanJson, $hasil, $severity);
} else {
    $stmt = $conn->prepare("INSERT INTO hasil_tesdiri (user_id, jawaban, hasil, severity) VALUES (NULL, ?, ?, ?)");
    $jawabanJson = json_encode($jawabanArr);
    $stmt->bind_param("sss", $jawabanJson, $hasil, $severity);
}
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'hasil' => $hasil, 'severity' => $severity]);
} else {
    echo json_encode(['success' => false, 'message' => 'Gagal simpan hasil tes!']);
}
?>
