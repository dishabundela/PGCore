<?php
include "db.php";

if($_SERVER['REQUEST_METHOD'] === 'GET'){
    $token = $_GET['token'] ?? '';
    
    if(empty($token)){
        die("Invalid token");
    }
    
    $sql = "SELECT id FROM users WHERE reset_token = ? AND token_expiry > NOW()";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $token);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if(mysqli_num_rows($result) > 0){
        echo '
        <!DOCTYPE html>
        <html>
        <head>
            <title>Reset Password</title>
            <style>
                body { font-family: Arial, sans-serif; max-width: 400px; margin: 50px auto; padding: 20px; }
                input { width: 100%; padding: 10px; margin: 10px 0; }
                button { background: #2c5364; color: white; padding: 10px; border: none; cursor: pointer; width: 100%; }
            </style>
        </head>
        <body>
            <h2>Reset Your Password</h2>
            <form method="POST">
                <input type="hidden" name="token" value="' . htmlspecialchars($token) . '">
                <input type="password" name="new_password" placeholder="New Password" required>
                <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                <button type="submit">Reset Password</button>
            </form>
        </body>
        </html>';
    } else {
        echo "Invalid or expired token";
    }
    
} elseif($_SERVER['REQUEST_METHOD'] === 'POST'){
    $token = $_POST['token'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if($new_password !== $confirm_password){
        echo "Passwords do not match";
        exit;
    }
    
    if(strlen($new_password) < 6){
        echo "Password must be at least 6 characters long";
        exit;
    }
    
    $sql = "SELECT id FROM users WHERE reset_token = ? AND token_expiry > NOW()";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $token);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if($row = mysqli_fetch_assoc($result)){
        $user_id = $row['id'];
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        $update_sql = "UPDATE users SET password = ?, reset_token = NULL, token_expiry = NULL WHERE id = ?";
        $update_stmt = mysqli_prepare($conn, $update_sql);
        mysqli_stmt_bind_param($update_stmt, "si", $hashed_password, $user_id);
        
        if(mysqli_stmt_execute($update_stmt)){
            echo "Password reset successful! You can now login with your new password.";
        } else {
            echo "Error resetting password";
        }
        
        mysqli_stmt_close($update_stmt);
    } else {
        echo "Invalid or expired token";
    }
    
    mysqli_stmt_close($stmt);
}

mysqli_close($conn);
?>