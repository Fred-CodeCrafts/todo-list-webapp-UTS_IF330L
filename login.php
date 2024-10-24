<?php
require_once('db_fns.php');
require_once('user_auth_fns.php');
require_once('session_fns.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
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

            echo "Username: " . $_SESSION['valid_user'] . "<br>";
            echo "User ID: " . $_SESSION['user_id'] . "<br>";
            
            header('Location: list.php');
            exit();
        } else {
            echo 'User ID not found. <a href="login_form.php">Try again</a>';
        }

        $stmt->close();
        $conn->close();
    } else {
        echo 'Login failed. <a href="login_form.php">Try again</a>';
    }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . '. <a href="login_form.php">Try again</a>';
}
?>
