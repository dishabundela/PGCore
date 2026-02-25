<?php
// Backend/admin/get_all_payments.php - FIXED VERSION
include "../db.php";
session_start();
header('Content-Type: application/json');

if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true){
    echo json_encode(['error' => 'unauthorized']);
    exit;
}

$sql = "SELECT 
            p.payment_id as id, 
            u.fullname as residentName, 
            r.room_number as room,
            p.amount, 
            p.payment_month as month, 
            DATE_FORMAT(p.payment_date, '%d-%m-%Y') as date,
            CASE 
                WHEN p.late_fee > 0 THEN 'online (late)'
                ELSE 'online'
            END as method,
            p.status
        FROM payments p
        JOIN users u ON p.user_id = u.id
        LEFT JOIN bookings b ON u.id = b.user_id AND b.booking_status = 'confirmed'
        LEFT JOIN rooms r ON b.room_id = r.room_id
        ORDER BY p.payment_date DESC";

$result = mysqli_query($conn, $sql);

if(!$result){
    echo json_encode(['error' => mysqli_error($conn)]);
    exit;
}

$payments = [];
while($row = mysqli_fetch_assoc($result)){
    $payments[] = $row;
}

echo json_encode($payments);
mysqli_close($conn);
?>