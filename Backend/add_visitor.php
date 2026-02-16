<?php
include "db.php";
session_start();

if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true){
    echo "not_logged_in";
    exit;
}

$user_id = $_SESSION['user_id'];
$visitor_name = $_POST['visitor_name'] ?? '';
$visit_date = $_POST['visit_date'] ?? '';
$in_time = $_POST['in_time'] ?? '';

if(empty($visitor_name) || empty($visit_date) || empty($in_time)){
    echo "empty";
    exit;
}

$sql = "INSERT INTO visitor_log (user_id, visitor_name, visit_date, in_time) 
        VALUES (?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "isss", $user_id, $visitor_name, $visit_date, $in_time);

if(mysqli_stmt_execute($stmt)){
    echo "success";
} else {
    echo "error";
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>