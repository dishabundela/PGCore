<?php
// Backend/admin/get_all_payments.php
include "../db.php";
session_start();
header('Content-Type: application/json');

if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true){
    echo json_encode(['error' => 'unauthorized']);
    exit;
}

$sql = "SELECT p.payment_id as id, u.fullname as residentName, 
        COALESCE(b.room_id, 'N/A') as room,
        p.amount, p.payment_month as month, p.payment_date as date, 
        p.payment_method as method, p.status
        FROM payments p
        JOIN users u ON p.user_id = u.id
        LEFT JOIN bookings b ON u.id = b.user_id AND b.booking_status = 'confirmed'
        ORDER BY p.payment_date DESC";

$result = mysqli_query($conn, $sql);
$payments = [];
while($row = mysqli_fetch_assoc($result)){
    $payments[] = $row;
}

echo json_encode($payments);
mysqli_close($conn);
?>