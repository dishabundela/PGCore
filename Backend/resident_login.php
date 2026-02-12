<?php
// Backend/resident_login.php
include "db.php";
session_start();

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if(empty($email) || empty($password)){
    echo "empty";
    exit;
}

$sql = "SELECT id, fullname, email, phone, password FROM users WHERE email = ?";
$stmt = mysqli_prepare($conn, $sql);

if(!$stmt){
    echo "db_error";
    exit;
}

mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if($row = mysqli_fetch_assoc($result)){
    if(password_verify($password, $row['password'])){
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['user_name'] = $row['fullname'];
        $_SESSION['user_email'] = $row['email'];
        $_SESSION['user_phone'] = $row['phone'];
        $_SESSION['logged_in'] = true;
        
        echo "success";
    } else {
        echo "wrong";
    }
} else {
    echo "not_found";
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>