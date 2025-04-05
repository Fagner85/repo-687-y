<?php
require_once __DIR__ . '/config.php';

// Define authentication functions only if they don't exist
if (!function_exists('isLoggedIn')) {
    function isLoggedIn() {
        return !empty($_SESSION['user_id']);
    }
}

if (!function_exists('isAdmin')) {
    function isAdmin() {
        return isLoggedIn() && ($_SESSION['role'] ?? '') === 'admin';
    }
}

if (!function_exists('isApplicant')) {
    function isApplicant() {
        return isLoggedIn() && ($_SESSION['role'] ?? '') === 'applicant';
    }
}

// Helper function to safely check auth status
function checkAuth() {
    if (!function_exists('isLoggedIn') || !function_exists('isAdmin') || !function_exists('isApplicant')) {
        require_once __DIR__ . '/autoload.php';
    }
}
?>