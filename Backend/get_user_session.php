<?php
// Backend/get_user_session.php
session_start();
header('Content-Type: application/json');

if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true){
    echo json_encode([
        'logged_in' => true,
        'user_id' => $_SESSION['user_id'] ?? null,
        'name' => $_SESSION['user_name'] ?? '',
        'email' => $_SESSION['user_email'] ?? '',
        'phone' => $_SESSION['user_phone'] ?? ''
    ]);
} else {
    echo json_encode(['logged_in' => false]);
}
?>