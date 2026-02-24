<?php
// Backend/get_user_complaints.php
include "db.php";
session_start();
header('Content-Type: application/json');

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true){
    echo json_encode(['error' => 'not_logged_in']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Check if complaints table exists
$check_table = mysqli_query($conn, "SHOW TABLES LIKE 'complaints'");
if(mysqli_num_rows($check_table) == 0) {
    echo json_encode([]);
    exit;
}

// Get complaints for this user
$sql = "SELECT complaint_id, title, category, room_no, complaint_text as description, complaint_date, status 
        FROM complaints 
        WHERE user_id = ? 
        ORDER BY complaint_date DESC, complaint_id DESC";

$stmt = mysqli_prepare($conn, $sql);

if(!$stmt){
    echo json_encode(['error' => 'prepare_error']);
    exit;
}

mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$complaints = [];
while($row = mysqli_fetch_assoc($result)){
    $complaints[] = $row;
}

echo json_encode($complaints);

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>