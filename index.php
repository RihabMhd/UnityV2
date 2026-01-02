<?php

require_once __DIR__ . '/vendor/autoload.php';

session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: public/auth/login.php");
    exit();
}

switch ($_SESSION['role']) {
    case 'admin':
        header("Location: public/admin/index.php");
        break;
    
    case 'doctor':
        header("Location: public/doctor/index.php");
        break;
    
    case 'patient':
        header("Location: public/patient/index.php");
        break;
    
    default:
        session_destroy();
        header("Location: public/auth/login.php");
        break;
}
exit();
?>