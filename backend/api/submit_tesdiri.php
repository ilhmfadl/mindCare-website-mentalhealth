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
if (!is_array($jawabanArr)) {
    echo json_encode(['success' => false, 'message' => 'Jawaban tidak valid!']);
    exit;
}

try {
    // Ambil semua pertanyaan dengan kategori
    $stmt = $conn->prepare("SELECT nomor, pertanyaan, kategori FROM pertanyaan_tesdiri ORDER BY nomor ASC");
    $stmt->execute();
    $result = $stmt->get_result();
    
    $questions = [];
    while ($row = $result->fetch_assoc()) {
        $questions[] = $row;
    }
    
    if (count($jawabanArr) !== count($questions)) {
        echo json_encode(['success' => false, 'message' => 'Jumlah jawaban tidak sesuai dengan jumlah pertanyaan!']);
        exit;
    }
    
    // Hitung berdasarkan kategori
    $kategoriCounts = [
        'neurosis' => 0,
        'psikotik' => 0,
        'ptsd' => 0
    ];
    
    foreach ($questions as $index => $question) {
        $jawaban = strtoupper(strval($jawabanArr[$index]));
        if ($jawaban === 'YA' || $jawaban === '1') {
            $kategoriCounts[$question['kategori']]++;
        }
    }
    
    // Logika hasil berdasarkan kategori
    $hasil = 'Normal';
    $severity = 'Normal';
    
    // Prioritas: PTSD > Psikotik > Neurosis
    if ($kategoriCounts['ptsd'] > 0) {
        $hasil = 'Gejala PTSD';
        if ($kategoriCounts['ptsd'] == 1) $severity = 'Ringan';
        elseif ($kategoriCounts['ptsd'] <= 3) $severity = 'Sedang';
        else $severity = 'Berat';
    } elseif ($kategoriCounts['psikotik'] > 0) {
        $hasil = 'Gejala Psikotik';
        if ($kategoriCounts['psikotik'] == 1) $severity = 'Ringan';
        elseif ($kategoriCounts['psikotik'] == 2) $severity = 'Sedang';
        else $severity = 'Berat';
    } elseif ($kategoriCounts['neurosis'] >= 5) {
        $hasil = 'Gejala Neurosis';
        if ($kategoriCounts['neurosis'] <= 7) $severity = 'Ringan';
        elseif ($kategoriCounts['neurosis'] <= 10) $severity = 'Sedang';
        else $severity = 'Berat';
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
        echo json_encode([
            'success' => true, 
            'hasil' => $hasil, 
            'severity' => $severity,
            'kategori_counts' => $kategoriCounts,
            'total_questions' => count($questions)
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal simpan hasil tes!']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($conn)) {
        $conn->close();
    }
}
?>
