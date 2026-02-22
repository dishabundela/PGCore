<?php
include "db.php";
session_start();

if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true){
    echo "not_logged_in";
    exit;
}

$user_id = $_SESSION['user_id'];

// Get separate fields
$title = $_POST['title'] ?? '';
$category = $_POST['category'] ?? '';
$room_no = $_POST['room_no'] ?? '';
$description = $_POST['description'] ?? '';

if(empty($title) || empty($category) || empty($room_no) || empty($description)){
    echo "empty";
    exit;
}

$complaint_date = date('Y-m-d');

// Insert with separate columns
$sql = "INSERT INTO complaints (user_id, title, category, room_no, complaint_text, complaint_date, status) 
        VALUES (?, ?, ?, ?, ?, ?, 'pending')";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "isssss", $user_id, $title, $category, $room_no, $description, $complaint_date);

if(mysqli_stmt_execute($stmt)){
    echo "success";
} else {
    echo "error: " . mysqli_error($conn);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>