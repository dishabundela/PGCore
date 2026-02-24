<?php
// Backend/admin/get_all_bookings.php
include "../db.php";
session_start();
header('Content-Type: application/json');

if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true){
    echo json_encode(['error' => 'unauthorized']);
    exit;
}

$sql = "SELECT b.booking_id as id, u.fullname as name, u.phone as mobile, u.email,
        r.room_number as roomPreference, r.room_type as roomType,
        b.move_in_date as checkInDate, u.occupation, 
        '' as message, b.booking_date as requestedOn,
        b.booking_status as status
        FROM bookings b
        JOIN users u ON b.user_id = u.id
        JOIN rooms r ON b.room_id = r.room_id
        ORDER BY b.booking_date DESC";

$result = mysqli_query($conn, $sql);
$bookings = [];
while($row = mysqli_fetch_assoc($result)){
    $bookings[] = $row;
}

echo json_encode($bookings);
mysqli_close($conn);
?>