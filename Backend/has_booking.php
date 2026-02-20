<?php
// Backend/has_booking.php
include "db.php";
session_start();

header('Content-Type: text/plain');

if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true){
    echo "false";
    exit;
}

$user_id = $_SESSION['user_id'];

// Check if user has ANY booking
$sql = "SELECT COUNT(*) as count FROM bookings WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);

if($row['count'] > 0){
    echo "true";
} else {
    echo "false";
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>