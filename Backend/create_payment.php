<?php
// Backend/create_payment.php
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

// Get user's actual rent from their CONFIRMED room
$rent_query = "SELECT r.rent FROM bookings b 
               JOIN rooms r ON b.room_id = r.room_id 
               WHERE b.user_id = ? AND b.booking_status IN ('confirmed', 'approved')
               ORDER BY b.booking_date DESC LIMIT 1";

$stmt = mysqli_prepare($conn, $rent_query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if($row = mysqli_fetch_assoc($result)){
    $base_rent = $row['rent']; // Gets actual room rent (6000, 7500, 8000, etc.)
} else {
    // If no confirmed booking, check if they have ANY booking
    $fallback_query = "SELECT r.rent FROM bookings b 
                       JOIN rooms r ON b.room_id = r.room_id 
                       WHERE b.user_id = ? 
                       ORDER BY b.booking_date DESC LIMIT 1";
    
    $stmt2 = mysqli_prepare($conn, $fallback_query);
    mysqli_stmt_bind_param($stmt2, "i", $user_id);
    mysqli_stmt_execute($stmt2);
    $result2 = mysqli_stmt_get_result($stmt2);
    
    if($row2 = mysqli_fetch_assoc($result2)){
        $base_rent = $row2['rent'];
    } else {
        // Ultimate fallback (should not happen)
        $base_rent = 7500;
    }
}

// Calculate late fee (after 5th of month)
$current_day = date('j');
$late_fee = ($current_day > 5) ? 800 : 0;
$total_amount = $base_rent + $late_fee;

// Insert payment
$sql = "INSERT INTO payments (user_id, amount, late_fee, payment_month, payment_date, status) 
        VALUES (?, ?, ?, ?, CURDATE(), 'completed')";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "idds", $user_id, $total_amount, $late_fee, $payment_month);

if(mysqli_stmt_execute($stmt)){
    echo "success";
} else {
    echo "error: " . mysqli_error($conn);
}

mysqli_close($conn);
?>