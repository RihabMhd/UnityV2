<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- dataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- dataTables JS -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Hospital Management System</title>
    <link rel="stylesheet" href="./assets/css/headStyle.css">
    <link rel="stylesheet" href="./assets/css/listStyle.css">
</head>

<body>
    <?php
    $currentController = isset($_GET['controller']) ? $_GET['controller'] : 'dashboard';
    ?>
    
    <div class="sidebar">
        <div class="sidebar-header">
            <h1><i class="fa-solid fa-house-medical-flag"></i>&nbsp;Unity Care</h1>
            <p>Hospital Management</p>
        </div>
        <div class="sidebar-menu">
            <div class="menu-section">
                <div class="menu-section-title">Main</div>
                <a href="index.php?controller=dashboard" class="icon-dashboard <?php echo ($currentController == 'dashboard') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-chart-line"></i>
                    <span>Dashboard</span>
                </a>
            </div>

            <div class="menu-section">
                <div class="menu-section-title">Patients</div>
                <a href="index.php?controller=patients" class="<?php echo ($currentController == 'patients') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-hospital-user"></i>
                    <span>All Patients</span>
                </a>
            </div>

            <div class="menu-section">
                <div class="menu-section-title">Doctors</div>
                <a href="index.php?controller=doctors" class="<?php echo ($currentController == 'doctors') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-user-doctor"></i>
                    <span>All Doctors</span>
                </a>
            </div>

            <div class="menu-section">
                <div class="menu-section-title">Departments</div>
                <a href="index.php?controller=departments" class="<?php echo ($currentController == 'departments') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-hospital"></i>
                    <span>All Departments</span>
                </a>
            </div>
        </div>
    </div>

    <div class="main-content">
        <div class="top-bar">
            <h2>
                Hospital Management
            </h2>
            <div class="user-info">
                <div class="user-avatar">A</div>
                <span>Admin</span>
            </div>
        </div>
        <div class="content">