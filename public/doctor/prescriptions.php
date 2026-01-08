<?php
$page_title = "Doctor Dashboard";
require_once '../../includes/auth_check.php';
$router->requireRole('doctor');
require_once '../../includes/header.php';
require_once '../../includes/navbar.php';
?>



<?php require_once '../../includes/footer.php'; ?>