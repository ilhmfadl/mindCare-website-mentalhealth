<?php
$host = "localhost";         // atau host dari hosting Anda
$user = "u698909386_mc_user";      // ganti dengan user database Anda
$pass = "MindCare123";       // ganti dengan password database Anda
$db   = "u698909386_db_mindcare";     // ganti dengan nama database Anda

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

// Set charset to utf8
$conn->set_charset("utf8");

// Helper function untuk konversi zona waktu
function convertToClientTimezone($utc_time, $client_timezone = 'Asia/Jakarta') {
    try {
        $utc_datetime = new DateTime($utc_time, new DateTimeZone('UTC'));
        $utc_datetime->setTimezone(new DateTimeZone($client_timezone));
        return $utc_datetime->format('Y-m-d H:i:s');
    } catch (Exception $e) {
        error_log("Timezone conversion failed: " . $e->getMessage());
        return $utc_time; // Return original time if conversion fails
    }
}

// Helper function untuk mendapatkan waktu client dengan zona waktu
function getClientTimeWithTimezone($client_time, $client_timezone) {
    try {
        $client_datetime = new DateTime($client_time, new DateTimeZone($client_timezone));
        $client_datetime->setTimezone(new DateTimeZone('UTC'));
        return $client_datetime->format('Y-m-d H:i:s');
    } catch (Exception $e) {
        error_log("Client timezone conversion failed: " . $e->getMessage());
        return date('Y-m-d H:i:s'); // Return server time if conversion fails
    }
}

// Helper function untuk format waktu yang user-friendly
function formatUserFriendlyTime($utc_time, $client_timezone = 'Asia/Jakarta') {
    try {
        $utc_datetime = new DateTime($utc_time, new DateTimeZone('UTC'));
        $utc_datetime->setTimezone(new DateTimeZone($client_timezone));
        
        $now = new DateTime('now', new DateTimeZone($client_timezone));
        $diff = $now->diff($utc_datetime);
        
        if ($diff->days == 0) {
            if ($diff->h == 0) {
                if ($diff->i == 0) {
                    return 'Baru saja';
                } else {
                    return $diff->i . ' menit yang lalu';
                }
            } else {
                return $diff->h . ' jam yang lalu';
            }
        } elseif ($diff->days == 1) {
            return 'Kemarin ' . $utc_datetime->format('H:i');
        } else {
            return $utc_datetime->format('d/m/Y H:i');
        }
    } catch (Exception $e) {
        error_log("Time formatting failed: " . $e->getMessage());
        return $utc_time;
    }
}
?>