<?php
include "db.php";
session_start();

if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true){
    echo "not_logged_in";
    exit;
}

$visitor_id = $_POST['visitor_id'] ?? '';
$out_time = $_POST['out_time'] ?? '';

if(empty($visitor_id) || empty($out_time)){
    echo "empty";
    exit;
}

$sql = "UPDATE visitor_log SET out_time = ? WHERE visitor_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "si", $out_time, $visitor_id);

if(mysqli_stmt_execute($stmt)){
    echo "success";
} else {
    echo "error";
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>