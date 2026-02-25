<?php
// Backend/admin/get_upcoming_dues.php - COMPLETELY FIXED VERSION
include "../db.php";
session_start();
header('Content-Type: application/json');

if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true){
    echo json_encode(['error' => 'unauthorized']);
    exit;
}

// Get next month name
$next_month = date('F Y', strtotime('first day of next month'));

$sql = "SELECT 
            u.id,
            u.fullname as residentName,
            r.room_number as room,
            r.rent as amount,
            DATE_FORMAT(DATE_ADD(LAST_DAY(CURRENT_DATE()), INTERVAL 1 DAY), '%Y-%m-%d') as dueDate,
            DATEDIFF(DATE_ADD(LAST_DAY(CURRENT_DATE()), INTERVAL 1 DAY), CURRENT_DATE()) as daysOverdue
        FROM users u
        JOIN bookings b ON u.id = b.user_id 
        JOIN rooms r ON b.room_id = r.room_id
        WHERE b.booking_status = 'confirmed'
        AND NOT EXISTS (
            SELECT 1 FROM payments p 
            WHERE p.user_id = u.id 
            AND p.payment_month = ?
        )
        ORDER BY dueDate";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $next_month);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$dues = [];
while($row = mysqli_fetch_assoc($result)){
    // Create a new array with all the data
    $due_item = [
        'id' => $row['id'],           // This is the user_id
        'residentName' => $row['residentName'],
        'room' => $row['room'],
        'amount' => $row['amount'],
        'dueDate' => $row['dueDate'],
        'daysOverdue' => $row['daysOverdue'],
        'status' => 'pending'
    ];
    $dues[] = $due_item;
}

echo json_encode($dues);
mysqli_close($conn);
?>