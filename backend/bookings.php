<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  http_response_code(200);
  exit;
}

$conn = new mysqli("localhost", "root", "", "ruanganft_db");

if ($conn->connect_error) {
  die(json_encode(["success" => false, "error" => "Koneksi gagal: " . $conn->connect_error]));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $data = json_decode(file_get_contents("php://input"), true);

  if (!$data) {
    echo json_encode(["success" => false, "error" => "Data kosong"]);
    exit;
  }

  $nama = $conn->real_escape_string($data['nama']);
  $nim = $conn->real_escape_string($data['nim']);
  $tanggal = $conn->real_escape_string($data['tanggal']);
  $waktu = $conn->real_escape_string($data['waktu']);
  $ruang = $conn->real_escape_string($data['ruang']);
  $ket = $conn->real_escape_string($data['ket']);
  $status = $conn->real_escape_string($data['status']);
  $user = $conn->real_escape_string($data['user']);

  $query = "INSERT INTO bookings (nama, nim, tanggal, waktu, ruang, ket, status, user)
            VALUES ('$nama', '$nim', '$tanggal', '$waktu', '$ruang', '$ket', '$status', '$user')";
  if ($conn->query($query)) {
    echo json_encode(["success" => true, "id" => $conn->insert_id]);
  } else {
    echo json_encode(["success" => false, "error" => $conn->error]);
  }
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
  $data = json_decode(file_get_contents("php://input"), true);

  if (!isset($data['id']) || !isset($data['status'])) {
    echo json_encode(["success" => false, "error" => "Data ID atau status tidak ada"]);
    exit;
  }

  $id = intval($data['id']);
  $status = $conn->real_escape_string($data['status']);

  $query = "UPDATE bookings SET status = '$status' WHERE id = $id";
  if ($conn->query($query)) {
    echo json_encode(["success" => true]);
  } else {
    echo json_encode(["success" => false, "error" => $conn->error]);
  }
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  $result = $conn->query("SELECT * FROM bookings ORDER BY id DESC");
  $rows = [];
  while ($row = $result->fetch_assoc()) {
    $rows[] = $row;
  }
  echo json_encode($rows);
}
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
  $data = json_decode(file_get_contents("php://input"), true);
  if (!isset($data['id'])) {
    echo json_encode(["success" => false, "error" => "ID booking tidak dikirim"]);
    exit;
  }

  $id = intval($data['id']);
  $query = "DELETE FROM bookings WHERE id = $id";
  if ($conn->query($query)) {
    echo json_encode(["success" => true]);
  } else {
    echo json_encode(["success" => false, "error" => $conn->error]);
  }
  exit;
}
$conn->close();
?>
