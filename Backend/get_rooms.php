<?php
// Backend/get_rooms.php
include "db.php";
header('Content-Type: application/json');

// Get room capacity mapping based on room_type
$capacity_map = [
    'Single Sharing' => 1,
    'Double Sharing' => 2,
    'Triple Sharing' => 3,
    'Four Sharing' => 4
];

// First, get all rooms with their current occupants count
$sql = "SELECT 
            r.room_id, 
            r.room_number, 
            r.room_type, 
            r.rent as price,
            r.status,
            COUNT(ro.id) as occupants_count
        FROM rooms r
        LEFT JOIN room_occupants ro ON r.room_id = ro.room_id AND ro.status = 'active'
        GROUP BY r.room_id";

$result = mysqli_query($conn, $sql);

$rooms = [];
while($row = mysqli_fetch_assoc($result)){
    // Get capacity based on room type
    $capacity = $capacity_map[$row['room_type']] ?? 1;
    $occupied = (int)$row['occupants_count'];
    $available_beds = $capacity - $occupied;
    
    // Only show rooms that have at least 1 bed available
    if($available_beds > 0){
        $row['available_beds'] = $available_beds;
        $row['capacity'] = $capacity;
        $row['occupied'] = $occupied;
        $rooms[] = $row;
    }
}

echo json_encode($rooms);
mysqli_close($conn);
?>