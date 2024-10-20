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

// Function to handle user login
function login($username, $password) {
    // Connect to the database
    $conn = db_connect();

    // Secure against SQL injection
    $username = mysqli_real_escape_string($conn, $username);

    // Retrieve user information
    $query = "SELECT * FROM users WHERE username = '$username'";
    $result = $conn->query($query);

    if (!$result) {
        throw new Exception("Could not execute query");
    }

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify the hashed password
        if (password_verify($password, $user['password'])) {
            // Set session for the valid user
            $_SESSION['valid_user'] = $username;
            $_SESSION['user_id'] = $user['id']; // Assuming 'id' is the primary key
            return true;
        } else {
            throw new Exception("Invalid password.");
        }
    } else {
        throw new Exception("User does not exist.");
    }
}

// Add any other functions related to user authentication here
?>
