<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config/db.php';

if ($conn && !$conn->connect_error) {
    echo "Koneksi ke database BERHASIL!";
} else {
    echo "Koneksi GAGAL: " . $conn->connect_error;
}
?>