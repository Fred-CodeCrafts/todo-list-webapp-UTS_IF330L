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
    $conn = db_connect();
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND password = SHA1(?)");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // User is found, login successful
        return true;
    } else {
        throw new Exception('Old password is incorrect.');
    }
}


?>
