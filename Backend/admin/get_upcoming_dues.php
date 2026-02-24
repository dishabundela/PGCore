<?php
// Backend/admin/get_upcoming_dues.php
include "../db.php";
session_start();
header('Content-Type: application/json');

if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true){
    echo json_encode(['error' => 'unauthorized']);
    exit;
}

$sql = "SELECT u.id, u.fullname as residentName, b.room_id as room,
        r.rent as amount,
        DATE_FORMAT(DATE_ADD(LAST_DAY(CURRENT_DATE()), INTERVAL 1 DAY), '%Y-%m-%d') as dueDate,
        DATEDIFF(DATE_ADD(LAST_DAY(CURRENT_DATE()), INTERVAL 1 DAY), CURRENT_DATE()) as daysOverdue
        FROM users u
        JOIN bookings b ON u.id = b.user_id AND b.booking_status = 'confirmed'
        JOIN rooms r ON b.room_id = r.room_id
        LEFT JOIN payments p ON u.id = p.user_id 
            AND p.payment_month = DATE_FORMAT(DATE_ADD(CURRENT_DATE(), INTERVAL 1 MONTH), '%M %Y')
        WHERE p.payment_id IS NULL
        ORDER BY dueDate";

$result = mysqli_query($conn, $sql);
$dues = [];
while($row = mysqli_fetch_assoc($result)){
    $dues[] = $row;
}

echo json_encode($dues);
mysqli_close($conn);
?>