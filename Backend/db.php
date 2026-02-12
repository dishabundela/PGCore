<?php
// Backend/db.php
$conn = mysqli_connect("localhost", "root", "", "pgcore");

if(!$conn){
    die("Database connection failed");
}
?>