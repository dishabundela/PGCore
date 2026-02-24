<?php
// Backend/get_visitor_log.php
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

// Check if table exists
$check_table = mysqli_query($conn, "SHOW TABLES LIKE 'visitor_log'");
if(mysqli_num_rows($check_table) == 0) {
    echo json_encode([]);
    exit;
}

// Select all columns
$sql = "SELECT id, visitor_name, relation, contact, room_no, visit_date, 
        TIME_FORMAT(in_time, '%H:%i') as in_time, 
        TIME_FORMAT(out_time, '%H:%i') as out_time 
        FROM visitor_log 
        WHERE user_id = ? 
        ORDER BY visit_date DESC, in_time DESC";

$stmt = mysqli_prepare($conn, $sql);

if(!$stmt){
    echo json_encode(['error' => 'prepare_error']);
    exit;
}

mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$visitors = [];
while($row = mysqli_fetch_assoc($result)){
    // Format out_time if it exists
    if($row['out_time'] === null) {
        $row['out_time'] = null;
    }
    $visitors[] = $row;
}

echo json_encode($visitors);
mysqli_close($conn);
?>