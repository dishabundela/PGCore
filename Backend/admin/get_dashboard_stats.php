<?php
// Backend/admin/get_dashboard_stats.php
include "../db.php";
session_start();
header('Content-Type: application/json');

if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true){
    echo json_encode(['error' => 'unauthorized']);
    exit;
}

$stats = [];

// Total users
$result = mysqli_query($conn, "SELECT COUNT(*) as count FROM users");
$row = mysqli_fetch_assoc($result);
$stats['totalResidents'] = $row['count'];

// Available rooms
$result = mysqli_query($conn, "SELECT COUNT(*) as count FROM rooms WHERE status = 'available'");
$row = mysqli_fetch_assoc($result);
$stats['availableRooms'] = $row['count'];

// Monthly revenue
$result = mysqli_query($conn, "SELECT COALESCE(SUM(amount), 0) as total FROM payments WHERE MONTH(payment_date) = MONTH(CURRENT_DATE()) AND YEAR(payment_date) = YEAR(CURRENT_DATE())");
$row = mysqli_fetch_assoc($result);
$stats['monthlyRevenue'] = '₹' . number_format($row['total']);

// Pending complaints
$result = mysqli_query($conn, "SELECT COUNT(*) as count FROM complaints WHERE status = 'pending'");
$row = mysqli_fetch_assoc($result);
$stats['pendingComplaints'] = $row['count'];

// Today's visitors
$result = mysqli_query($conn, "SELECT COUNT(*) as count FROM visitor_log WHERE visit_date = CURRENT_DATE()");
$row = mysqli_fetch_assoc($result);
$stats['todayVisitors'] = $row['count'];

// Active emergencies
$result = mysqli_query($conn, "SELECT COUNT(*) as count FROM emergencies WHERE status = 'active'");
$row = mysqli_fetch_assoc($result);
$stats['activeEmergencies'] = $row['count'];

// Pending bookings
$result = mysqli_query($conn, "SELECT COUNT(*) as count FROM bookings WHERE booking_status = 'pending'");
$row = mysqli_fetch_assoc($result);
$stats['pendingBookings'] = $row['count'];

echo json_encode($stats);
mysqli_close($conn);
?>