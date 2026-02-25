<?php
// Backend/admin/get_recent_activities.php - FIXED VERSION
include "../db.php";
session_start();
header('Content-Type: application/json');

if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true){
    echo json_encode(['error' => 'unauthorized']);
    exit;
}

$activities = [];

// Recent payments (from user side)
$sql = "SELECT 
            'payment' as type, 
            CONCAT('Payment received from ', u.fullname) as description,
            CONCAT('₹', p.amount) as amount_info,
            CONCAT('Room ', r.room_number) as room_info,
            p.payment_date as raw_time,
            DATE_FORMAT(p.payment_date, '%Y-%m-%d %H:%i:%s') as time
        FROM payments p
        JOIN users u ON p.user_id = u.id
        LEFT JOIN bookings b ON u.id = b.user_id AND b.booking_status = 'confirmed'
        LEFT JOIN rooms r ON b.room_id = r.room_id
        ORDER BY p.payment_date DESC LIMIT 5";

$result = mysqli_query($conn, $sql);
while($row = mysqli_fetch_assoc($result)){
    $row['time_ago'] = time_elapsed($row['raw_time']);
    $activities[] = $row;
}

// Recent check-ins
$sql = "SELECT 
            'checkin' as type, 
            CONCAT('New resident ', u.fullname, ' checked in') as description,
            CONCAT('Room ', r.room_number) as room_info,
            b.booking_date as raw_time,
            DATE_FORMAT(b.booking_date, '%Y-%m-%d %H:%i:%s') as time
        FROM bookings b
        JOIN users u ON b.user_id = u.id
        LEFT JOIN rooms r ON b.room_id = r.room_id
        WHERE b.booking_status = 'confirmed'
        ORDER BY b.booking_date DESC LIMIT 5";

$result = mysqli_query($conn, $sql);
while($row = mysqli_fetch_assoc($result)){
    $row['time_ago'] = time_elapsed($row['raw_time']);
    $activities[] = $row;
}

// Sort by time
usort($activities, function($a, $b) {
    return strtotime($b['raw_time']) - strtotime($a['raw_time']);
});

echo json_encode(array_slice($activities, 0, 5));

function time_elapsed($datetime) {
    if(!$datetime) return 'Just now';
    $time = strtotime($datetime);
    $now = time();
    $diff = $now - $time;
    
    if($diff < 60) return 'Just now';
    if($diff < 3600) return floor($diff/60) . ' minutes ago';
    if($diff < 86400) return floor($diff/3600) . ' hours ago';
    if($diff < 604800) return floor($diff/86400) . ' days ago';
    return date('d M Y', $time);
}

mysqli_close($conn);
?>