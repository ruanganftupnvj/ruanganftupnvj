<?php
$host = "localhost";
$user = "root"; 
$pass = "";     
$db   = "ruanganft_db";

$conn = new mysqli($host, $user, $pass, $db);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}
?>

