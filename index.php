<!DOCTYPE html> 
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auth Page</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Caveat&family=Patrick+Hand&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Patrick Hand', cursive;
        }
        .notebook-bg {
            background: url('https://www.transparenttextures.com/patterns/lined-paper-2.png');
        }
        .dashed-border {
            border: 2px dashed #86d5f8;
        }
        .notebook-shadow {
            box-shadow: 8px 8px 0px rgba(0, 0, 0, 0.1);
        }
        .fade {
            opacity: 0;
            transition: opacity 0.5s ease-in-out;
        }
        .fade.show {
            opacity: 1;
        }
        .hover-normal {
            transition: transform 0.2s ease-in-out, background-color 0.2s;
        }
        .hover-normal:hover {
            transform: scale(1.02);
            background-color: #51839a;
        }
        .title {
            font-family: 'Caveat', cursive;
        }
    </style>
    <script>
        function switchMode(mode) {
            const formContainer = document.querySelector('.form-container');
            formContainer.classList.remove('show');
            
            setTimeout(() => {
                window.location.href = mode;
            }, 500);
        }

        window.onload = () => {
            document.querySelector('.form-container').classList.add('show');
        }
    </script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script> <!-- Add this line -->
</head>
<body class="bg-gray-100 h-screen flex items-center justify-center notebook-bg">

<?php
$isLogin = true;
if (isset($_GET['mode']) && $_GET['mode'] === 'register') {
    $isLogin = false;
}
?>

<div class="container mx-auto max-w-md p-6 bg-white rounded-lg dashed-border notebook-shadow fade form-container">
    <?php if ($isLogin): ?>
        <h2 class="text-center text-4xl title mb-6 text-gray-800">Login</h2>
        <form action="login.php" method="post" class="flex flex-col">
            <label class="text-sm text-gray-600 mb-1">Username</label>
            <input type="text" name="username" required class="p-2 border rounded-md mb-4 focus:outline-none focus:ring-2 focus:ring-[#86d5f8]">
            
            <label class="text-sm text-gray-600 mb-1">Password</label>
            <input type="password" name="password" required class="p-2 border rounded-md mb-4 focus:outline-none focus:ring-2 focus:ring-[#86d5f8]">

            <div class="g-recaptcha" data-sitekey="6Lfl4msqAAAAAPaz2uVIgU_4Ezookz_92TcyiFo5"></div>
            
            <input type="submit" value="Login" class="bg-[#86d5f8] text-white rounded-md p-2 w-full hover-normal cursor-pointer">
        </form>
        <div class="text-center mt-4">
            <a href="javascript:void(0);" onclick="switchMode('?mode=register')" class="text-sm text-blue-600 hover:underline">Don't have an account? Register here</a>
        </div>
    <?php else: ?>
        <h2 class="text-center text-4xl title mb-6 text-gray-800">Register</h2>
        <form action="register_new.php" method="post" class="flex flex-col">
            <label class="text-sm text-gray-600 mb-1">Username</label>
            <input type="text" name="username" required class="p-2 border rounded-md mb-4 focus:outline-none focus:ring-2 focus:ring-[#86d5f8]">
            
            <label class="text-sm text-gray-600 mb-1">Email</label>
            <input type="email" name="email" required class="p-2 border rounded-md mb-4 focus:outline-none focus:ring-2 focus:ring-[#86d5f8]">
            
            <label class="text-sm text-gray-600 mb-1">Password</label>
            <input type="password" name="password" required class="p-2 border rounded-md mb-4 focus:outline-none focus:ring-2 focus:ring-[#86d5f8]">
            
            <label class="text-sm text-gray-600 mb-1">Confirm Password</label>
            <input type="password" name="confirm_password" required class="p-2 border rounded-md mb-4 focus:outline-none focus:ring-2 focus:ring-[#86d5f8]">
            
            <div class="g-recaptcha" data-sitekey="6Lfl4msqAAAAAPaz2uVIgU_4Ezookz_92TcyiFo5"></div>
            
            <input type="submit" value="Register" class="bg-[#86d5f8] text-white rounded-md p-2 w-full hover-normal cursor-pointer">
        </form>
        <div class="text-center mt-4">
            <a href="javascript:void(0);" onclick="switchMode('?mode=login')" class="text-sm text-blue-600 hover:underline">Already have an account? Login here</a>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
