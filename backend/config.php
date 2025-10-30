<?php
header('Content-Type: application/json; charset=utf-8');

$host = "localhost";       
$user = "root";            
$pass = "";                
$db   = "ruanganft_db";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
  die(json_encode(["error" => "Koneksi gagal: " . $conn->connect_error]));
}
?>

