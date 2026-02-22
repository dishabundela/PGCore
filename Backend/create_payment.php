<?php
include "db.php";
session_start();

$user_id = $_SESSION['user_id'];
$payment_month = $_POST['month'] ?? '';

$base_rent = 8000;
$current_day = date('j');
$late_fee = ($current_day > 5) ? 800 : 0;
$total_amount = $base_rent + $late_fee;

$sql = "INSERT INTO payments (user_id, amount, late_fee, payment_month, payment_date, status) 
        VALUES ($user_id, $total_amount, $late_fee, '$payment_month', CURDATE(), 'completed')";

if(mysqli_query($conn, $sql)) {
    echo "success";
} else {
    echo "error: " . mysqli_error($conn);
}
?>