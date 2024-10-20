<?php
require_once('db_fns.php');
require_once('session_fns.php');
session_start();

// Check if the user is logged in
check_valid_user();

// Retrieve the username and user_id from session
$username = $_SESSION['valid_user'];
$user_id = $_SESSION['user_id']; // Assume you store user_id in session during login

// Check if the form fields are set
if (isset($_POST['task']) && isset($_POST['due_date'])) {
    // Debugging: Check what is being submitted
    var_dump($_POST['task']);
    var_dump($_POST['due_date']);
    
    // Sanitize user input
    $task = htmlspecialchars(trim($_POST['task']));
    $due_date = htmlspecialchars(trim($_POST['due_date']));

    // Connect to the database
    $conn = db_connect();

    // Prepare and execute the SQL statement to insert the new task
    $stmt = $conn->prepare("INSERT INTO tasks (user_id, task_description, due_date) VALUES (?, ?, ?)");
    
    // Bind parameters (assuming task_description is the correct column name in your tasks table)
    $stmt->bind_param('iss', $user_id, $task, $due_date);

    if ($stmt->execute()) {
        echo 'Task added successfully. <a href="member.php">Back to tasks</a>';
    } else {
        echo 'Error adding task: ' . $stmt->error; // Use $stmt->error for the specific statement error
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
} else {
    echo 'Task and due date are required.';
}
?>
