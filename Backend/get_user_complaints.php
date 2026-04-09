<?php
session_start();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

// Include database connection
require_once 'db.php';

// Get logged-in user from session
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

if($user_id == 0) {
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

$sql = "SELECT complaint_id, title, category, room_no, complaint_text as description, status, created_at as complaint_date 
        FROM complaints 
        WHERE user_id = ? 
        ORDER BY created_at DESC";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$complaints = [];
while ($row = mysqli_fetch_assoc($result)) {
    $complaints[] = $row;
}

echo json_encode($complaints);

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>