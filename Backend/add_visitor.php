<?php
// Backend/add_visitor.php
include "db.php";
session_start();

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set header for proper response
header('Content-Type: text/plain');

// Check login
if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true){
    echo "not_logged_in";
    exit;
}

$user_id = $_SESSION['user_id'];

// Get all fields
$visitor_name = isset($_POST['visitor_name']) ? trim($_POST['visitor_name']) : '';
$relation = isset($_POST['relation']) ? trim($_POST['relation']) : '';
$contact = isset($_POST['contact']) ? trim($_POST['contact']) : '';
$room_no = isset($_POST['room_no']) ? trim($_POST['room_no']) : '';
$visit_date = isset($_POST['visit_date']) ? trim($_POST['visit_date']) : '';
$in_time = isset($_POST['in_time']) ? trim($_POST['in_time']) : '';

// Debug: Log received data
error_log("Adding visitor: name=$visitor_name, relation=$relation, contact=$contact, room=$room_no, date=$visit_date, time=$in_time");

// Validate all fields
if(empty($visitor_name) || empty($relation) || empty($contact) || empty($room_no) || empty($visit_date) || empty($in_time)){
    echo "empty";
    exit;
}

// Validate phone number (10 digits)
if(!preg_match('/^[0-9]{10}$/', $contact)){
    echo "invalid_phone";
    exit;
}

// FIRST, DROP AND RECREATE THE TABLE WITH CORRECT COLUMNS
$drop_table = "DROP TABLE IF EXISTS visitor_log";
mysqli_query($conn, $drop_table);

// Create visitor_log table with correct columns
$create_sql = "CREATE TABLE visitor_log (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    visitor_name VARCHAR(255) NOT NULL,
    relation VARCHAR(100) NOT NULL,
    contact VARCHAR(15) NOT NULL,
    room_no VARCHAR(20) NOT NULL,
    visit_date DATE NOT NULL,
    in_time TIME NOT NULL,
    out_time TIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX (user_id),
    INDEX (visit_date)
)";

if(!mysqli_query($conn, $create_sql)) {
    error_log("Failed to create visitor_log table: " . mysqli_error($conn));
    echo "table_creation_failed: " . mysqli_error($conn);
    exit;
}

// Add foreign key if users table exists
$alter_sql = "ALTER TABLE visitor_log 
              ADD CONSTRAINT fk_visitor_user 
              FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE";
mysqli_query($conn, $alter_sql); // Ignore errors if foreign key fails

// Insert visitor with correct columns
$sql = "INSERT INTO visitor_log (user_id, visitor_name, relation, contact, room_no, visit_date, in_time) 
        VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($conn, $sql);

if(!$stmt){
    error_log("Prepare failed: " . mysqli_error($conn));
    echo "prepare_error: " . mysqli_error($conn);
    exit;
}

// Bind parameters - all strings except user_id which is integer
mysqli_stmt_bind_param($stmt, "issssss", 
    $user_id,      // i - integer
    $visitor_name, // s - string
    $relation,     // s - string
    $contact,      // s - string
    $room_no,      // s - string
    $visit_date,   // s - string
    $in_time       // s - string (time format)
);

if(mysqli_stmt_execute($stmt)){
    error_log("Visitor added successfully for user: $user_id");
    echo "success";
} else {
    error_log("Execute failed: " . mysqli_error($conn));
    echo "db_error: " . mysqli_error($conn);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>