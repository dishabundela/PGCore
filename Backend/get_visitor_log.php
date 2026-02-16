<?php
include "db.php";
session_start();
header('Content-Type: application/json');

if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true){
    echo json_encode(['error' => 'not_logged_in']);
    exit;
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM visitor_log WHERE user_id = ? ORDER BY visit_date DESC, in_time DESC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$visitors = [];
while($row = mysqli_fetch_assoc($result)){
    $visitors[] = $row;
}

echo json_encode($visitors);
mysqli_close($conn);
?>