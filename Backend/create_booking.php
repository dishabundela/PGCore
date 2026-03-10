<?php
// Backend/create_booking.php
include "db.php";
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true){
    echo "not_logged_in";
    exit;
}

$user_id = $_SESSION['user_id'];
$booking_date = $_POST['booking_date'] ?? '';
$move_in_date = $_POST['move_in_date'] ?? '';
$booking_amount = $_POST['price'] ?? '';
$room_id = $_POST['room_id'] ?? '';

if(empty($booking_date) || empty($move_in_date) || empty($booking_amount) || empty($room_id)){
    echo "empty";
    exit;
}

// Get room details and capacity
$capacity_map = [
    'Single Sharing' => 1,
    'Double Sharing' => 2,
    'Triple Sharing' => 3,
    'Four Sharing' => 4
];

$room_info = mysqli_query($conn, "SELECT room_type FROM rooms WHERE room_id = $room_id");
$room_data = mysqli_fetch_assoc($room_info);
$room_type = $room_data['room_type'];
$capacity = $capacity_map[$room_type] ?? 1;

// Count current active occupants
$count_sql = "SELECT COUNT(*) as total FROM room_occupants WHERE room_id = ? AND status = 'active'";
$count_stmt = mysqli_prepare($conn, $count_sql);
mysqli_stmt_bind_param($count_stmt, "i", $room_id);
mysqli_stmt_execute($count_stmt);
$count_result = mysqli_stmt_get_result($count_stmt);
$count_row = mysqli_fetch_assoc($count_result);
$current_occupants = $count_row['total'];

if($current_occupants >= $capacity){
    echo "room_full";
    exit;
}

// Find the next available bed number
$bed_sql = "SELECT bed_number FROM room_occupants WHERE room_id = ? AND status = 'active' ORDER BY bed_number";
$bed_stmt = mysqli_prepare($conn, $bed_sql);
mysqli_stmt_bind_param($bed_stmt, "i", $room_id);
mysqli_stmt_execute($bed_stmt);
$bed_result = mysqli_stmt_get_result($bed_stmt);

$occupied_beds = [];
while($bed_row = mysqli_fetch_assoc($bed_result)){
    $occupied_beds[] = $bed_row['bed_number'];
}

$available_bed = 1;
for($i = 1; $i <= $capacity; $i++){
    if(!in_array($i, $occupied_beds)){
        $available_bed = $i;
        break;
    }
}

// Check if user already has a pending/confirmed booking
$check_user_booking = mysqli_prepare($conn, "SELECT booking_id FROM bookings WHERE user_id = ? AND booking_status IN ('pending', 'confirmed')");
mysqli_stmt_bind_param($check_user_booking, "i", $user_id);
mysqli_stmt_execute($check_user_booking);
$user_booking_result = mysqli_stmt_get_result($check_user_booking);

if(mysqli_num_rows($user_booking_result) > 0){
    echo "already_booked";
    exit;
}
mysqli_stmt_close($check_user_booking);

// Insert booking with bed_number in booking details
$sql = "INSERT INTO bookings (user_id, room_id, booking_date, move_in_date, booking_amount, payment_status, booking_status) 
        VALUES (?, ?, ?, ?, ?, 'pending', 'pending')";

$stmt = mysqli_prepare($conn, $sql);

if(!$stmt){
    echo "error";
    exit;
}

mysqli_stmt_bind_param($stmt, "iissd", $user_id, $room_id, $booking_date, $move_in_date, $booking_amount);

if(mysqli_stmt_execute($stmt)){
    $booking_id = mysqli_insert_id($conn);
    
    // Store bed number in booking details (you can add a column or store in notes)
    // For now, we'll update room status but not mark as fully occupied
    if($current_occupants + 1 == $capacity){
        // Room will be fully occupied after this booking
        $update_room = mysqli_prepare($conn, "UPDATE rooms SET status = 'occupied' WHERE room_id = ?");
        mysqli_stmt_bind_param($update_room, "i", $room_id);
        mysqli_stmt_execute($update_room);
        mysqli_stmt_close($update_room);
    }
    
    echo "success";
} else {
    echo "error";
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>