<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 h-screen flex items-center justify-center">

<div class="container mx-auto max-w-md p-6 bg-white rounded-lg shadow-lg">
    <h2 class="text-center text-2xl font-semibold mb-6 text-gray-800">Reset Password</h2>
    <form action="forgot_password_fns.php" method="post" class="flex flex-col">
        <label class="text-sm text-gray-600 mb-1">Username</label>
        <input type="text" name="username" required aria-required="true" class="p-2 border rounded-md mb-4 focus:outline-none focus:ring-2 focus:ring-[#86d5f8]">
        
        <label class="text-sm text-gray-600 mb-1">Email Address</label>
        <input type="email" name="email" required aria-required="true" class="p-2 border rounded-md mb-4 focus:outline-none focus:ring-2 focus:ring-[#86d5f8]">
        
        <input type="submit" value="Send Reset Link" class="bg-[#86d5f8] text-white rounded-md p-2 w-full hover:bg-[#51839a] transition duration-300 cursor-pointer">
    </form>

    <div class="text-center mt-4">
        <a href="index.php" class="text-sm text-blue-600 hover:underline">Back to Login</a>
    </div>
</div>

</body>
</html>
