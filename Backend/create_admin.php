<?php
// Backend/create_admin.php
include "db.php";

// Admin credentials - CHANGE THESE!
$username = "admin";
$password = "Admin@123"; // Change this to your preferred password
$hash = password_hash($password, PASSWORD_DEFAULT);

// Check if admin already exists
$check = mysqli_query($conn, "SELECT * FROM admin WHERE username = '$username'");
if(mysqli_num_rows($check) > 0){
    echo "Admin already exists!<br>";
    echo "Try logging in with existing credentials.";
} else {
    $sql = "INSERT INTO admin (username, password) VALUES ('$username', '$hash')";
    if(mysqli_query($conn, $sql)){
        echo "✅ Admin created successfully!<br>";
        echo "Username: <strong>$username</strong><br>";
        echo "Password: <strong>$password</strong><br>";
        echo "<a href='admin/login.html'>Go to Admin Login</a>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>