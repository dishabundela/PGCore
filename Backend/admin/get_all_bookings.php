<?php
// Backend/admin/get_all_bookings.php - FIXED VERSION
include "../db.php";
session_start();
header('Content-Type: application/json');

if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true){
    echo json_encode(['error' => 'unauthorized']);
    exit;
}

// Simple query first to test - Get ALL bookings regardless of status
$sql = "SELECT 
            b.booking_id as id, 
            u.fullname as name, 
            u.phone as mobile, 
            u.email,
            r.room_number as roomPreference, 
            r.room_type as roomType,
            r.rent as price,
            b.move_in_date as checkInDate,
            b.booking_date as requestedOn,
            b.booking_status as status
        FROM bookings b
        JOIN users u ON b.user_id = u.id
        LEFT JOIN rooms r ON b.room_id = r.room_id
        ORDER BY 
            CASE b.booking_status
                WHEN 'pending' THEN 1
                WHEN 'confirmed' THEN 2
                ELSE 3
            END,
            b.booking_date DESC";

$result = mysqli_query($conn, $sql);

if(!$result){
    echo json_encode(['error' => 'Query failed: ' . mysqli_error($conn)]);
    exit;
}

$bookings = [];
while($row = mysqli_fetch_assoc($result)){
    // Format dates for display
    if($row['checkInDate']){
        $row['checkInDate'] = date('d-m-Y', strtotime($row['checkInDate']));
    }
    if($row['requestedOn']){
        $row['requestedOn'] = date('d-m-Y', strtotime($row['requestedOn']));
    }
    $bookings[] = $row;
}

// Debug - log how many bookings found
error_log("Found " . count($bookings) . " bookings in database");

echo json_encode($bookings);
mysqli_close($conn);
?>