<?php
// Backend/get_user_bookings.php
include "db.php";
session_start();
header('Content-Type: application/json');

if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true){
    echo json_encode(['error' => 'not_logged_in']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Join with rooms table to get room details
$sql = "SELECT b.*, 
        r.room_number, 
        r.room_type, 
        r.rent as room_rent,
        u.fullname,
        u.email,
        u.phone
        FROM bookings b 
        LEFT JOIN rooms r ON b.room_id = r.room_id 
        LEFT JOIN users u ON b.user_id = u.id
        WHERE b.user_id = ? 
        ORDER BY b.booking_date DESC";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$bookings = [];
while($row = mysqli_fetch_assoc($result)){
    $bookings[] = $row;
}

echo json_encode($bookings);
mysqli_close($conn);
?>