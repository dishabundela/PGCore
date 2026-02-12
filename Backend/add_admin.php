<?php
include "db.php";

$username = "admin";
$password = "admin123";
$hash = password_hash($password, PASSWORD_DEFAULT);

$sql = "INSERT INTO admin (username, password) VALUES ('$username', '$hash')";

if (mysqli_query($conn, $sql)) {
    echo "Admin added successfully! Username: admin, Password: admin123";
} else {
    echo "Error adding admin: " . mysqli_error($conn);
}
?>