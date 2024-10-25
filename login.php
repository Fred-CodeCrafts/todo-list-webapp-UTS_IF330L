<?php
require_once('db_fns.php');
require_once('user_auth_fns.php');
require_once('session_fns.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$recaptcha_secret = '6Lfl4msqAAAAAK3DewQmLHxEG3gqNfOuOcCt1bSL'; 
$recaptcha_response = $_POST['g-recaptcha-response'];

$response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$recaptcha_secret&response=$recaptcha_response");
$response_keys = json_decode($response, true);

if (intval($response_keys["success"]) !== 1) {
    echo "<script>alert('Please complete the CAPTCHA.'); window.location.href='index.php';</script>";
    exit();
}

$username = htmlspecialchars(trim($_POST['username']));
$password = htmlspecialchars(trim($_POST['password']));

try {
    if (login($username, $password)) {
        $conn = db_connect();
        
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            $_SESSION['valid_user'] = $username;
            $_SESSION['user_id'] = $row['user_id'];

            echo "<script>
            window.location.href='list.php';
            </script>";
            exit();
        } else {
            echo "<script>alert('User ID not found. Please try again.'); window.location.href='index.php';</script>";
        }

        $stmt->close();
        $conn->close();
    } else {
        echo "<script>alert('Login failed. Please check your username and password.'); window.location.href='index.php';</script>";
    }
} catch (Exception $e) {
    echo "<script>alert('Error: " . addslashes($e->getMessage()) . ". Please try again.'); window.location.href='index.php';</script>";
}
?>
