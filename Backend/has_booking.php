<?php
// Backend/has_booking.php
include "db.php";
session_start();

// Allow error reporting for debugging (remove after fixing)
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: text/plain');

if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true){
    echo "false";
    exit;
}

$user_id = $_SESSION['user_id'];

// Debug: Write to error log
error_log("Checking has_booking for user_id: " . $user_id);

$sql = "SELECT COUNT(*) as count FROM bookings WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $sql);

if(!$stmt){
    error_log("Prepare failed: " . mysqli_error($conn));
    echo "false";
    exit;
}

mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);

error_log("Bookings found for user $user_id: " . $row['count']);

if($row['count'] > 0){
    echo "true";
} else {
    echo "false";
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>