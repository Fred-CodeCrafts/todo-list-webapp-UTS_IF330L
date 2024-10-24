<?php 
require_once('session_fns.php');
require_once('profile_fns.php');

session_start();

check_valid_user();

$user_id = $_SESSION['user_id'];

$user_profile = get_user_profile($user_id);
if (!$user_profile) {
    echo 'User not found.';
    exit();
}

$profile_image = isset($user_profile['profile_image']) && $user_profile['profile_image'] !== 'default.png' 
    ? 'uploads/profile_images/' . $user_profile['profile_image'] 
    : 'images/profile_images/default.png';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['change_password'])) {
    $new_username = htmlspecialchars(trim($_POST['username']));
    $new_email = htmlspecialchars(trim($_POST['email']));
    
    if (update_user_profile($user_id, $new_username, $new_email)) {
        echo 'Profile updated successfully. <a href="profile.php">View Profile</a>';
    } else {
        echo 'Error updating profile.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];

    try {
        change_password($user_profile['username'], $old_password, $new_password);
        echo '<p>Password changed successfully.</p>';
    } catch (Exception $e) {
        echo '<p>Error: ' . $e->getMessage() . '</p>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background-color: #213a45;
            font-family: 'Courier New', Courier, monospace; /* Change font to Courier */
        }
    </style>
</head>
<body class="flex justify-center items-center min-h-screen">

<div class="container mx-auto bg-[#2b4d5a] rounded-lg shadow-lg p-8 max-w-lg text-center">
    <h2 class="text-3xl font-semibold text-[#86d5f8] mb-6">Your Profile</h2>
    
    <div class="text-center mb-6">
        <img src="<?php echo $profile_image; ?>" alt="Profile Image" class="w-32 h-32 rounded-full mb-4 mx-auto object-cover">
    </div>

    <form action="profile.php" method="post" class="flex flex-col mb-8">
        <label for="username" class="text-sm text-white mb-1">Username:</label>
        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user_profile['username']); ?>" required class="p-2 border border-transparent rounded-md mb-4 focus:outline-none focus:ring-2 focus:ring-[#86d5f8]">

        <label for="email" class="text-sm text-white mb-1">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user_profile['email']); ?>" required class="p-2 border border-transparent rounded-md mb-4 focus:outline-none focus:ring-2 focus:ring-[#86d5f8]">

        <input type="submit" value="Update Profile" class="bg-[#86d5f8] text-[#213a45] rounded-md p-2 w-full font-bold transition duration-300 cursor-pointer hover:bg-[#51839a]">
    </form>    

    <h3 class="text-lg font-semibold mb-4 text-[#86d5f8]">Upload Profile Image</h3>
    <form action="upload_image.php" method="post" enctype="multipart/form-data" class="flex flex-col mb-8">
        <input type="file" id="profile_image" name="profile_image" class="p-2 border border-transparent rounded-md mb-4 focus:outline-none focus:ring-2 focus:ring-[#86d5f8]">

        <input type="submit" value="Upload Image" class="bg-[#86d5f8] text-[#213a45] rounded-md p-2 w-full font-bold transition duration-300 cursor-pointer hover:bg-[#51839a]">
    </form>    

    <h3 class="text-lg font-semibold mb-4 text-[#86d5f8]">Change Password</h3>
    <form action="profile.php" method="post" class="flex flex-col">
        <label for="old_password" class="text-sm text-white mb-1">Old Password:</label>
        <input type="password" id="old_password" name="old_password" required class="p-2 border border-transparent rounded-md mb-4 focus:outline-none focus:ring-2 focus:ring-[#86d5f8]">

        <label for="new_password" class="text-sm text-white mb-1">New Password:</label>
        <input type="password" id="new_password" name="new_password" required class="p-2 border border-transparent rounded-md mb-4 focus:outline-none focus:ring-2 focus:ring-[#86d5f8]">

        <input type="submit" name="change_password" value="Change Password" class="bg-[#86d5f8] text-[#213a45] rounded-md p-2 w-full font-bold transition duration-300 cursor-pointer hover:bg-[#51839a]">
    </form>
    
    <div class="mt-6">
        <a href="list.php" class="text-sm text-[#86d5f8] hover:underline">Back to Tasks</a>
    </div>
</div>
</body>
</html>
