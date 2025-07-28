<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Try multiple paths for db.php
$possible_paths = [
    '../config/db.php',
    __DIR__ . '/../config/db.php',
    dirname(__FILE__) . '/../config/db.php'
];

$db_loaded = false;
foreach ($possible_paths as $db_path) {
    if (file_exists($db_path)) {
        require_once $db_path;
        $db_loaded = true;
        break;
    }
}

if (!$db_loaded) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database config file not found']);
    exit;
}

// Log if function doesn't exist (for debugging)
if (!function_exists('formatUserFriendlyTime')) {
    error_log('formatUserFriendlyTime function not found after including db.php');
}

try {
    $result = $conn->query("SELECT id, username, email, role, created_at, photo, fullName, address FROM users ORDER BY created_at DESC");
    
    if (!$result) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Gagal mengambil data dari database']);
        exit;
    }
    
    $users = [];
    while ($row = $result->fetch_assoc()) {
        // Format tanggal dengan zona waktu yang benar
        $client_timezone = isset($_GET['timezone']) ? $_GET['timezone'] : 'Asia/Jakarta';
        
        // Check if function exists before calling it
        if (function_exists('formatUserFriendlyTime')) {
            $row['date'] = formatUserFriendlyTime($row['created_at'], $client_timezone);
        } else {
            // Fallback if function doesn't exist
            try {
                $utc_datetime = new DateTime($row['created_at'], new DateTimeZone('UTC'));
                $utc_datetime->setTimezone(new DateTimeZone($client_timezone));
                $row['date'] = $utc_datetime->format('d/m/Y H:i');
            } catch (Exception $e) {
                $row['date'] = $row['created_at'];
            }
        }
        
        // Format waktu hanya jam dan menit dengan AM/PM
        try {
            $utc_datetime = new DateTime($row['created_at'], new DateTimeZone('UTC'));
            $utc_datetime->setTimezone(new DateTimeZone($client_timezone));
            $row['time'] = $utc_datetime->format('h:i A');
        } catch (Exception $e) {
            // Check if function exists before calling it
            if (function_exists('convertToClientTimezone')) {
                $row['time'] = convertToClientTimezone($row['created_at'], $client_timezone);
            } else {
                // Fallback if function doesn't exist
                try {
                    $utc_datetime = new DateTime($row['created_at'], new DateTimeZone('UTC'));
                    $utc_datetime->setTimezone(new DateTimeZone($client_timezone));
                    $row['time'] = $utc_datetime->format('h:i A');
                } catch (Exception $e2) {
                    $row['time'] = $row['created_at'];
                }
            }
        }
        
        // Handle photo URL
        if ($row['photo']) {
            // Jika photo adalah URL lengkap, gunakan langsung
            if (filter_var($row['photo'], FILTER_VALIDATE_URL)) {
                // URL sudah lengkap, tidak perlu diubah
            } else {
                // Jika photo adalah path relatif, tambahkan base URL
                // Hapus 'uploads/' jika sudah ada di path
                $photoPath = $row['photo'];
                if (strpos($photoPath, 'uploads/') === 0) {
                    $photoPath = substr($photoPath, 8); // Hapus 'uploads/' dari awal
                }
                $row['photo'] = 'https://mindcareindependent.com/uploads/' . $photoPath;
            }
        } else {
            $row['photo'] = null;
        }
        
        // Default role jika tidak ada
        if (!$row['role']) {
            $row['role'] = 'User';
        }
        
        $users[] = $row;
    }
    
    http_response_code(200);
    echo json_encode(['success' => true, 'users' => $users]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error server: ' . $e->getMessage()]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?> 