<?php
require_once('db_fns.php');
require_once('user_auth_fns.php');
require_once('session_fns.php');

$username = htmlspecialchars($_POST['username']);
$password = $_POST['password'];

if (login($username, $password)) {
    session_start();
    $_SESSION['valid_user'] = $username;
    header('Location: member.php');
} else {
    echo 'Login failed. <a href="login_form.php">Try again</a>';
}
?>