<?php
include "db.php";
session_start();
header('Content-Type: application/json');

if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true){
    echo json_encode(['error' => 'not_logged_in']);
    exit;
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT complaint_id, user_id, title, category, room_no, complaint_text as description, complaint_date, status 
        FROM complaints WHERE user_id = ? ORDER BY complaint_date DESC";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$complaints = [];
while($row = mysqli_fetch_assoc($result)){
    $complaints[] = $row;
}

echo json_encode($complaints);
mysqli_close($conn);
?>