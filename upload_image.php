<?php
require_once('db_fns.php');
require_once('session_fns.php');

session_start();
check_valid_user(); // Ensure the user is logged in

$user_id = $_SESSION['user_id']; // Get the user ID from the session

// Check if an image has been uploaded
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['profile_image'])) {
    // Get image details
    $image = $_FILES['profile_image'];
    $image_name = $image['name'];
    $image_tmp_name = $image['tmp_name'];
    $image_size = $image['size'];
    $image_error = $image['error'];

    // Allowed file extensions and max file size (2MB)
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
    $max_size = 2 * 1024 * 1024; // 2MB

    // Get image extension
    $image_ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));

    // Validate file extension and size
    if (in_array($image_ext, $allowed_extensions) && $image_size <= $max_size) {
        if ($image_error === 0) {
            // Set unique name for the image
            $new_image_name = "profile_" . $user_id . "." . $image_ext;
            $upload_dir = 'uploads/profile_images/';
            $upload_path = $upload_dir . $new_image_name;

            // Create upload directory if it doesn't exist
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            // Move the uploaded image to the server directory
            if (move_uploaded_file($image_tmp_name, $upload_path)) {
                // Update the user's profile in the database
                $conn = db_connect();
                $stmt = $conn->prepare("UPDATE users SET profile_image = ? WHERE user_id = ?");
                $stmt->bind_param('si', $new_image_name, $user_id);
                $stmt->execute();

                if ($stmt->affected_rows > 0) {
                    echo "Image uploaded successfully. <a href='profile.php'>Go to profile</a>";
                } else {
                    echo "Failed to update profile image in database.";
                }

                $stmt->close();
                $conn->close();
            } else {
                echo "Failed to upload image.";
            }
        } else {
            echo "Error uploading image: " . $image_error;
        }
    } else {
        echo "Invalid file type or file is too large (max 2MB).";
    }
} else {
    echo "No image uploaded.";
}
