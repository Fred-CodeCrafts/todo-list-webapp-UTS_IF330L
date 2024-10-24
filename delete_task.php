<?php
require_once('db_fns.php');
require_once('session_fns.php');
session_start();

check_valid_user();

$id = (int)$_GET['id']; 

$conn = db_connect();

$stmt = $conn->prepare("DELETE FROM tasks WHERE task_id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        header('Location: list.php');
        exit; 
    } else {
        echo 'No task found with that ID.';
    }
} else {
    die('Error executing query: ' . $stmt->error);
}

$stmt->close();
$conn->close();
?>
