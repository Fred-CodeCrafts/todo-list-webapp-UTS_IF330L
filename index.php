<!DOCTYPE html> 
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auth Page</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 h-screen flex items-center justify-center">

<?php
// Default to login mode
$isLogin = true;
if (isset($_GET['mode']) && $_GET['mode'] === 'register') {
    $isLogin = false;
}
?>

<div class="container mx-auto max-w-md p-6 bg-white rounded-lg shadow-lg">
    <?php if ($isLogin): ?>
        <!-- Login Form -->
        <h2 class="text-center text-2xl font-semibold mb-6 text-gray-800">Login</h2>
        <form action="login.php" method="post" class="flex flex-col">
            <label class="text-sm text-gray-600 mb-1">Username</label>
            <input type="text" name="username" required class="p-2 border rounded-md mb-4 focus:outline-none focus:ring-2 focus:ring-[#86d5f8]">
            
            <label class="text-sm text-gray-600 mb-1">Password</label>
            <input type="password" name="password" required class="p-2 border rounded-md mb-4 focus:outline-none focus:ring-2 focus:ring-[#86d5f8]">
            
            <input type="submit" value="Login" class="bg-[#86d5f8] text-white rounded-md p-2 w-full hover:bg-[#51839a] transition duration-300 cursor-pointer">
        </form>
        <div class="text-center mt-4">
            <a href="?mode=register" class="text-sm text-blue-600 hover:underline">Don't have an account? Register here</a>
        </div>
    <?php else: ?>
        <!-- Registration Form -->
        <h2 class="text-center text-2xl font-semibold mb-6 text-gray-800">Register</h2>
        <form action="register_new.php" method="post" class="flex flex-col">
            <label class="text-sm text-gray-600 mb-1">Username</label>
            <input type="text" name="username" required class="p-2 border rounded-md mb-4 focus:outline-none focus:ring-2 focus:ring-[#86d5f8]">
            
            <label class="text-sm text-gray-600 mb-1">Email</label>
            <input type="email" name="email" required class="p-2 border rounded-md mb-4 focus:outline-none focus:ring-2 focus:ring-[#86d5f8]">
            
            <label class="text-sm text-gray-600 mb-1">Password</label>
            <input type="password" name="password" required class="p-2 border rounded-md mb-4 focus:outline-none focus:ring-2 focus:ring-[#86d5f8]">
            
            <label class="text-sm text-gray-600 mb-1">Confirm Password</label>
            <input type="password" name="confirm_password" required class="p-2 border rounded-md mb-4 focus:outline-none focus:ring-2 focus:ring-[#86d5f8]">
            
            <input type="submit" value="Register" class="bg-[#86d5f8] text-white rounded-md p-2 w-full hover:bg-[#51839a] transition duration-300 cursor-pointer">
        </form>
        <div class="text-center mt-4">
            <a href="?mode=login" class="text-sm text-blue-600 hover:underline">Already have an account? Login here</a>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
