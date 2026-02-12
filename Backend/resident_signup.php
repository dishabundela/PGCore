<?php
// Backend/resident_signup.php
include "db.php";

$name  = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';
$raw   = $_POST['password'] ?? '';

if(empty($name) || empty($email) || empty($phone) || empty($raw)){
    echo "empty";
    exit;
}

// Check if email exists
$check_sql = "SELECT id FROM users WHERE email = ?";
$check_stmt = mysqli_prepare($conn, $check_sql);
mysqli_stmt_bind_param($check_stmt, "s", $email);
mysqli_stmt_execute($check_stmt);
mysqli_stmt_store_result($check_stmt);

if(mysqli_stmt_num_rows($check_stmt) > 0){
    echo "email_exists";
    exit;
}

// Hash password and insert
$hashed_password = password_hash($raw, PASSWORD_DEFAULT);
$sql = "INSERT INTO users (fullname, email, phone, password) VALUES (?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $sql);

if(!$stmt){
    echo "prepare_error";
    exit;
}

mysqli_stmt_bind_param($stmt, "ssss", $name, $email, $phone, $hashed_password);

if(mysqli_stmt_execute($stmt)){
    echo "success";
} else {
    echo "error";
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>