<?php 
require_once __DIR__ . '/../config/lang.php';
?>
<!DOCTYPE html>
<html lang="<?php echo getCurrentLang(); ?>" dir="<?php echo getCurrentLang() === 'ar' ? 'rtl' : 'ltr'; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title><?php echo __('Hospital Management'); ?></title>
    <link rel="stylesheet" href="./assets/css/headStyle.css">
    <link rel="stylesheet" href="./assets/css/listStyle.css">
    <?php if (getCurrentLang() === 'ar'): ?>
    <style>
        body { font-family: 'Arial', 'Tahoma', sans-serif; }
        .sidebar { right: 0; left: auto; }
        .main-content { margin-right: 250px; margin-left: 0; }
    </style>
    <?php endif; ?>
</head>

<body>
    <?php
    $currentController = isset($_GET['controller']) ? $_GET['controller'] : 'dashboard';
    ?>
    
    <div class="sidebar">
        <div class="sidebar-header">
            <h1><i class="fa-solid fa-house-medical-flag"></i>&nbsp;<?php echo __('Unity Care'); ?></h1>
            <p><?php echo __('Hospital Management'); ?></p>
        </div>
        <div class="sidebar-menu">
            <div class="menu-section">
                <div class="menu-section-title"><?php echo __('Main'); ?></div>
                <a href="index.php?controller=dashboard" class="icon-dashboard <?php echo ($currentController == 'dashboard') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-chart-line"></i>
                    <span><?php echo __('Dashboard'); ?></span>
                </a>
            </div>

            <div class="menu-section">
                <div class="menu-section-title"><?php echo __('Patients'); ?></div>
                <a href="index.php?controller=patients" class="<?php echo ($currentController == 'patients') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-hospital-user"></i>
                    <span><?php echo __('All Patients'); ?></span>
                </a>
            </div>

            <div class="menu-section">
                <div class="menu-section-title"><?php echo __('Doctors'); ?></div>
                <a href="index.php?controller=doctors" class="<?php echo ($currentController == 'doctors') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-user-doctor"></i>
                    <span><?php echo __('All Doctors'); ?></span>
                </a>
            </div>

            <div class="menu-section">
                <div class="menu-section-title"><?php echo __('Departments'); ?></div>
                <a href="index.php?controller=departments" class="<?php echo ($currentController == 'departments') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-hospital"></i>
                    <span><?php echo __('All Departments'); ?></span>
                </a>
            </div>
        </div>
    </div>

    <div class="main-content">
        <div class="top-bar">
            <h2>
                <?php echo __('Hospital Management'); ?>
            </h2>
            <div style="display: flex; align-items: center; gap: 15px;">
                <div class="language-switcher">
                    <a href="?<?php echo http_build_query(array_merge($_GET, ['lang' => 'en'])); ?>" 
                       class="<?php echo getCurrentLang() === 'en' ? 'active' : ''; ?>" 
                       title="English">EN</a>
                    <a href="?<?php echo http_build_query(array_merge($_GET, ['lang' => 'fr'])); ?>" 
                       class="<?php echo getCurrentLang() === 'fr' ? 'active' : ''; ?>" 
                       title="Français">FR</a>
                    <a href="?<?php echo http_build_query(array_merge($_GET, ['lang' => 'ar'])); ?>" 
                       class="<?php echo getCurrentLang() === 'ar' ? 'active' : ''; ?>" 
                       title="العربية">AR</a>
                </div>
                
                <div class="user-info">
                    <div class="user-avatar">A</div>
                    <span><?php echo __('Admin'); ?></span>
                </div>
            </div>
        </div>
        <div class="content">