<?php
require_once('db_fns.php');
require_once('session_fns.php');

session_start();

check_valid_user();

$user_id = $_SESSION['user_id'];

if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = 'uploads/profile_images/';
    $file_tmp = $_FILES['profile_image']['tmp_name'];
    $file_name = basename($_FILES['profile_image']['name']);
    $upload_path = $upload_dir . $file_name;

    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
    $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    $max_file_size = 2 * 1024 * 1024; 

    if ($file_name === 'default.png') {
        die("Please rename your file.");
    }

    if (!in_array($file_extension, $allowed_extensions)) {
        die("Invalid file type. Only JPG, PNG, and GIF are allowed.");
    }

    if ($_FILES['profile_image']['size'] > $max_file_size) {
        die("File size exceeds 2 MB limit.");
    }

    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    if (move_uploaded_file($file_tmp, $upload_path)) {
        if (update_profile_image($user_id, $file_name)) {
            echo "Profile image uploaded successfully. <a href='profile.php'>View Profile</a>";
        } else {
            echo "Failed to update profile image in the database.";
        }
    } else {
        echo "Failed to move uploaded file.";
    }
} else {
    echo "File upload error: " . $_FILES['profile_image']['error'];
}

function update_profile_image($user_id, $file_name) {
    $conn = db_connect();
    $stmt = $conn->prepare("UPDATE users SET profile_image = ? WHERE user_id = ?");
    $stmt->bind_param('si', $file_name, $user_id);
    $success = $stmt->execute();

    $stmt->close();
    $conn->close();

    return $success;
}
?>
