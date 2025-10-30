<?php
include 'config.php';

$result = $conn->query("SELECT * FROM rooms ORDER BY code ASC");
$rooms = [];
while($row = $result->fetch_assoc()){
    $rooms[] = $row;
}
echo json_encode($rooms);
?>
