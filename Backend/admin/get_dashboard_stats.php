<?php
// Backend/admin/get_dashboard_stats.php - FINAL WORKING VERSION
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

// Pending bookings
$result = mysqli_query($conn, "SELECT COUNT(*) as count FROM bookings WHERE booking_status = 'pending'");
$row = mysqli_fetch_assoc($result);
$stats['pendingBookings'] = $row['count'];

// Total Collected
$collected_result = mysqli_query($conn, "SELECT COALESCE(SUM(amount), 0) as total FROM payments");
$collected_row = mysqli_fetch_assoc($collected_result);
$stats['totalCollected'] = '₹' . number_format($collected_row['total']);

// This Month Collection
$stats['thisMonthCollection'] = $stats['monthlyRevenue'];

// ===== FIXED: Pending Payments =====
$current_month = date('F Y'); // "February 2026"

$pending_sql = "SELECT COALESCE(SUM(r.rent), 0) as pending_total
                FROM users u
                INNER JOIN bookings b ON u.id = b.user_id AND b.booking_status = 'confirmed'
                INNER JOIN rooms r ON b.room_id = r.room_id
                WHERE u.id NOT IN (
                    SELECT user_id FROM payments WHERE payment_month = '$current_month'
                )";

$pending_result = mysqli_query($conn, $pending_sql);
$pending_row = mysqli_fetch_assoc($pending_result);
$pending_amount = $pending_row['pending_total'];

$stats['totalPending'] = '₹' . number_format($pending_amount);

// Debug (remove later)
$stats['debug_pending_users'] = "pinky + disha = ₹10,000";

echo json_encode($stats);
mysqli_close($conn);
?>