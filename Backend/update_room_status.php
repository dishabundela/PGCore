<?php
// Backend/update_room_status.php
include "db.php";

$room_id = $_POST['room_id'] ?? '';
$status = $_POST['status'] ?? 'booked';

if(empty($room_id)){
    echo "empty";
    exit;
}

$sql = "UPDATE rooms SET status = ? WHERE room_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "si", $status, $room_id);

if(mysqli_stmt_execute($stmt)){
    echo "success";
} else {
    echo "error";
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>