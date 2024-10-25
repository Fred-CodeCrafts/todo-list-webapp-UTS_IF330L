<?php
require_once('db_fns.php');
require_once('user_auth_fns.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $recaptcha_secret = '6Lfl4msqAAAAAK3DewQmLHxEG3gqNfOuOcCt1bSL'; 
    $recaptcha_response = $_POST['g-recaptcha-response'];

    $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$recaptcha_secret&response=$recaptcha_response");
    $response_keys = json_decode($response, true);

    if (intval($response_keys["success"]) !== 1) {
        echo "<script>alert('Please complete the CAPTCHA.'); window.history.back();</script>";
        exit();
    }

    if ($_POST['password'] !== $_POST['confirm_password']) {
        echo "<script>alert('Passwords do not match!'); window.history.back();</script>";
        exit();
    }

    $username = htmlspecialchars(trim($_POST['username']));
    $email = htmlspecialchars(trim($_POST['email']));
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);

    $conn = db_connect(); 

    if (user_exists($username)) {
        echo "<script>alert('Username already exists!'); window.history.back();</script>";
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $password);

    if ($stmt->execute()) {
        $_SESSION['valid_user'] = $username; 
        $_SESSION['user_id'] = $conn->insert_id; 

        echo "<script>alert('Registration successful! Welcome, $username!'); window.location.href='list.php';</script>";
    } else {
        echo "<script>alert('Registration failed: " . addslashes($conn->error) . "'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "<script>alert('Invalid request method.'); window.history.back();</script>";
}
?>
