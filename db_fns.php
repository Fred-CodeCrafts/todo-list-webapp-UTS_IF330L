<?php
function db_connect() {
    $host = 'localhost';
    $user = 'hary8495_alenajadeh';
    $password = 'alenajadeh';
    $dbname = 'hary8495_todo_app';

    $conn = new mysqli('localhost', 'hary8495_alenajadeh', 'alenajadeh', 'hary8495_todo_app');
    
    if ($conn->connect_error) {
        die('Connection failed: ' . $conn->connect_error);
    }
    
    return $conn;
}
?>
