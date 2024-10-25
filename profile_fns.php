<?php
require_once('db_fns.php');

function get_user_profile($user_id) {
    $conn = db_connect();
    $stmt = $conn->prepare("SELECT username, email, profile_image FROM users WHERE user_id = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $profile = null;

    if ($result->num_rows === 1) {
        $profile = $result->fetch_assoc();
    }

    $stmt->close();
    $conn->close();

    return $profile;
}

function update_user_profile($user_id, $username, $email) {
    $conn = db_connect();
    $stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE user_id = ?");
    $stmt->bind_param('ssi', $username, $email, $user_id);
    $success = $stmt->execute();

    $stmt->close();
    $conn->close();

    return $success;
}

function change_password($username, $old_password, $new_password) {
    $conn = db_connect();

    $stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception('User not found.');
    }

    $row = $result->fetch_assoc();
    $hashed_password = $row['password'];

    if (!password_verify($old_password, $hashed_password)) {
        echo "<script>alert('Old password is incorrect.'); window.history.back();</script>";
        exit();
    }
    
    $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE username = ?");
    $update_stmt->bind_param("ss", $new_hashed_password, $username);

    if (!$update_stmt->execute()) {
        throw new Exception('Password could not be changed.');
    }

    return true; 
}

function upload_profile_image($user_id, $file) {
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
    $upload_dir = 'uploads/profile_images/'; 
    $max_file_size = 2 * 1024 * 1024; 

    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Error during file upload.');
    }

    if ($file['size'] > $max_file_size) {
        throw new Exception('File size exceeds 2 MB.');
    }

    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($file_extension, $allowed_extensions)) {
        throw new Exception('Invalid file type. Only JPG, PNG, and GIF are allowed.');
    }

    $file_name = uniqid('profile_', true) . '.' . $file_extension;
    $file_path = $upload_dir . $file_name;

    if (!move_uploaded_file($file['tmp_name'], $file_path)) {
        throw new Exception('Failed to move uploaded file.');
    }

    $conn = db_connect();
    $stmt = $conn->prepare("UPDATE users SET profile_image = ? WHERE user_id = ?");
    $stmt->bind_param('si', $file_name, $user_id);
    $success = $stmt->execute();

    $stmt->close();
    $conn->close();

    return $success;
}
?>
