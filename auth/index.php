<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

// Redirect to login page by default
header('Location: login.php');
exit();
?>