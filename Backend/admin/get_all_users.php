<?php
// Backend/admin/get_all_users.php
include "../db.php";
session_start();
header('Content-Type: application/json');

// Check if admin is logged in
if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true){
    echo json_encode(['error' => 'unauthorized']);
    exit;
}

$sql = "SELECT u.id, u.fullname as name, u.email, u.phone as mobile, 
        COALESCE(b.room_id, 'Not Assigned') as room,
        COALESCE(b.booking_date, '') as checkinDate,
        CASE WHEN p.payment_id IS NOT NULL THEN 'paid' ELSE 'pending' END as rentStatus,
        CASE WHEN b.booking_status = 'confirmed' THEN 'active' ELSE 'inactive' END as status
        FROM users u
        LEFT JOIN bookings b ON u.id = b.user_id AND b.booking_status = 'confirmed'
        LEFT JOIN payments p ON u.id = p.user_id AND p.payment_month = DATE_FORMAT(NOW(), '%M %Y')
        ORDER BY u.id DESC";

$result = mysqli_query($conn, $sql);
$users = [];
while($row = mysqli_fetch_assoc($result)){
    $users[] = $row;
}

echo json_encode($users);
mysqli_close($conn);
?>