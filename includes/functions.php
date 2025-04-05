<?php
if (!function_exists('checkAuth')) {
    function checkAuth() {
        // Ensure session is started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Verify required session variables exist
        if (empty($_SESSION['user_id']) || empty($_SESSION['role'])) {
            return false;
        }
        return true;
    }
}

if (!function_exists('isLoggedIn')) {
    function isLoggedIn() {
        return checkAuth();
    }
}

if (!function_exists('getUserId')) {
    function getUserId() {
        return $_SESSION['user_id'] ?? null;
    }
}

if (!function_exists('isAdmin')) {
    function isAdmin() {
        return checkAuth() && $_SESSION['role'] === 'admin';
    }
}

if (!function_exists('isApplicant')) {
    function isApplicant() {
        return checkAuth() && $_SESSION['role'] === 'applicant';
    }
}

if (!function_exists('redirect')) {
    function redirect($url) {
        header("Location: $url");
        exit();
    }
}

// Define the function to get the applicant's profile
function getApplicantProfile($userId, $pdo) {
    $stmt = $pdo->prepare("SELECT id, email,first_name,last_name, created_at FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    return $stmt->fetch();

}
?>