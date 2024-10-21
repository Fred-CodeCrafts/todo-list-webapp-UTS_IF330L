<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Task Management App</title>
    <script src="https://cdn.tailwindcss.com"></script> <!-- Tailwind CSS CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- Font Awesome CDN -->
</head>
<body class="bg-gray-100">

<?php
// Include necessary function files for this application
require_once('db_fns.php');
require_once('session_fns.php');
require_once('task_types.php');  // Include the task types file

session_start();

// Check if user_id is set in the session
if (!isset($_SESSION['user_id'])) {
    echo 'User ID is not set in the session. Debugging Info: ';
    print_r($_SESSION); // This will output all session data for debugging
    exit();
}

check_valid_user(); // Check if the user is logged in
$conn = db_connect();
$username = $_SESSION['valid_user'];
$user_id = $_SESSION['user_id']; // Retrieve user_id from session

// Fetch the user profile data, including the profile image
$result = $conn->query("SELECT profile_image FROM users WHERE user_id='$user_id'");
$user_profile = $result->fetch_assoc();

// Set profile image to default if not available
$profile_image = isset($user_profile['profile_image']) && !empty($user_profile['profile_image']) 
    ? 'uploads/profile_images/' . $user_profile['profile_image'] 
    : 'images/profile_images/default.jpg';

echo "<div class='max-w-screen-sm mx-auto p-4 bg-white rounded-lg shadow-md'>"; // Center and narrow content
echo "<div class='flex justify-between items-center mb-4'>"; // Flex container for row alignment
echo "<h2 class='text-2xl font-bold'>Welcome, $username!</h2>";
echo "<div class='flex items-center'>"; // Wrap profile image and logout in a div for grouping

// Replace 'View Profile' link with profile image
echo "<a href='profile.php' class='block mr-4'>
        <img src='$profile_image' alt='Profile Image' class='w-10 h-10 rounded-full object-cover'>
      </a>";

echo "<a href='logout.php' class='text-blue-500 hover:text-blue-700 underline'>Logout</a>";
echo "</div>";
echo "</div>"; // Close flex container
echo "<h3 class='text-lg font-semibold mt-6'>Your Tasks</h3>";
?>

<!-- Task Filter and Search -->
<div class="flex flex-col sm:flex-row items-center mt-4 space-y-2 sm:space-y-0 sm:space-x-4">
    <!-- Filter Dropdown for Task Status -->
    <select id="statusFilter" class="border border-gray-300 rounded-md p-2 w-full sm:w-auto">
        <option value="all">All Tasks</option>
        <option value="completed">Completed</option>
        <option value="pending">Pending</option>
    </select>

    <!-- Search Bar -->
    <input type="text" id="taskSearch" placeholder="Search tasks..." class="border border-gray-300 rounded-md p-2 w-full sm:w-auto flex-1">
</div>

<?php
// Use user_id to retrieve tasks, including due_date
$result = $conn->query("SELECT task_id, task_description, is_completed, task_type, due_date FROM tasks WHERE user_id='$user_id'");

if ($result->num_rows > 0) {
    echo '<ul id="taskList" class="mt-4 space-y-4">';
    while ($row = $result->fetch_assoc()) {
        $status = $row['is_completed'] ? 'completed' : 'pending';
        $task_type = $row['task_type'];
        $task_image = $task_types[$task_type]; // Get the image for the task type
        $due_date = date('F j, Y', strtotime($row['due_date'])); // Format the due date

        // Font Awesome icons for completed and pending statuses
        $status_icon = $row['is_completed'] ? '<i class="fas fa-check-circle text-green-500"></i>' : '<i class="fas fa-clock text-yellow-500"></i>';

        // Display task description, due date, and status
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

<!-- Add Task Form -->
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
    <input type="submit" value="Add Task" 
    class="text-[#51839a] rounded-md p-2 w-full transition duration-300 cursor-pointer" 
    style="background-color: #86d5f8;" 
    onmouseover="this.style.backgroundColor='#51839a'; this.style.color='white';" 
    onmouseout="this.style.backgroundColor='#86d5f8'; this.style.color='#51839a';">
    </div>
</form>
</div> <!-- Closing the centered content wrapper -->

<script>
document.addEventListener('DOMContentLoaded', function () {
    const statusFilter = document.getElementById('statusFilter');
    const taskSearch = document.getElementById('taskSearch');
    const taskItems = document.querySelectorAll('.task-item');

    // Set the min attribute of the due date input to today's date
    const today = new Date().toISOString().split('T')[0];
    document.querySelector('input[name="due_date"]').setAttribute('min', today);

    // Function to filter tasks by status
    function filterTasks() {
        const filterValue = statusFilter.value;
        const searchTerm = taskSearch.value.toLowerCase();

        taskItems.forEach(task => {
            const taskStatus = task.getAttribute('data-status');
            const taskDescription = task.getAttribute('data-task').toLowerCase();

            // Match the status and search term
            if ((filterValue === 'all' || taskStatus === filterValue) &&
                taskDescription.includes(searchTerm)) {
                task.style.display = ''; // Show matching tasks
            } else {
                task.style.display = 'none'; // Hide non-matching tasks
            }
        });
    }

    // Event listeners for status filter and search bar
    statusFilter.addEventListener('change', filterTasks);
    taskSearch.addEventListener('input', filterTasks);
});
</script>

<style>
    /* Tailwind overrides and custom styles */
    .task-item:nth-child(odd) {
        background-color: #86d5f8; /* Stable light blue background for odd tasks */
    }

    .task-item:nth-child(even) {
        background-color: #fff; /* White background for even tasks */
    }

    .task-item:hover {
        transform: scale(1.02); /* Slightly enlarge on hover */
        transition: transform 0.2s;
    }
</style>
</body>
</html>
