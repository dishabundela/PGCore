<?php
// Backend/process_online_payment.php - For ONLINE payments
session_start();
include "db.php";
header('Content-Type: application/json');

// Check if user is logged in
if(!isset($_SESSION['user_id'])){
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];
$amount = $_POST['amount'] ?? '';
$month = $_POST['month'] ?? '';
$transaction_id = $_POST['transaction_id'] ?? '';

if(empty($amount) || empty($month)){
    echo json_encode(['success' => false, 'message' => 'Missing fields']);
    exit;
}

// Check if already paid
$check_sql = "SELECT payment_id FROM payments WHERE user_id = ? AND payment_month = ?";
$check_stmt = mysqli_prepare($conn, $check_sql);
mysqli_stmt_bind_param($check_stmt, "is", $user_id, $month);
mysqli_stmt_execute($check_stmt);
mysqli_stmt_store_result($check_stmt);

if(mysqli_stmt_num_rows($check_stmt) > 0){
    echo json_encode(['success' => false, 'message' => 'Already paid for this month']);
    exit;
}

// Insert ONLINE payment
$payment_date = date('Y-m-d');
$payment_method = 'online';
$status = 'completed';

$sql = "INSERT INTO payments (user_id, amount, payment_month, payment_date, payment_method, status, transaction_id) 
        VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "idsssss", $user_id, $amount, $month, $payment_date, $payment_method, $status, $transaction_id);

if(mysqli_stmt_execute($stmt)){
    echo json_encode(['success' => true, 'message' => 'Payment successful']);
} else {
    echo json_encode(['success' => false, 'message' => mysqli_error($conn)]);
}

mysqli_close($conn);
?>