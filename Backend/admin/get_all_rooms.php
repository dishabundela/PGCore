<?php
// Backend/admin/get_all_rooms.php - FIXED (removed amenities)
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "../db.php";
session_start();
header('Content-Type: application/json');

// Check database connection
if (!$conn) {
    echo json_encode(['error' => 'Database connection failed: ' . mysqli_connect_error()]);
    exit;
}

if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true){
    echo json_encode(['error' => 'unauthorized']);
    exit;
}

// Check if room_occupants table exists
$table_check = mysqli_query($conn, "SHOW TABLES LIKE 'room_occupants'");
if(mysqli_num_rows($table_check) == 0){
    echo json_encode(['error' => 'room_occupants table does not exist']);
    exit;
}

// Get all rooms with their occupants - REMOVED amenities column
$sql = "SELECT 
            r.room_id, 
            r.room_number as number, 
            r.room_type as type, 
            r.capacity, 
            r.rent, 
            r.status,
            GROUP_CONCAT(
                CONCAT(u.fullname, ' (Bed ', ro.bed_number, ')') 
                SEPARATOR '||'
            ) as occupants_list,
            COUNT(ro.id) as occupants_count
        FROM rooms r
        LEFT JOIN room_occupants ro ON r.room_id = ro.room_id AND ro.status = 'active'
        LEFT JOIN users u ON ro.user_id = u.id
        GROUP BY r.room_id
        ORDER BY r.room_number";

$result = mysqli_query($conn, $sql);

if(!$result){
    echo json_encode(['error' => 'Query failed: ' . mysqli_error($conn)]);
    exit;
}

$rooms = [];
while($row = mysqli_fetch_assoc($result)){
    // Parse occupants list
    $occupants = [];
    if(!empty($row['occupants_list'])){
        $occupant_strings = explode('||', $row['occupants_list']);
        foreach($occupant_strings as $occ){
            $occupants[] = $occ;
        }
    }
    $row['occupants'] = $occupants;
    $row['occupants_count'] = (int)$row['occupants_count'];
    $row['vacant_beds'] = $row['capacity'] - $row['occupants_count'];
    $row['amenities'] = []; // Empty amenities array
    
    unset($row['occupants_list']);
    $rooms[] = $row;
}

echo json_encode($rooms);
mysqli_close($conn);
?>