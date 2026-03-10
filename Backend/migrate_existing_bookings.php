<?php
// Backend/migrate_existing_bookings.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "db.php";

echo "<h1>🔄 PGCore Migration Tool</h1>";
echo "<p>Migrating existing bookings to room_occupants table...</p>";

// Check connection
if(!$conn){
    die("<p style='color:red;'>❌ Database connection failed: " . mysqli_connect_error() . "</p>");
}

echo "<p style='color:green;'>✅ Database connected successfully!</p>";

// Check if room_occupants table exists
$check_table = mysqli_query($conn, "SHOW TABLES LIKE 'room_occupants'");
if(mysqli_num_rows($check_table) == 0){
    echo "<p style='color:red;'>❌ Table 'room_occupants' does not exist!</p>";
    echo "<p>Please run this SQL first:</p>";
    echo "<pre style='background:#f4f4f4; padding:10px; border:1px solid #ccc;'>
CREATE TABLE room_occupants (
    id INT PRIMARY KEY AUTO_INCREMENT,
    room_id INT NOT NULL,
    user_id INT NOT NULL,
    bed_number INT NOT NULL,
    check_in_date DATE NOT NULL,
    status VARCHAR(20) DEFAULT 'active',
    FOREIGN KEY (room_id) REFERENCES rooms(room_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_active_bed (room_id, bed_number, status)
);
</pre>";
    exit;
}

echo "<p style='color:green;'>✅ Table 'room_occupants' exists!</p>";

// Get all confirmed bookings
$sql = "SELECT b.*, r.room_type, r.room_number 
        FROM bookings b 
        JOIN rooms r ON b.room_id = r.room_id 
        WHERE b.booking_status = 'confirmed'";

$result = mysqli_query($conn, $sql);

if(!$result){
    die("<p style='color:red;'>❌ Error fetching bookings: " . mysqli_error($conn) . "</p>");
}

$capacity_map = [
    'Single Sharing' => 1,
    'Double Sharing' => 2,
    'Triple Sharing' => 3,
    'Four Sharing' => 4
];

$migrated = 0;
$errors = 0;
$skipped = 0;

echo "<h2>Found " . mysqli_num_rows($result) . " confirmed bookings</h2>";
echo "<hr>";

while($booking = mysqli_fetch_assoc($result)){
    $room_id = $booking['room_id'];
    $user_id = $booking['user_id'];
    $move_in_date = $booking['move_in_date'] ?: $booking['booking_date'];
    $room_type = $booking['room_type'];
    $room_number = $booking['room_number'];
    $capacity = $capacity_map[$room_type] ?? 1;
    
    echo "<p><strong>Processing:</strong> Room {$room_number} ({$room_type}) - User ID: {$user_id}</p>";
    
    // Check if user already has an active occupancy
    $check_sql = "SELECT id FROM room_occupants WHERE user_id = ? AND status = 'active'";
    $check_stmt = mysqli_prepare($conn, $check_sql);
    
    if($check_stmt){
        mysqli_stmt_bind_param($check_stmt, "i", $user_id);
        mysqli_stmt_execute($check_stmt);
        mysqli_stmt_store_result($check_stmt);
        
        if(mysqli_stmt_num_rows($check_stmt) > 0){
            echo "<p style='color:orange;'>⚠️ User already has active occupancy - skipping</p>";
            $skipped++;
            continue;
        }
    }
    
    // Find available bed for this room
    $occupied_sql = "SELECT bed_number FROM room_occupants WHERE room_id = ? AND status = 'active'";
    $occupied_stmt = mysqli_prepare($conn, $occupied_sql);
    
    if($occupied_stmt){
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
        $insert = "INSERT INTO room_occupants (room_id, user_id, bed_number, check_in_date, status) 
                   VALUES (?, ?, ?, ?, 'active')";
        
        $insert_stmt = mysqli_prepare($conn, $insert);
        if($insert_stmt){
            mysqli_stmt_bind_param($insert_stmt, "iiis", $room_id, $user_id, $bed_number, $move_in_date);
            
            if(mysqli_stmt_execute($insert_stmt)){
                $migrated++;
                echo "<p style='color:green;'>✅ Migrated to Bed #{$bed_number}</p>";
                
                // Update room status if needed
                if($bed_number == $capacity){
                    mysqli_query($conn, "UPDATE rooms SET status = 'occupied' WHERE room_id = {$room_id}");
                }
            } else {
                $errors++;
                echo "<p style='color:red;'>❌ Error: " . mysqli_error($conn) . "</p>";
            }
        } else {
            $errors++;
            echo "<p style='color:red;'>❌ Prepare error for insert</p>";
        }
    } else {
        $errors++;
        echo "<p style='color:red;'>❌ Prepare error for occupied check</p>";
    }
    
    echo "<hr>";
}

echo "<h2>📊 Migration Summary:</h2>";
echo "<ul>";
echo "<li style='color:green;'>✅ Successfully migrated: {$migrated}</li>";
echo "<li style='color:orange;'>⚠️ Skipped (already exists): {$skipped}</li>";
echo "<li style='color:red;'>❌ Errors: {$errors}</li>";
echo "</ul>";

if($migrated > 0 || $skipped > 0){
    echo "<h3 style='color:green;'>✅ Migration complete! Check your Rooms Management page.</h3>";
    echo "<p><a href='http://localhost/Girls/PGCore/admin2.html' target='_blank'>Go to Admin Panel</a></p>";
}

mysqli_close($conn);
?>