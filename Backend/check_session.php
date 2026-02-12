<?php
// Backend/check_session.php
session_start();

if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true){
    echo "logged_in";
} else {
    echo "not_logged_in";
}
?>