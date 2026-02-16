<?php
// Backend/get_rooms.php
include "db.php";
header('Content-Type: application/json');

// Using 'status' column as per your table structure
$sql = "SELECT room_id, room_number, room_type, rent as price, status FROM rooms WHERE status = 'available'";
$result = mysqli_query($conn, $sql);

$rooms = [];
while($row = mysqli_fetch_assoc($result)){
    $rooms[] = $row;
}

echo json_encode($rooms);
mysqli_close($conn);
?>