<?php
// Backend/admin/update_booking_status.php
include "../db.php";
session_start();
header('Content-Type: text/plain');

if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true){
    echo "unauthorized";
    exit;
}

$booking_id = $_POST['booking_id'] ?? '';
$status = $_POST['status'] ?? ''; // 'confirmed' or 'rejected'

if(empty($booking_id) || empty($status)){
    echo "empty";
    exit;
}

$sql = "UPDATE bookings SET booking_status = ? WHERE booking_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "si", $status, $booking_id);

if(mysqli_stmt_execute($stmt)){
    // If confirmed, update room status
    if($status == 'confirmed'){
        $room_sql = "UPDATE rooms SET status = 'occupied' WHERE room_id = (SELECT room_id FROM bookings WHERE booking_id = ?)";
        $room_stmt = mysqli_prepare($conn, $room_sql);
        mysqli_stmt_bind_param($room_stmt, "i", $booking_id);
        mysqli_stmt_execute($room_stmt);
    }
    echo "success";
} else {
    echo "error";
}

mysqli_close($conn);
?>