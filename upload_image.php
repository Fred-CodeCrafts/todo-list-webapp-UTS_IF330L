<?php
require_once('db_fns.php');
require_once('session_fns.php');

session_start();

// Check if the user is logged in
check_valid_user();

$user_id = $_SESSION['user_id'];

// Check if the form was submitted and file was uploaded
if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = 'uploads/profile_images/';
    $file_tmp = $_FILES['profile_image']['tmp_name'];
    $file_name = basename($_FILES['profile_image']['name']);
    $upload_path = $upload_dir . $file_name;

    // Move the uploaded file to the desired location
    if (move_uploaded_file($file_tmp, $upload_path)) {
        // Update the user's profile image in the database
        if (update_profile_image($user_id, $file_name)) {
            echo "Profile image uploaded successfully. <a href='profile.php'>View Profile</a>";
        } else {
            echo "Failed to update profile image in the database.";
        }
    } else {
        echo "Failed to move uploaded file.";
    }
} else {
    echo "File upload error.";
}

// Function to update the profile image in the database
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
