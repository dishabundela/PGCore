<?php
// Backend/admin/update_complaint_status.php
include "../db.php";
session_start();
header('Content-Type: text/plain');

if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true){
    echo "unauthorized";
    exit;
}

$complaint_id = $_POST['complaint_id'] ?? '';
$status = $_POST['status'] ?? ''; // 'in-progress' or 'resolved'

if(empty($complaint_id) || empty($status)){
    echo "empty";
    exit;
}

$sql = "UPDATE complaints SET status = ? WHERE complaint_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "si", $status, $complaint_id);

if(mysqli_stmt_execute($stmt)){
    echo "success";
} else {
    echo "error";
}

mysqli_close($conn);
?>