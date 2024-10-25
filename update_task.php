<?php
require_once('db_fns.php');
require_once('session_fns.php');
session_start();

check_valid_user();

$id = (int)$_GET['id']; 
$conn = db_connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $task_description = htmlspecialchars($_POST['task']); 
    $due_date = $_POST['due_date'];
    
    $is_completed = isset($_POST['is_completed']) ? 1 : 0; 

    $stmt = $conn->prepare("UPDATE tasks SET task_description = ?, due_date = ?, is_completed = ? WHERE task_id = ?");
    $stmt->bind_param('ssii', $task_description, $due_date, $is_completed, $id); 

    if ($stmt->execute()) {
        echo "<script>alert('Task updated successfully!'); window.location.href='list.php';</script>";
        exit; 
    } else {
        echo "<script>alert('Error updating task: " . addslashes($stmt->error) . "'); window.history.back();</script>";
        exit; 
    }
} else {
    $result = $conn->query("SELECT * FROM tasks WHERE task_id = $id");
    $task = $result->fetch_assoc();

    if (!$task) {
        echo "<script>alert('Task not found.'); window.history.back();</script>";
        exit;
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Task</title>
    <style>
        body {
            background-color: #213a45;
            color: #ffffff;
            font-family: 'Courier New', monospace; 
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: #2b4d5a;
            border-radius: 8px;
            padding: 2rem;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
            text-align: center;
        }

        h3 {
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
            color: #86d5f8;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        input[type="text"],
        input[type="date"],
        input[type="checkbox"] {
            margin-bottom: 1rem;
            padding: 0.5rem;
            border-radius: 4px;
            border: none;
            width: 100%;
        }

        input[type="checkbox"] {
            width: auto;
            transform: scale(1.2);
        }

        label {
            margin-right: 0.5rem;
            font-size: 1rem;
            color: #ffffff;
        }

        input[type="submit"] {
            background-color: #86d5f8;
            color: #213a45;
            padding: 0.75rem;
            font-size: 1rem;
            font-weight: bold;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #51839a;
            color: white;
        }
    </style>
</head>
<body>

<div class="container">
    <h3>Edit Task</h3>
    <form method="post">
        <div>
            <label for="task">Task:</label>
            <input type="text" name="task" id="task" value="<?= htmlspecialchars($task['task_description']) ?>" required>
        </div>
        <div>
            <label for="due_date">Due Date:</label>
            <input type="date" name="due_date" id="due_date" value="<?= $task['due_date'] ?>" required>
        </div>
        <div>
            <label for="is_completed">Completed:</label>
            <input type="checkbox" name="is_completed" id="is_completed" value="1" <?= $task['is_completed'] ? 'checked' : '' ?>>
        </div>
        <input type="submit" value="Update Task">
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('due_date').setAttribute('min', today);
});
</script>

</body>
</html>

<?php 
} 
?>
