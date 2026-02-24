<?php
// Backend/admin/get_recent_activities.php
include "../db.php";
session_start();
header('Content-Type: application/json');

if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true){
    echo json_encode(['error' => 'unauthorized']);
    exit;
}

$activities = [];

// Recent check-ins (bookings)
$sql = "SELECT 'checkin' as type, CONCAT('New resident ', u.fullname, ' checked in') as description,
        DATE_FORMAT(b.booking_date, '%Y-%m-%d %H:%i') as time
        FROM bookings b
        JOIN users u ON b.user_id = u.id
        WHERE b.booking_status = 'confirmed'
        ORDER BY b.booking_date DESC LIMIT 3";
$result = mysqli_query($conn, $sql);
while($row = mysqli_fetch_assoc($result)){
    $row['time'] = time_elapsed($row['time']);
    $activities[] = $row;
}

// Recent payments
$sql = "SELECT 'payment' as type, CONCAT('Payment received from ', u.fullname, ' - ₹', p.amount) as description,
        DATE_FORMAT(p.payment_date, '%Y-%m-%d %H:%i') as time
        FROM payments p
        JOIN users u ON p.user_id = u.id
        ORDER BY p.payment_date DESC LIMIT 3";
$result = mysqli_query($conn, $sql);
while($row = mysqli_fetch_assoc($result)){
    $row['time'] = time_elapsed($row['time']);
    $activities[] = $row;
}

// Recent complaints
$sql = "SELECT 'complaint' as type, CONCAT('New complaint from ', u.fullname) as description,
        DATE_FORMAT(c.complaint_date, '%Y-%m-%d %H:%i') as time
        FROM complaints c
        JOIN users u ON c.user_id = u.id
        ORDER BY c.complaint_date DESC LIMIT 3";
$result = mysqli_query($conn, $sql);
while($row = mysqli_fetch_assoc($result)){
    $row['time'] = time_elapsed($row['time']);
    $activities[] = $row;
}

// Recent visitors
$sql = "SELECT 'visitor' as type, CONCAT('Visitor ', v.visitor_name, ' for ', u.fullname) as description,
        CONCAT(v.visit_date, ' ', v.in_time) as time
        FROM visitor_log v
        JOIN users u ON v.user_id = u.id
        ORDER BY v.visit_date DESC, v.in_time DESC LIMIT 3";
$result = mysqli_query($conn, $sql);
while($row = mysqli_fetch_assoc($result)){
    $row['time'] = time_elapsed($row['time']);
    $activities[] = $row;
}

// Sort by time (most recent first)
usort($activities, function($a, $b) {
    return strtotime($b['time']) - strtotime($a['time']);
});

echo json_encode(array_slice($activities, 0, 5));

function time_elapsed($datetime) {
    $time = strtotime($datetime);
    $now = time();
    $diff = $now - $time;
    
    if($diff < 60) return 'Just now';
    if($diff < 3600) return floor($diff/60) . ' minutes ago';
    if($diff < 86400) return floor($diff/3600) . ' hours ago';
    return floor($diff/86400) . ' days ago';
}

mysqli_close($conn);
?>