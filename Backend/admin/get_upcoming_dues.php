<?php
// Backend/admin/get_upcoming_dues.php - FIXED VERSION
include "../db.php";
session_start();
header('Content-Type: application/json');

if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true){
    echo json_encode(['error' => 'unauthorized']);
    exit;
}

// Get current month
$current_month = date('F Y');

// Get all confirmed residents who haven't paid for current month
$sql = "SELECT 
            u.id,
            u.fullname as residentName,
            r.room_number as room,
            r.rent as amount,
            '5th of next month' as dueDate,
            0 as daysOverdue,
            'pending' as status
        FROM users u
        JOIN bookings b ON u.id = b.user_id 
        JOIN rooms r ON b.room_id = r.room_id
        WHERE b.booking_status = 'confirmed'
        AND u.id NOT IN (
            SELECT user_id FROM payments 
            WHERE payment_month = ?
        )
        ORDER BY u.fullname";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $current_month);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$dues = [];
while($row = mysqli_fetch_assoc($result)){
    $dues[] = $row;
}

echo json_encode($dues);
mysqli_close($conn);
?>