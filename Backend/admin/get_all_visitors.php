<?php
// Backend/admin/get_all_visitors.php
include "../db.php";
session_start();
header('Content-Type: application/json');

if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true){
    echo json_encode(['error' => 'unauthorized']);
    exit;
}

$sql = "SELECT v.id, v.visitor_name as name, v.contact, 
        u.fullname as residentName, v.room_no as room,
        CONCAT(v.visit_date, 'T', v.in_time) as checkIn,
        CASE WHEN v.out_time IS NOT NULL THEN CONCAT(v.visit_date, 'T', v.out_time) ELSE NULL END as checkOut,
        v.relation as purpose, '' as vehicle, '' as idProof,
        CASE WHEN v.out_time IS NULL THEN 'checked-in' ELSE 'checked-out' END as status
        FROM visitor_log v
        JOIN users u ON v.user_id = u.id
        ORDER BY v.visit_date DESC, v.in_time DESC";

$result = mysqli_query($conn, $sql);
$visitors = [];
while($row = mysqli_fetch_assoc($result)){
    $visitors[] = $row;
}

echo json_encode($visitors);
mysqli_close($conn);
?>