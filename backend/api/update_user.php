<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: PUT, OPTIONS');
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

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method tidak diizinkan']);
    exit;
}

try {
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input || !isset($input['id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'ID user wajib diisi']);
        exit;
    }

    $id = intval($input['id']);
    $username = isset($input['username']) ? trim($input['username']) : null;
    $email = isset($input['email']) ? trim($input['email']) : null;
    $fullName = isset($input['fullName']) ? trim($input['fullName']) : null;
    $address = isset($input['address']) ? trim($input['address']) : null;
    $role = isset($input['role']) ? trim($input['role']) : null;
    $password = isset($input['password']) ? $input['password'] : null;

    // Cek apakah user ada
    $stmt = $conn->prepare("SELECT id FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows === 0) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'User tidak ditemukan']);
        exit;
    }

    // Validasi email jika diupdate
    if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Format email tidak valid']);
        exit;
    }

    // Cek apakah username sudah ada (kecuali user yang sedang diupdate)
    if ($username) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
        $stmt->bind_param("si", $username, $id);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Username sudah digunakan']);
            exit;
        }
    }

    // Cek apakah email sudah ada (kecuali user yang sedang diupdate)
    if ($email) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->bind_param("si", $email, $id);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Email sudah digunakan']);
            exit;
        }
    }

    // Build query update
    $updateFields = [];
    $types = '';
    $values = [];

    if ($username !== null) {
        $updateFields[] = "username = ?";
        $types .= 's';
        $values[] = $username;
    }
    if ($email !== null) {
        $updateFields[] = "email = ?";
        $types .= 's';
        $values[] = $email;
    }
    if ($fullName !== null) {
        $updateFields[] = "fullName = ?";
        $types .= 's';
        $values[] = $fullName;
    }
    if ($address !== null) {
        $updateFields[] = "address = ?";
        $types .= 's';
        $values[] = $address;
    }
    if ($role !== null) {
        $updateFields[] = "role = ?";
        $types .= 's';
        $values[] = $role;
    }
    if ($password !== null) {
        $updateFields[] = "password = ?";
        $types .= 's';
        $values[] = password_hash($password, PASSWORD_DEFAULT);
    }

    if (empty($updateFields)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Tidak ada data yang diupdate']);
        exit;
    }

    // Tambahkan ID ke values dan types
    $values[] = $id;
    $types .= 'i';

    $query = "UPDATE users SET " . implode(', ', $updateFields) . " WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$values);

    if ($stmt->execute()) {
        // Ambil data user yang sudah diupdate
        $stmt = $conn->prepare("SELECT id, username, email, role, created_at, photo, fullName, address FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $updatedUser = $result->fetch_assoc();

        // Format tanggal dengan zona waktu yang benar
        $client_timezone = isset($_POST['timezone']) ? $_POST['timezone'] : 'Asia/Jakarta';
        
        // Check if function exists before calling it
        if (function_exists('formatUserFriendlyTime')) {
            $updatedUser['date'] = formatUserFriendlyTime($updatedUser['created_at'], $client_timezone);
        } else {
            // Fallback if function doesn't exist
            try {
                $utc_datetime = new DateTime($updatedUser['created_at'], new DateTimeZone('UTC'));
                $utc_datetime->setTimezone(new DateTimeZone($client_timezone));
                $updatedUser['date'] = $utc_datetime->format('d/m/Y H:i');
            } catch (Exception $e) {
                $updatedUser['date'] = $updatedUser['created_at'];
            }
        }
        
        // Format waktu hanya jam dan menit dengan AM/PM
        try {
            $utc_datetime = new DateTime($updatedUser['created_at'], new DateTimeZone('UTC'));
            $utc_datetime->setTimezone(new DateTimeZone($client_timezone));
            $updatedUser['time'] = $utc_datetime->format('h:i A');
        } catch (Exception $e) {
            // Check if function exists before calling it
            if (function_exists('convertToClientTimezone')) {
                $updatedUser['time'] = convertToClientTimezone($updatedUser['created_at'], $client_timezone);
            } else {
                // Fallback if function doesn't exist
                try {
                    $utc_datetime = new DateTime($updatedUser['created_at'], new DateTimeZone('UTC'));
                    $utc_datetime->setTimezone(new DateTimeZone($client_timezone));
                    $updatedUser['time'] = $utc_datetime->format('h:i A');
                } catch (Exception $e2) {
                    $updatedUser['time'] = $updatedUser['created_at'];
                }
            }
        }

        // Biarkan photo kosong jika tidak ada di database
        if (!$updatedUser['photo']) {
            $updatedUser['photo'] = null;
        }

        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'User berhasil diupdate',
            'data' => $updatedUser
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Gagal mengupdate user di database']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error server: ' . $e->getMessage()]);
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($conn)) {
        $conn->close();
    }
}
?> 