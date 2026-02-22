<?php
// Backend/add_visitor.php
include "db.php";
session_start();

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check login
if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true){
    echo "not_logged_in";
    exit;
}

$user_id = $_SESSION['user_id'];

// Get all fields
$visitor_name = $_POST['visitor_name'] ?? '';
$relation = $_POST['relation'] ?? '';
$contact = $_POST['contact'] ?? '';
$room_no = $_POST['room_no'] ?? '';
$visit_date = $_POST['visit_date'] ?? '';
$in_time = $_POST['in_time'] ?? '';

// Debug: Log received data
error_log("Adding visitor: name=$visitor_name, relation=$relation, contact=$contact, room=$room_no, date=$visit_date, time=$in_time");

// Validate all fields
if(empty($visitor_name) || empty($relation) || empty($contact) || empty($room_no) || empty($visit_date) || empty($in_time)){
    echo "empty";
    exit;
}

// Insert with correct types
$sql = "INSERT INTO visitor_log (user_id, visitor_name, relation, contact, room_no, visit_date, in_time) 
        VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($conn, $sql);

if(!$stmt){
    echo "prepare_error: " . mysqli_error($conn);
    exit;
}

// Bind parameters with correct types: i = integer, s = string
mysqli_stmt_bind_param($stmt, "issssss", 
    $user_id,      // i - integer
    $visitor_name, // s - string
    $relation,     // s - string
    $contact,      // s - string (not int!)
    $room_no,      // s - string
    $visit_date,   // s - string
    $in_time       // s - string (time format)
);

if(mysqli_stmt_execute($stmt)){
    echo "success";
} else {
    echo "error: " . mysqli_error($conn);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>