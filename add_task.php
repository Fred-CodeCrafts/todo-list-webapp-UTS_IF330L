<?php
require_once('db_fns.php');
require_once('session_fns.php');
session_start();

check_valid_user();

if (!isset($_SESSION['user_id'])) {
    echo 'User ID is not set in the session.';
    exit();  
}

$username = $_SESSION['valid_user'];
$user_id = $_SESSION['user_id']; 

if (isset($_POST['task']) && isset($_POST['due_date']) && isset($_POST['task_type'])) {
    $task = htmlspecialchars(trim($_POST['task']));
    $due_date = htmlspecialchars(trim($_POST['due_date']));
    $task_type = htmlspecialchars(trim($_POST['task_type']));

    $conn = db_connect();

    $stmt = $conn->prepare("INSERT INTO tasks (user_id, task_description, due_date, task_type) VALUES (?, ?, ?, ?)");
    
    $stmt->bind_param('isss', $user_id, $task, $due_date, $task_type);

    if ($stmt->execute()) {
        echo 'Task added successfully. <a href="list.php">Back to tasks</a>';
    } else {
        echo 'Error adding task: ' . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo 'Task, due date, and task type are required.';
}

?>
