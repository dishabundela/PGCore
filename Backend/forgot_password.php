<?php
// Backend/forgot_password.php
include "db.php";

$email = $_POST['email'] ?? '';
$new_password = $_POST['password'] ?? '';

if(empty($email) || empty($new_password)){
    echo "empty";
    exit;
}

$sql = "SELECT id FROM users WHERE email = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if($row = mysqli_fetch_assoc($result)){
    $user_id = $row['id'];
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    
    $update_sql = "UPDATE users SET password = ? WHERE id = ?";
    $update_stmt = mysqli_prepare($conn, $update_sql);
    mysqli_stmt_bind_param($update_stmt, "si", $hashed_password, $user_id);
    
    if(mysqli_stmt_execute($update_stmt)){
        echo "success";
    } else {
        echo "error";
    }
    
    mysqli_stmt_close($update_stmt);
} else {
    echo "not_found";
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>