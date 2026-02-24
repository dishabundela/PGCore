<?php
// Backend/create_complaint.php
include "db.php";
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set header for proper response
header('Content-Type: text/plain');

// Check if user is logged in
if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true){
    echo "not_logged_in";
    exit;
}

$user_id = $_SESSION['user_id'];

// Get all fields
$title = isset($_POST['title']) ? trim($_POST['title']) : '';
$category = isset($_POST['category']) ? trim($_POST['category']) : '';
$room_no = isset($_POST['room_no']) ? trim($_POST['room_no']) : '';
$description = isset($_POST['description']) ? trim($_POST['description']) : '';

// Debug log
error_log("Complaint received - User: $user_id, Title: $title, Category: $category, Room: $room_no");

// Validate all fields
if(empty($title) || empty($category) || empty($room_no) || empty($description)){
    echo "empty";
    exit;
}

$complaint_date = date('Y-m-d');

// FIRST, DROP AND RECREATE THE TABLE WITH CORRECT COLUMNS
$drop_table = "DROP TABLE IF EXISTS complaints";
mysqli_query($conn, $drop_table);

// Create complaints table with correct columns
$create_sql = "CREATE TABLE complaints (
    complaint_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    category VARCHAR(100) NOT NULL,
    room_no VARCHAR(20) NOT NULL,
    complaint_text TEXT NOT NULL,
    complaint_date DATE NOT NULL,
    status VARCHAR(50) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if(!mysqli_query($conn, $create_sql)) {
    error_log("Failed to create complaints table: " . mysqli_error($conn));
    echo "table_creation_failed: " . mysqli_error($conn);
    exit;
}

// Add foreign key if users table exists (optional)
$alter_sql = "ALTER TABLE complaints 
              ADD CONSTRAINT fk_complaints_user 
              FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE";
mysqli_query($conn, $alter_sql); // Ignore errors if foreign key fails

// Now insert the complaint
$sql = "INSERT INTO complaints (user_id, title, category, room_no, complaint_text, complaint_date, status) 
        VALUES (?, ?, ?, ?, ?, ?, 'pending')";

$stmt = mysqli_prepare($conn, $sql);

if(!$stmt){
    error_log("Prepare failed: " . mysqli_error($conn));
    echo "prepare_error: " . mysqli_error($conn);
    exit;
}

mysqli_stmt_bind_param($stmt, "isssss", $user_id, $title, $category, $room_no, $description, $complaint_date);

if(mysqli_stmt_execute($stmt)){
    error_log("Complaint inserted successfully for user: $user_id");
    echo "success";
} else {
    error_log("Execute failed: " . mysqli_error($conn));
    echo "db_error: " . mysqli_error($conn);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>