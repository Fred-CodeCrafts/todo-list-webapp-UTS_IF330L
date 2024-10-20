<?php
require_once('db_fns.php');
require_once('session_fns.php');
session_start();

check_valid_user();

// Get the task ID from the URL and sanitize it
$id = (int)$_GET['id']; // Cast to an integer for security

$conn = db_connect();

// Prepare and execute the DELETE statement
$stmt = $conn->prepare("DELETE FROM tasks WHERE task_id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    // Optionally, you can check how many rows were affected
    if ($stmt->affected_rows > 0) {
        // Task deleted successfully
        header('Location: member.php');
        exit; // Stop further execution
    } else {
        echo 'No task found with that ID.';
    }
} else {
    die('Error executing query: ' . $stmt->error);
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
