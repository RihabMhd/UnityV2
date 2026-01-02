<?php

require_once __DIR__ . '/../router.php';

if (!$router->isAuthenticated()) {
    $router->redirectToLogin();
}


$current_user_id = $_SESSION['user_id'];
$current_username = $_SESSION['username'];
$current_role = $_SESSION['role'];
$current_email = $_SESSION['email'] ?? '';
?>