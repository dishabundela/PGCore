<?php
// Backend/admin/update_booking_status.php
include "../db.php";
session_start();
header('Content-Type: text/plain');

if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true){
    echo "unauthorized";
    exit;
}

$booking_id = $_POST['booking_id'] ?? '';
$status = $_POST['status'] ?? ''; // 'confirmed' or 'rejected'

if(empty($booking_id) || empty($status)){
    echo "empty";
    exit;
}

// Get booking details first
$get_booking = mysqli_prepare($conn, "SELECT user_id, room_id, move_in_date FROM bookings WHERE booking_id = ?");
mysqli_stmt_bind_param($get_booking, "i", $booking_id);
mysqli_stmt_execute($get_booking);
$booking_result = mysqli_stmt_get_result($get_booking);
$booking = mysqli_fetch_assoc($booking_result);
mysqli_stmt_close($get_booking);

if(!$booking){
    echo "booking_not_found";
    exit;
}

$user_id = $booking['user_id'];
$room_id = $booking['room_id'];
$move_in_date = $booking['move_in_date'];

// Update booking status
$sql = "UPDATE bookings SET booking_status = ? WHERE booking_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "si", $status, $booking_id);

if(mysqli_stmt_execute($stmt)){
    // If confirmed, add to room_occupants
    if($status == 'confirmed'){
        
        // Get room capacity to find available bed
        $capacity_map = [
            'Single Sharing' => 1,
            'Double Sharing' => 2,
            'Triple Sharing' => 3,
            'Four Sharing' => 4
        ];
        
        $room_info = mysqli_query($conn, "SELECT room_type FROM rooms WHERE room_id = $room_id");
        $room_data = mysqli_fetch_assoc($room_info);
        $capacity = $capacity_map[$room_data['room_type']] ?? 1;
        
        // Find available bed number
        $occupied_sql = "SELECT bed_number FROM room_occupants WHERE room_id = ? AND status = 'active'";
        $occupied_stmt = mysqli_prepare($conn, $occupied_sql);
        mysqli_stmt_bind_param($occupied_stmt, "i", $room_id);
        mysqli_stmt_execute($occupied_stmt);
        $occupied_result = mysqli_stmt_get_result($occupied_stmt);
        
        $occupied_beds = [];
        while($occ = mysqli_fetch_assoc($occupied_result)){
            $occupied_beds[] = $occ['bed_number'];
        }
        
        $bed_number = 1;
        for($i = 1; $i <= $capacity; $i++){
            if(!in_array($i, $occupied_beds)){
                $bed_number = $i;
                break;
            }
        }
        
        // Insert into room_occupants
        $insert_occ = mysqli_prepare($conn, "INSERT INTO room_occupants (room_id, user_id, bed_number, check_in_date, status) VALUES (?, ?, ?, ?, 'active')");
        mysqli_stmt_bind_param($insert_occ, "iiis", $room_id, $user_id, $bed_number, $move_in_date);
        mysqli_stmt_execute($insert_occ);
        mysqli_stmt_close($insert_occ);
        
        // Update room status if fully occupied
        $count_occ = mysqli_query($conn, "SELECT COUNT(*) as total FROM room_occupants WHERE room_id = $room_id AND status = 'active'");
        $count_data = mysqli_fetch_assoc($count_occ);
        
        if($count_data['total'] >= $capacity){
            mysqli_query($conn, "UPDATE rooms SET status = 'occupied' WHERE room_id = $room_id");
        } else {
            mysqli_query($conn, "UPDATE rooms SET status = 'partial' WHERE room_id = $room_id");
        }
    }
    
    echo "success";
} else {
    echo "error";
}

mysqli_close($conn);
?>