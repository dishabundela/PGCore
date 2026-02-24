<?php
// Backend/update_visitor_out.php
include "db.php";
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: text/plain');

if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true){
    echo "not_logged_in";
    exit;
}

$visitor_id = isset($_POST['visitor_id']) ? $_POST['visitor_id'] : '';
$out_time = isset($_POST['out_time']) ? $_POST['out_time'] : '';

if(empty($visitor_id) || empty($out_time)){
    echo "empty";
    exit;
}

// Verify this visitor belongs to the logged-in user
$user_id = $_SESSION['user_id'];
$check_sql = "SELECT id FROM visitor_log WHERE id = ? AND user_id = ?";
$check_stmt = mysqli_prepare($conn, $check_sql);
mysqli_stmt_bind_param($check_stmt, "ii", $visitor_id, $user_id);
mysqli_stmt_execute($check_stmt);
$check_result = mysqli_stmt_get_result($check_stmt);

if(mysqli_num_rows($check_result) == 0){
    echo "unauthorized";
    exit;
}

// Update out time
$sql = "UPDATE visitor_log SET out_time = ? WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "si", $out_time, $visitor_id);

if(mysqli_stmt_execute($stmt)){
    echo "success";
} else {
    echo "error: " . mysqli_error($conn);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>