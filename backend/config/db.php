<?php
$host = "localhost";         // atau host dari hosting Anda
$user = "u698909386_mc_user";      // ganti dengan user database Anda
$pass = "MindCare123";       // ganti dengan password database Anda
$db   = "u698909386_db_mindcare";     // ganti dengan nama database Anda

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}
?>