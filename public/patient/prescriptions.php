<?php
$page_title = "Patient Dashboard";
require_once '../../includes/auth_check.php';
$router->requireRole('patient');
require_once '../../includes/header.php';
require_once '../../includes/navbar.php';
?>



<?php require_once '../../includes/footer.php'; ?>