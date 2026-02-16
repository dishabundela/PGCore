<?php
$conn = mysqli_connect("localhost", "root", "", "pgcore");

if(!$conn){
    die("Database connection failed: " . mysqli_connect_error());
}
?>