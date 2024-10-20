<?php
// session_fns.php

// Start a secure session
function start_session() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
}

// Destroy the session and log out the user
function end_session() {
    session_start();
    $_SESSION = array();
    session_destroy();
}

// session_fns.php or a similar file
function check_valid_user() {
    // Start the session if it is not already started
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    // Check if the user is logged in
    if (!isset($_SESSION['valid_user'])) {
        // If not, redirect to the login page or show an error
        header("Location: login.php");
        exit();
    }
}

// Function to log out the user
function logout() {
    session_start(); // Start the session to access session variables
    $_SESSION = array(); // Clear all session variables

    // If cookies are used, delete the session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    session_destroy(); // Destroy the session
}
function login($username, $password) {
    // Connect to the database
    $conn = db_connect();
    
    // Prepare the SQL statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Check if the user exists
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify the hashed password using password_verify
        if (password_verify($password, $user['password'])) {
            // Set session for the valid user
            $_SESSION['valid_user'] = $username;
            $_SESSION['user_id'] = $user['id']; // Assuming 'id' is the primary key
            return true; // Login successful
        } else {
            throw new Exception("Invalid password.");
        }
    } else {
        throw new Exception("User does not exist.");
    }
}


?>
