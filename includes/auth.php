<?php
require_once __DIR__ . '/config.php';

if (!function_exists('loginUser')) {
    function loginUser($email, $password) {
        global $pdo;
        
        try {
            $stmt = $pdo->prepare("SELECT id, email, password, role FROM users WHERE email = ? LIMIT 1");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['loggedin'] = true;
                return true;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('logoutUser')) {
    function logoutUser() {
        $_SESSION = array();
        session_destroy();
    }
}
?>