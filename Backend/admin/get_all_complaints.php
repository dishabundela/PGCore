<?php
// Backend/admin/get_all_complaints.php
include "../db.php";
session_start();
header('Content-Type: application/json');

if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true){
    echo json_encode(['error' => 'unauthorized']);
    exit;
}

$sql = "SELECT c.complaint_id as id, u.fullname as residentName, 
        c.room_no as room, c.title, c.complaint_text as description,
        c.complaint_date as date, c.status, 
        CASE WHEN c.status = 'pending' THEN 'high' ELSE 'low' END as priority
        FROM complaints c
        JOIN users u ON c.user_id = u.id
        ORDER BY c.complaint_date DESC";

$result = mysqli_query($conn, $sql);
$complaints = [];
while($row = mysqli_fetch_assoc($result)){
    $complaints[] = $row;
}

echo json_encode($complaints);
mysqli_close($conn);
?>