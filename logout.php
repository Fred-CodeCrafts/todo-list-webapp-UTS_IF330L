<?php
require_once('session_fns.php'); 
session_start(); 
logout(); 
header('Location: index.php'); 
exit(); 
?>
