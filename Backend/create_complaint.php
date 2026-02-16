<?php
include "db.php";
session_start();

if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true){
    echo "not_logged_in";
    exit;
}

$user_id = $_SESSION['user_id'];
$complaint_text = $_POST['complaint'] ?? '';

if(empty($complaint_text)){
    echo "empty";
    exit;
}

$complaint_date = date('Y-m-d');

$sql = "INSERT INTO complaints (user_id, complaint_text, complaint_date, status) 
        VALUES (?, ?, ?, 'pending')";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "iss", $user_id, $complaint_text, $complaint_date);

if(mysqli_stmt_execute($stmt)){
    echo "success";
} else {
    echo "error";
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>