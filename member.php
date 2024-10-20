<?php
// Include necessary function files for this application
require_once('db_fns.php');
require_once('session_fns.php');
session_start();

check_valid_user(); // Check if the user is logged in
$conn = db_connect();
$username = $_SESSION['valid_user'];
$user_id = $_SESSION['user_id']; // Retrieve user_id from session

echo "<h2>Welcome, $username!</h2>";
echo '<a href="logout.php">Logout</a>';
echo '<h3>Your Tasks</h3>';

// Use user_id to retrieve tasks
$result = $conn->query("SELECT task_id, task_description, is_completed FROM tasks WHERE user_id='$user_id'");

if ($result->num_rows > 0) {
    echo '<ul>';
    while ($row = $result->fetch_assoc()) {
        echo "<li>{$row['task_description']} - " . ($row['is_completed'] ? 'Completed' : 'Pending') . "
        <a href='update_task.php?id={$row['task_id']}'>Edit</a> | 
        <a href='delete_task.php?id={$row['task_id']}'>Delete</a></li>";
    }
    echo '</ul>';
} else {
    echo 'No tasks found.';
}
?>

<h3>Add New Task</h3>
<form action="add_task.php" method="post">
    Task: <input type="text" name="task" required><br>
    Due Date: <input type="date" name="due_date" required><br>
    <input type="submit" value="Add Task">
</form>
