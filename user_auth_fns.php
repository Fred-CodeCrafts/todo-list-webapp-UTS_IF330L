<?php
require_once 'db_fns.php';
session_start(); 

function register_user($username, $email, $hashed_password) {
    $conn = db_connect();
    
    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $hashed_password);
    
    return $stmt->execute();
}

function user_exists($username) {
    $conn = db_connect();
    
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->num_rows > 0; 
}


?>
