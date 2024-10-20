<?php
function db_connect() {
    $host = 'localhost';
    $user = 'todo_user';
    $password = '';
    $dbname = 'todo_app';

    $conn = new mysqli('localhost', 'todo_user', 'webpro1000', 'todo_app');
    
    if ($conn->connect_error) {
        die('Connection failed: ' . $conn->connect_error);
    }
    
    return $conn;
}
?>