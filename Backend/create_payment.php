<?php
include "db.php";
session_start();

if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true){
    echo "not_logged_in";
    exit;
}

$user_id = $_SESSION['user_id'];
$amount = $_POST['amount'] ?? '';
$payment_month = $_POST['month'] ?? '';
$payment_date = date('Y-m-d');

if(empty($amount) || empty($payment_month)){
    echo "empty";
    exit;
}

$sql = "INSERT INTO payments (user_id, amount, payment_month, payment_date, status) 
        VALUES (?, ?, ?, ?, 'completed')";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "idss", $user_id, $amount, $payment_month, $payment_date);

if(mysqli_stmt_execute($stmt)){
    echo "success";
} else {
    echo "error";
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>