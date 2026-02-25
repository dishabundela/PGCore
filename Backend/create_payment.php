<?php
// Backend/create_payment.php - FIXED VERSION
include "db.php";
session_start();
header('Content-Type: text/plain');

if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true){
    echo "not_logged_in";
    exit;
}

$user_id = $_SESSION['user_id'];
$payment_month = $_POST['month'] ?? '';

if(empty($payment_month)){
    echo "empty";
    exit;
}

// Check if payment already exists for this month
$check_sql = "SELECT payment_id FROM payments WHERE user_id = ? AND payment_month = ?";
$check_stmt = mysqli_prepare($conn, $check_sql);
mysqli_stmt_bind_param($check_stmt, "is", $user_id, $payment_month);
mysqli_stmt_execute($check_stmt);
mysqli_stmt_store_result($check_stmt);

if(mysqli_stmt_num_rows($check_stmt) > 0){
    echo "already_paid";
    exit;
}

// Get user's room rent from their CONFIRMED booking
$rent_query = "SELECT r.rent FROM bookings b 
               JOIN rooms r ON b.room_id = r.room_id 
               WHERE b.user_id = ? AND b.booking_status = 'confirmed'
               ORDER BY b.booking_date DESC LIMIT 1";

$stmt = mysqli_prepare($conn, $rent_query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if($row = mysqli_fetch_assoc($result)){
    $base_rent = $row['rent'];
} else {
    echo "no_booking";
    exit;
}

// Calculate late fee (after 5th of month)
$current_day = date('j');
$late_fee = ($current_day > 5) ? 800 : 0;
$total_amount = $base_rent + $late_fee;

// Insert payment
$sql = "INSERT INTO payments (user_id, amount, late_fee, payment_month, payment_date, status) 
        VALUES (?, ?, ?, ?, CURDATE(), 'completed')";

$insert_stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($insert_stmt, "idds", $user_id, $total_amount, $late_fee, $payment_month);

if(mysqli_stmt_execute($insert_stmt)){
    echo "success";
} else {
    echo "error: " . mysqli_error($conn);
}

mysqli_close($conn);
?>