<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$conn = new mysqli("localhost", "root", "", "ruanganft_db");
if ($conn->connect_error) {
  die(json_encode(["success" => false, "error" => "Koneksi gagal: " . $conn->connect_error]));
}

// Ambil semua data ruangan
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  $tanggal = isset($_GET['tanggal']) ? $conn->real_escape_string($_GET['tanggal']) : null;
  $query = "SELECT * FROM room_status";
  if ($tanggal) {
    $query .= " WHERE tanggal = '$tanggal'";
  }
  $query .= " ORDER BY tanggal DESC, waktu ASC";

  $result = $conn->query($query);
  $rows = [];
  while ($row = $result->fetch_assoc()) {
    $rows[] = $row;
  }
  echo json_encode($rows);
  exit;
}

// Tambah atau update data ruangan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $data = json_decode(file_get_contents("php://input"), true);
  if (!$data) {
    echo json_encode(["success" => false, "error" => "Data kosong"]);
    exit;
  }

  $tanggal = $conn->real_escape_string($data['tanggal']);
  $waktu = $conn->real_escape_string($data['waktu']);
  $ruang = $conn->real_escape_string($data['ruang']);
  $status = $conn->real_escape_string($data['status']);
  $by_nama = isset($data['by_nama']) ? $conn->real_escape_string($data['by_nama']) : '';
  $nim = isset($data['nim']) ? $conn->real_escape_string($data['nim']) : '';
  $ket = isset($data['ket']) ? $conn->real_escape_string($data['ket']) : '';

  // Cegah tabrakan jadwal
  $check = $conn->query("SELECT * FROM room_status 
                         WHERE tanggal='$tanggal' 
                         AND ruang='$ruang' 
                         AND waktu='$waktu' 
                         AND status='terpakai'");
  if ($check && $check->num_rows > 0 && $status === 'terpakai') {
    echo json_encode([
      "success" => false,
      "error" => "Ruang $ruang pada $tanggal jam $waktu sudah dipakai."
    ]);
    exit;
  }

  // Update jika sudah ada, insert jika belum
  $query = "INSERT INTO room_status (tanggal, waktu, ruang, status, by_nama, nim, ket)
            VALUES ('$tanggal', '$waktu', '$ruang', '$status', '$by_nama', '$nim', '$ket')
            ON DUPLICATE KEY UPDATE
              status='$status',
              by_nama='$by_nama',
              nim='$nim',
              ket='$ket'";

  if ($conn->query($query)) {
    echo json_encode(["success" => true]);
  } else {
    echo json_encode(["success" => false, "error" => $conn->error]);
  }
  exit;
}

// Hapus jadwal tertentu
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
  $data = json_decode(file_get_contents("php://input"), true);
  if (!$data || !isset($data['tanggal'], $data['waktu'], $data['ruang'])) {
    echo json_encode(["success" => false, "error" => "Data tidak lengkap untuk penghapusan"]);
    exit;
  }

  $tanggal = $conn->real_escape_string($data['tanggal']);
  $waktu   = $conn->real_escape_string($data['waktu']);
  $ruang   = $conn->real_escape_string($data['ruang']);

  $query = "DELETE FROM room_status WHERE tanggal='$tanggal' AND waktu='$waktu' AND ruang='$ruang'";

  if ($conn->query($query)) {
    echo json_encode(["success" => true]);
  } else {
    echo json_encode(["success" => false, "error" => $conn->error]);
  }
  exit;
}

$conn->close();
?>

