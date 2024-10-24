<?php

function start_session() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
}

function end_session() {
    session_start();
    $_SESSION = array();
    session_destroy();
}

function check_valid_user() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['valid_user'])) {
        header("Location: login.php");
        exit();
    }
}

function logout() {
    session_start(); 
    $_SESSION = array();


    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    session_destroy(); 
}
function login($username, $password) {
    $conn = db_connect();
    
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['valid_user'] = $username;
            $_SESSION['user_id'] = $user['id']; 
            return true; 
        } else {
            throw new Exception("Invalid password.");
        }
    } else {
        throw new Exception("User does not exist.");
    }
}


?>
