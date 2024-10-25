<?php
require 'vendor/PHPMailer/src/Exception.php';
require 'vendor/PHPMailer/src/PHPMailer.php';
require 'vendor/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

try {
    $pdo = new PDO('mysql:host=localhost;dbname=hary8495_todo_app', 'hary8495_alenajadeh', 'alenajadeh');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

function generateRandomToken($length = 6) {
    $token = '';
    for ($i = 0; $i < $length; $i++) {
        $token .= mt_rand(0, 9); 
    return $token;
}

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = isset($_POST["email"]) ? trim($_POST["email"]) : '';
    $username = isset($_POST["username"]) ? trim($_POST["username"]) : '';
    if (empty($email) || empty($username)) {
        $error = "Please fill in both email and username fields.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND username = ?");
        $stmt->execute([$email, $username]);
        $user = $stmt->fetch();

        if (!$user) {
            $error = "No account found with that username and email combination. Please check your details and try again.";
        } else {
            $newPassword = generateRandomToken(); 
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE username = ? AND email = ?");
            $stmt->execute([$hashedPassword, $username, $email]);
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = "smtp.gmail.com";
                $mail->SMTPAuth = true;
                $mail->Username = "Fg.cygnus468@gmail.com"; 
                $mail->Password = "cgln mzwz wzfv tqzn"; 
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                $mail->setFrom("fg.cygnus468@gmail.com", "Event Website");
                $mail->addAddress($email);

               
                $mail->isHTML(true);
                $mail->Subject = "Your Temporary Password";
                $mail->Body = "Your password has been reset. Here is your new password: <strong>$newPassword</strong><br>
                               Please log in using this password and change it manually through your profile page after logging in.";

                $mail->send();
                $success = "A new password has been sent to your email. Please log in and change it manually through your profile page.";
            } catch (Exception $e) {
                $error = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 h-screen flex items-center justify-center">

<div class="container mx-auto max-w-md p-6 bg-white rounded-lg shadow-lg">
    <h2 class="text-center text-2xl font-semibold mb-6 text-gray-800">Password Reset Status</h2>
    <div class="text-center mt-4">
        <?php if ($error): ?>
            <div class="text-red-600"><?= htmlspecialchars($error) ?></div>
        <?php elseif ($success): ?>
            <div class="text-green-600"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <a href="index.php" class="text-sm text-blue-600 hover:underline">Back to Login</a>
    </div>
</div>

</body>
</html>
