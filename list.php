<?php
session_start();

require_once('db_fns.php');
require_once('session_fns.php');
require_once('task_types.php');  

if (!isset($_SESSION['user_id'])) {
    echo 'User ID is not set in the session. Debugging Info: ';
    print_r($_SESSION); 
    exit();
}

check_valid_user(); 
$conn = db_connect();
$username = $_SESSION['valid_user'];
$user_id = $_SESSION['user_id'];

$result = $conn->query("SELECT profile_image FROM users WHERE user_id='$user_id'");
$user_profile = $result->fetch_assoc();

$profile_image = isset($user_profile['profile_image']) && !empty($user_profile['profile_image']) 
    ? 'uploads/profile_images/' . $user_profile['profile_image'] 
    : 'images/profile_images/default.png';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Task Management App</title>
    <script src="https://cdn.tailwindcss.com"></script> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- Font Awesome CDN -->
</head>
<body class="bg-gray-100">

<div class='max-w-screen-sm mx-auto p-4 bg-white rounded-lg shadow-md'>
    <div class='flex justify-between items-center mb-4'>
        <h2 class='text-2xl font-bold'>Welcome, <?php echo $username; ?>!</h2>
        <div class='flex items-center'>
            <a href='profile.php' class='block mr-4'>
                <img src='<?php echo $profile_image; ?>' alt='Profile Image' class='w-10 h-10 rounded-full object-cover'>
            </a>
            <a href='logout.php' class='text-blue-500 hover:text-blue-700 underline'>Logout</a>
        </div>
    </div>

    <h3 class='text-lg font-semibold mt-6'>Your Tasks</h3>

    <div class="flex flex-col sm:flex-row items-center mt-4 space-y-2 sm:space-y-0 sm:space-x-4">
        <select id="statusFilter" class="border border-gray-300 rounded-md p-2 w-full sm:w-auto">
            <option value="all">All Tasks</option>
            <option value="completed">Completed</option>
            <option value="pending">Pending</option>
        </select>
        <input type="text" id="taskSearch" placeholder="Search tasks..." class="border border-gray-300 rounded-md p-2 w-full sm:w-auto flex-1">
    </div>

    <?php
    $result = $conn->query("SELECT task_id, task_description, is_completed, task_type, due_date FROM tasks WHERE user_id='$user_id'");

    if ($result->num_rows > 0) {
        echo '<ul id="taskList" class="mt-4 space-y-4">';
        while ($row = $result->fetch_assoc()) {
            $status = $row['is_completed'] ? 'completed' : 'pending';
            $task_type = $row['task_type'];
            $task_image = $task_types[$task_type];
            $due_date = date('F j, Y', strtotime($row['due_date']));
            $status_icon = $row['is_completed'] ? '<i class="fas fa-check-circle text-green-500"></i>' : '<i class="fas fa-clock text-yellow-500"></i>';

            echo "<li class='task-item flex items-center space-x-4 p-4 border border-gray-300 rounded-md' data-task='{$row['task_description']}' data-status='$status'>
                    <img src='images/$task_image' alt='$task_type' class='w-6 h-6'> <!-- Image for task type -->
                    <span class='flex-1'>{$row['task_description']} - Due: $due_date</span>
                    <div class='flex items-center space-x-2'>
                        $status_icon
                        <a href='update_task.php?id={$row['task_id']}' class='text-blue-500 hover:underline'>Edit</a>
                        <a href='delete_task.php?id={$row['task_id']}' class='text-red-500 hover:underline'>Delete</a>
                    </div>
                  </li>";
        }
        echo '</ul>';
    } else {
        echo '<p class="text-gray-600">No tasks found.</p>';
    }
    ?>

    <h3 class="text-lg font-semibold mt-6">Add New Task</h3>
    <form action="add_task.php" method="post" class="mt-4 space-y-4">
        <div>
            Task: <input type="text" name="task" required class="border border-gray-300 rounded-md p-2 w-full">
        </div>
        <div>
            Due Date: <input type="date" name="due_date" required class="border border-gray-300 rounded-md p-2 w-full">
        </div>
        <div>
            <select name="task_type" required class="border border-gray-300 rounded-md p-2 w-full">
                <option value="work">Work</option>
                <option value="personal">Personal</option>
                <option value="shopping">Shopping</option>
                <option value="fitness">Fitness</option>
                <option value="others">Others</option>
            </select>
        </div>
        <div>
            <input type="submit" value="Add Task" class="text-[#51839a] rounded-md p-2 w-full transition duration-300 cursor-pointer" style="background-color: #86d5f8;" onmouseover="this.style.backgroundColor='#51839a'; this.style.color='white';" onmouseout="this.style.backgroundColor='#86d5f8'; this.style.color='#51839a';">
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const statusFilter = document.getElementById('statusFilter');
    const taskSearch = document.getElementById('taskSearch');
    const taskItems = document.querySelectorAll('.task-item');

    const today = new Date().toISOString().split('T')[0];
    document.querySelector('input[name="due_date"]').setAttribute('min', today);

    function filterTasks() {
        const filterValue = statusFilter.value;
        const searchTerm = taskSearch.value.toLowerCase();

        taskItems.forEach(task => {
            const taskStatus = task.getAttribute('data-status');
            const taskDescription = task.getAttribute('data-task').toLowerCase();

            if ((filterValue === 'all' || taskStatus === filterValue) &&
                taskDescription.includes(searchTerm)) {
                task.style.display = ''; 
            } else {
                task.style.display = 'none';
            }
        });
    }

    statusFilter.addEventListener('change', filterTasks);
    taskSearch.addEventListener('input', filterTasks);
});
</script>

<style>
    .task-item:nth-child(odd) {
        background-color: #86d5f8; 
    }
    .task-item:nth-child(even) {
        background-color: #fff; 
    }
    .task-item:hover {
        transform: scale(1.02);
        transition: transform 0.2s;
    }
</style>

</body>
</html>
