<?php
// Backend/create_booking.php
include "db.php";
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is logged in
if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true){
    echo "not_logged_in";
    exit;
}

$user_id = $_SESSION['user_id'];
$booking_date = $_POST['booking_date'] ?? '';
$move_in_date = $_POST['move_in_date'] ?? '';
$booking_amount = $_POST['price'] ?? '';
$room_id = $_POST['room_id'] ?? ''; // Get room_id from form

// Validate all fields including room_id
if(empty($booking_date) || empty($move_in_date) || empty($booking_amount) || empty($room_id)){
    echo "empty";
    exit;
}

// First check if room exists and is available (using 'status' column as per your table)
$check_room = mysqli_prepare($conn, "SELECT room_id FROM rooms WHERE room_id = ? AND status = 'available'");
mysqli_stmt_bind_param($check_room, "i", $room_id);
mysqli_stmt_execute($check_room);
$room_result = mysqli_stmt_get_result($check_room);

if(mysqli_num_rows($room_result) == 0){
    echo "invalid_room";
    exit;
}
mysqli_stmt_close($check_room);

// Check if user already has a pending/confirmed booking
$check_user_booking = mysqli_prepare($conn, "SELECT booking_id FROM bookings WHERE user_id = ? AND booking_status IN ('pending', 'confirmed')");
mysqli_stmt_bind_param($check_user_booking, "i", $user_id);
mysqli_stmt_execute($check_user_booking);
$user_booking_result = mysqli_stmt_get_result($check_user_booking);

if(mysqli_num_rows($user_booking_result) > 0){
    echo "already_booked";
    exit;
}
mysqli_stmt_close($check_user_booking);

// Insert booking with room_id
$sql = "INSERT INTO bookings (user_id, room_id, booking_date, move_in_date, booking_amount, payment_status, booking_status) 
        VALUES (?, ?, ?, ?, ?, 'pending', 'pending')";

$stmt = mysqli_prepare($conn, $sql);

if(!$stmt){
    echo "error";
    exit;
}

mysqli_stmt_bind_param($stmt, "iissd", $user_id, $room_id, $booking_date, $move_in_date, $booking_amount);

if(mysqli_stmt_execute($stmt)){
    // Update room status to 'booked' (using 'status' column as per your table)
    $update_room = mysqli_prepare($conn, "UPDATE rooms SET status = 'booked' WHERE room_id = ?");
    mysqli_stmt_bind_param($update_room, "i", $room_id);
    mysqli_stmt_execute($update_room);
    mysqli_stmt_close($update_room);
    
    echo "success";
} else {
    echo "error";
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>