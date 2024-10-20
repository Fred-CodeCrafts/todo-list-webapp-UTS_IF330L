<?php
require_once 'db_fns.php';
session_start(); // Start the session for user authentication

function register_user($username, $email, $hashed_password) {
    $conn = db_connect();
    
    // Prepare the SQL statement
    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $hashed_password);
    
    // Execute and check for success
    return $stmt->execute();
}

function user_exists($username) {
    // Connect to the database
    $conn = db_connect();
    
    // Prepare a query to check if the username already exists in the database
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Check if any row was returned
    return $result->num_rows > 0; // Return true or false
}


// Add any other functions related to user authentication here
?>
