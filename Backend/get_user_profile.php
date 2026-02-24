<?php
// Backend/get_user_profile.php
include "db.php";
session_start();
header('Content-Type: application/json');

if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true){
    echo json_encode(['error' => 'not_logged_in']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Get user details with their booking information
$sql = "SELECT 
            u.id,
            u.fullname,
            u.email,
            u.phone,
            b.booking_id,
            b.booking_status,
            b.booking_date as joining_date,
            b.move_in_date,
            b.booking_amount,
            r.room_number,
            r.room_type,
            r.rent
        FROM users u
        LEFT JOIN bookings b ON u.id = b.user_id 
        LEFT JOIN rooms r ON b.room_id = r.room_id
        WHERE u.id = ? 
        ORDER BY b.booking_date DESC 
        LIMIT 1";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user_data = mysqli_fetch_assoc($result);

if($user_data){
    $response = [
        'success' => true,
        'id' => $user_data['id'],
        'fullname' => $user_data['fullname'],
        'email' => $user_data['email'],
        'phone' => $user_data['phone'],
        'joining_date' => $user_data['joining_date'] ?? 'Not available',
        'has_booking' => !empty($user_data['booking_id']),
        'booking_status' => $user_data['booking_status'] ?? 'none',
        'room_number' => $user_data['room_number'] ?? 'Not assigned',
        'room_type' => $user_data['room_type'] ?? 'Not assigned',
        'room_rent' => $user_data['rent'] ?? 0, // This gets the actual room rent!
        'move_in_date' => $user_data['move_in_date'] ?? 'Not available'
    ];
    echo json_encode($response);
} else {
    echo json_encode(['error' => 'user_not_found']);
}

mysqli_close($conn);
?>