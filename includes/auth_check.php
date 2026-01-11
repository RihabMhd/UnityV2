<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../router.php';

if (!$router->isAuthenticated()) {
    $router->redirectToLogin();
    exit();
}

$current_user_id = $_SESSION['user_id'] ?? null;
$current_username = $_SESSION['username'] ?? null;
$current_role = $_SESSION['role'] ?? null;
$current_email = $_SESSION['email'] ?? '';

if (!$current_user_id || !$current_username || !$current_role) {
    session_destroy();
    header("Location: /UnityV2/public/auth/login.php");
    exit();
}
?>