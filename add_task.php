<?php
require_once('db_fns.php');
require_once('session_fns.php');
session_start();

// Check if the user is logged in
check_valid_user();

// Check if the user_id is set in the session
if (!isset($_SESSION['user_id'])) {
    echo 'User ID is not set in the session.';
    exit();  // Stop execution if user_id is missing
}

// Retrieve the username and user_id from session
$username = $_SESSION['valid_user'];
$user_id = $_SESSION['user_id']; // Ensure user_id is set during login

// Check if the form fields are set
if (isset($_POST['task']) && isset($_POST['due_date']) && isset($_POST['task_type'])) {
    // Sanitize user input
    $task = htmlspecialchars(trim($_POST['task']));
    $due_date = htmlspecialchars(trim($_POST['due_date']));
    $task_type = htmlspecialchars(trim($_POST['task_type'])); // Add task type handling

    // Connect to the database
    $conn = db_connect();

    // Prepare and execute the SQL statement to insert the new task
    $stmt = $conn->prepare("INSERT INTO tasks (user_id, task_description, due_date, task_type) VALUES (?, ?, ?, ?)");
    
    // Bind parameters
    $stmt->bind_param('isss', $user_id, $task, $due_date, $task_type);

    if ($stmt->execute()) {
        echo 'Task added successfully. <a href="member.php">Back to tasks</a>';
    } else {
        // If there's an error with the SQL execution
        echo 'Error adding task: ' . $stmt->error;
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
} else {
    // If task, due date, or task type is not set
    echo 'Task, due date, and task type are required.';
}

?>
