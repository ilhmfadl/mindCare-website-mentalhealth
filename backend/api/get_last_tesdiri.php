<?php
require_once __DIR__ . '/../config/db.php';
header('Content-Type: application/json');

// Ambil user_id dari POST
$user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'user_id wajib diisi']);
    exit;
}

$sql = "SELECT id, user_id, tanggal, jawaban, hasil, severity FROM hasil_tesdiri WHERE user_id = ? ORDER BY tanggal DESC, id DESC LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    // If severity is not stored in database, calculate it
    if (!isset($row['severity']) || $row['severity'] === null) {
        $jawabanArr = json_decode($row['jawaban'], true);
        $severity = 'Normal';
        
        if (is_array($jawabanArr) && count($jawabanArr) >= 29) {
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

            if ($ya_25_29 > 0) {
                if ($ya_25_29 == 1) $severity = 'Ringan';
                elseif ($ya_25_29 <= 3) $severity = 'Sedang';
                else $severity = 'Berat';
            } elseif ($ya_22_24 > 0) {
                if ($ya_22_24 == 1) $severity = 'Ringan';
                elseif ($ya_22_24 == 2) $severity = 'Sedang';
                else $severity = 'Berat';
            } elseif ($ya_1_21 >= 5) {
                if ($ya_1_21 <= 7) $severity = 'Ringan';
                elseif ($ya_1_21 <= 10) $severity = 'Sedang';
                else $severity = 'Berat';
            }
        }
        
        $row['severity'] = $severity;
    }
    
    echo json_encode(['success' => true, 'data' => $row]);
} else {
    echo json_encode(['success' => false, 'message' => 'Belum ada hasil tes untuk user ini']);
}
$stmt->close();
$conn->close(); 