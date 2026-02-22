<?php
// Backend/db.php
$host = "localhost";
$port = 3307; // Your port from earlier
$user = "root";
$pass = ""; // Keep empty for XAMPP/WAMP
$db = "pgcore";

// Create connection with port
$conn = mysqli_connect($host, $user, $pass, $db, $port);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set charset
mysqli_set_charset($conn, "utf8");

// No output here - this file is included by others
?>