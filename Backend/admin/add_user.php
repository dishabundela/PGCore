<?php
// Backend/admin/add_user.php
include "../db.php";
session_start();
header('Content-Type: text/plain');

if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true){
    echo "unauthorized";
    exit;
}

$name = $_POST['name'] ?? '';
$room = $_POST['room'] ?? '';
$mobile = $_POST['mobile'] ?? '';
$email = $_POST['email'] ?? '';
$checkinDate = $_POST['checkinDate'] ?? '';

if(empty($name) || empty($room) || empty($mobile)){
    echo "empty";
    exit;
}

// First insert user
$password = password_hash('Welcome@123', PASSWORD_DEFAULT);
$sql = "INSERT INTO users (fullname, email, phone, password) VALUES (?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ssss", $name, $email, $mobile, $password);

if(mysqli_stmt_execute($stmt)){
    $user_id = mysqli_insert_id($conn);
    
    // Create booking for this user
    $booking_sql = "INSERT INTO bookings (user_id, room_id, booking_date, move_in_date, booking_status) 
                    VALUES (?, ?, CURDATE(), ?, 'confirmed')";
    $booking_stmt = mysqli_prepare($conn, $booking_sql);
    mysqli_stmt_bind_param($booking_stmt, "iis", $user_id, $room, $checkinDate);
    mysqli_stmt_execute($booking_stmt);
    
    echo "success";
} else {
    echo "error";
}

mysqli_close($conn);
?>