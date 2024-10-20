<?php
require_once('session_fns.php'); // Include the session functions file
session_start(); // Start the session
logout(); // Call the logout function
header('Location: index.php'); // Redirect to the login page
exit(); // Ensure no further code is executed after the redirect
?>
