<?php
// Backend/admin_login.php
include "db.php";
session_start();

// Add debug line
error_log("Admin login attempt: " . $_POST['username']);

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

if(empty($username) || empty($password)){
    echo "empty";
    exit;
}

$sql = "SELECT * FROM admin WHERE username=?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if($row = mysqli_fetch_assoc($result)){
    if(password_verify($password, $row['password'])){
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = $row['id'];
        $_SESSION['admin_username'] = $row['username'];
        echo "success";
    } else {
        echo "wrong";
    }
} else {
    echo "not_found";
}
?>