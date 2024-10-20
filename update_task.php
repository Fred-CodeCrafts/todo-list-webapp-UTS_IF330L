<?php
require_once('db_fns.php');
require_once('session_fns.php');
session_start();

check_valid_user();

$id = (int)$_GET['id']; // Cast to integer for security
$conn = db_connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Use the correct column name based on your database structure
    $task_description = htmlspecialchars($_POST['task']); // Updated variable name
    $due_date = $_POST['due_date'];
    
    // Determine if the task is completed based on the checkbox
    $is_completed = isset($_POST['is_completed']) ? 1 : 0; // 1 for completed, 0 for not completed

    // Update query with the correct column names
    $stmt = $conn->prepare("UPDATE tasks SET task_description = ?, due_date = ?, is_completed = ? WHERE task_id = ?");
    $stmt->bind_param('ssii', $task_description, $due_date, $is_completed, $id); // Updated variable names

    if ($stmt->execute()) {
        header('Location: member.php');
        exit; // Stop further execution
    } else {
        echo "Error updating task: " . $stmt->error; // Use $stmt->error for the prepared statement
    }
} else {
    // Fetch the task using the correct column name
    $result = $conn->query("SELECT * FROM tasks WHERE task_id = $id");
    $task = $result->fetch_assoc();

    if (!$task) {
        die("Task not found.");
    }
?>

<h3>Edit Task</h3>
<form method="post">
    Task: <input type="text" name="task" value="<?= htmlspecialchars($task['task_description']) ?>" required><br> <!-- Updated variable name -->
    Due Date: <input type="date" name="due_date" value="<?= $task['due_date'] ?>" required><br>
    
    <!-- Checkbox for is_completed -->
    <label for="is_completed">Completed:</label>
    <input type="checkbox" name="is_completed" id="is_completed" value="1" <?= $task['is_completed'] ? 'checked' : '' ?>><br>

    <input type="submit" value="Update Task">
</form>

<?php 
} // End of else for the GET request
?>
