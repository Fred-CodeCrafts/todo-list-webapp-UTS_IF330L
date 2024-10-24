<?php
require_once('db_fns.php');
require_once('user_auth_fns.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['password'] !== $_POST['confirm_password']) {
        die('Passwords do not match!');
    }

    $username = htmlspecialchars(trim($_POST['username']));
    $email = htmlspecialchars(trim($_POST['email']));
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);

    $conn = db_connect(); 

    if (user_exists($username)) {
        die('Username already exists!');
    }

    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $password);

    if ($stmt->execute()) {
        $_SESSION['valid_user'] = $username; 
        $_SESSION['user_id'] = $conn->insert_id; 

        echo "Registration successful! Welcome, $username! <a href='list.php'>Go to your dashboard</a>";
    } else {
        echo "Registration failed: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request method.";
}
?>
