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
        echo "<script>alert('Please rename your file.'); window.history.back();</script>";
        exit;
    }

    if (!in_array($file_extension, $allowed_extensions)) {
        echo "<script>alert('Invalid file type. Only JPG, PNG, and GIF are allowed.'); window.history.back();</script>";
        exit;
    }

    if ($_FILES['profile_image']['size'] > $max_file_size) {
        echo "<script>alert('File size exceeds 2 MB limit.'); window.history.back();</script>";
        exit;
    }

    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    if (move_uploaded_file($file_tmp, $upload_path)) {
        if (update_profile_image($user_id, $file_name)) {
            echo "<script>alert('Profile image uploaded successfully.'); window.location.href='profile.php';</script>";
        } else {
            echo "<script>alert('Failed to update profile image in the database.'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Failed to move uploaded file.'); window.history.back();</script>";
    }
} else {
    echo "<script>alert('File upload error: " . $_FILES['profile_image']['error'] . "'); window.history.back();</script>";
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
