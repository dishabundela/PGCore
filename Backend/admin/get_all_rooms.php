<?php
// Backend/admin/get_all_rooms.php
include "../db.php";
session_start();
header('Content-Type: application/json');

if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true){
    echo json_encode(['error' => 'unauthorized']);
    exit;
}

$sql = "SELECT room_id, room_number as number, room_type as type, capacity, rent, amenities, status,
        (SELECT COUNT(*) FROM bookings WHERE room_id = rooms.room_id AND booking_status = 'confirmed') as occupants
        FROM rooms ORDER BY room_number";

$result = mysqli_query($conn, $sql);
$rooms = [];
while($row = mysqli_fetch_assoc($result)){
    // Convert amenities string to array if stored as JSON/comma-separated
    $row['amenities'] = $row['amenities'] ? explode(',', $row['amenities']) : [];
    $rooms[] = $row;
}

echo json_encode($rooms);
mysqli_close($conn);
?>