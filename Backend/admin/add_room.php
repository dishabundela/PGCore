<?php
// Backend/admin/add_room.php
include "../db.php";
session_start();
header('Content-Type: text/plain');

if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true){
    echo "unauthorized";
    exit;
}

$room_number = $_POST['room_number'] ?? '';
$room_type = $_POST['room_type'] ?? '';
$capacity = $_POST['capacity'] ?? '';
$rent = $_POST['rent'] ?? '';
$amenities = isset($_POST['amenities']) ? implode(',', $_POST['amenities']) : '';
$status = $_POST['status'] ?? 'available';

if(empty($room_number) || empty($room_type) || empty($capacity) || empty($rent)){
    echo "empty";
    exit;
}

$sql = "INSERT INTO rooms (room_number, room_type, capacity, rent, amenities, status) 
        VALUES (?, ?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ssiiss", $room_number, $room_type, $capacity, $rent, $amenities, $status);

if(mysqli_stmt_execute($stmt)){
    echo "success";
} else {
    echo "error";
}

mysqli_close($conn);
?>