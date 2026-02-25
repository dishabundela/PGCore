<?php
// Backend/admin/record_payment.php - For OFFLINE payments only
include "../db.php";
session_start();
header('Content-Type: text/plain');

if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true){
    echo "unauthorized";
    exit;
}

$user_id = $_POST['user_id'] ?? '';
$amount = $_POST['amount'] ?? '';
$month = $_POST['month'] ?? '';

if(empty($user_id) || empty($amount) || empty($month)){
    echo "empty";
    exit;
}

// Check if already paid
$check_sql = "SELECT payment_id FROM payments WHERE user_id = ? AND payment_month = ?";
$check_stmt = mysqli_prepare($conn, $check_sql);
mysqli_stmt_bind_param($check_stmt, "is", $user_id, $month);
mysqli_stmt_execute($check_stmt);
mysqli_stmt_store_result($check_stmt);

if(mysqli_stmt_num_rows($check_stmt) > 0){
    echo "already_paid";
    exit;
}

// Insert offline payment
$payment_date = date('Y-m-d');
$sql = "INSERT INTO payments (user_id, amount, payment_month, payment_date, status, payment_method) 
        VALUES (?, ?, ?, ?, 'completed', 'cash')";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "idss", $user_id, $amount, $month, $payment_date);

if(mysqli_stmt_execute($stmt)){
    echo "success";
} else {
    echo "error: " . mysqli_error($conn);
}

mysqli_close($conn);
?>