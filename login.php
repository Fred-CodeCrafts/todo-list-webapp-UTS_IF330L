<?php
require_once('db_fns.php');
require_once('user_auth_fns.php');
require_once('session_fns.php');

// Start the session at the beginning only if it's not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Sanitize user input
$username = htmlspecialchars(trim($_POST['username']));
$password = htmlspecialchars(trim($_POST['password']));

try {
    // Attempt to log in
    if (login($username, $password)) {
        // Connect to the database to get the user ID
        $conn = db_connect();
        
        // Prepare and execute the SQL statement to get the user_id
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            // Fetch the user ID
            $row = $result->fetch_assoc();
            $_SESSION['valid_user'] = $username;
            $_SESSION['user_id'] = $row['user_id']; // Set user_id in session

            // Debugging info to check if user_id is set
            echo "Username: " . $_SESSION['valid_user'] . "<br>";
            echo "User ID: " . $_SESSION['user_id'] . "<br>";
            
            // Redirect to the member page
            header('Location: member.php');
            exit();
        } else {
            echo 'User ID not found. <a href="login_form.php">Try again</a>';
        }

        // Close the statement and connection
        $stmt->close();
        $conn->close();
    } else {
        echo 'Login failed. <a href="login_form.php">Try again</a>';
    }
} catch (Exception $e) {
    // Catch the exception thrown by the login function and display an error message
    echo 'Error: ' . $e->getMessage() . '. <a href="login_form.php">Try again</a>';
}
?>
