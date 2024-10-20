<?php
require_once('db_fns.php');
require_once('user_auth_fns.php');

// Start the session only if it hasn't been started yet
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate passwords match
    if ($_POST['password'] !== $_POST['confirm_password']) {
        die('Passwords do not match!');
    }

    // Get and sanitize user input
    $username = htmlspecialchars(trim($_POST['username']));
    $email = htmlspecialchars(trim($_POST['email']));
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);

    $conn = db_connect(); // Connect to the database

    // Check if the username already exists
    if (user_exists($username)) {
        die('Username already exists!');
    }

    // Prepare and execute the SQL statement to insert the new user
    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $password);

    if ($stmt->execute()) {
        // Optionally set session variables
        $_SESSION['valid_user'] = $username; // Log in the user after registration
        $_SESSION['user_id'] = $conn->insert_id; // Store user ID in session (if needed)

        echo "Registration successful! Welcome, $username! <a href='member.php'>Go to your dashboard</a>";
    } else {
        echo "Registration failed: " . $conn->error;
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request method.";
}
?>
