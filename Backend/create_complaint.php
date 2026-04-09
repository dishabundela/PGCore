<?php
session_start();

header('Content-Type: text/plain');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Set timezone to Indian Standard Time
date_default_timezone_set('Asia/Kolkata');

// Include database connection
require_once 'db.php';

// Get user_id from session (the ACTUAL logged-in user)
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

if($user_id == 0) {
    echo "error: User not logged in";
    exit;
}

$title = isset($_POST['title']) ? $_POST['title'] : '';
$category = isset($_POST['category']) ? $_POST['category'] : '';
$room_no = isset($_POST['room_no']) ? $_POST['room_no'] : '';
$description = isset($_POST['description']) ? $_POST['description'] : '';
$status = 'pending';
$currentDateTime = date('Y-m-d H:i:s');
$currentDate = date('Y-m-d');

if (empty($title) || empty($room_no) || empty($description)) {
    echo "error: Missing fields";
    exit;
}

$sql = "INSERT INTO complaints (user_id, title, category, room_no, complaint_text, status, complaint_date, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "isssssss", $user_id, $title, $category, $room_no, $description, $status, $currentDate, $currentDateTime);

if (mysqli_stmt_execute($stmt)) {
    echo "success";
} else {
    echo "error: " . mysqli_error($conn);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>