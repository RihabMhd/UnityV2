<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Hospital Management System'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #F1F0CC;
            overflow-x: hidden;
        }

        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            width: 260px;
            background: linear-gradient(180deg, #3F0D12 0%, #A71D31 100%);
            padding: 20px 0;
            transition: all 0.3s;
            z-index: 1000;
            overflow-y: auto;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }

        .sidebar::-webkit-scrollbar {
            width: 5px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: rgba(255,255,255,0.1);
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.3);
            border-radius: 10px;
        }

        .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 20px;
        }

        .sidebar-header i {
            font-size: 2.5rem;
            color: #D5BF86;
            margin-bottom: 10px;
        }

        .sidebar-header h4 {
            color: #fff;
            font-size: 1.1rem;
            font-weight: 600;
            margin: 0;
        }

        .sidebar-menu {
            list-style: none;
            padding: 0 10px;
        }

        .sidebar-menu li {
            margin-bottom: 5px;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: #D5BF86;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s;
            font-size: 0.95rem;
        }

        .sidebar-menu a:hover {
            background: rgba(255,255,255,0.1);
            color: #fff;
            transform: translateX(5px);
        }

        .sidebar-menu a.active {
            background: #A71D31;
            color: #fff;
        }

        .sidebar-menu a i {
            width: 25px;
            margin-right: 15px;
            font-size: 1.1rem;
        }

        /* User Profile in Sidebar */
        .sidebar-user {
            padding: 15px 20px;
            margin: 20px 10px;
            background: rgba(255,255,255,0.05);
            border-radius: 8px;
            border-top: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-user-info {
            display: flex;
            align-items: center;
            color: #fff;
            margin-bottom: 10px;
        }

        .sidebar-user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #8D775F;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            font-size: 1.2rem;
            color: #fff;
        }

        .sidebar-user-details h6 {
            margin: 0;
            font-size: 0.95rem;
            color: #fff;
        }

        .sidebar-user-details small {
            color: #D5BF86;
            font-size: 0.8rem;
        }

        .sidebar-user-actions {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        .sidebar-user-actions a {
            flex: 1;
            padding: 8px;
            text-align: center;
            background: rgba(255,255,255,0.1);
            color: #D5BF86;
            text-decoration: none;
            border-radius: 5px;
            font-size: 0.85rem;
            transition: all 0.3s;
        }

        .sidebar-user-actions a:hover {
            background: rgba(255,255,255,0.2);
            color: #fff;
        }

        /* Main Content */
        .main-content {
            margin-left: 260px;
            min-height: 100vh;
            background-color: #F1F0CC;
            transition: all 0.3s;
        }

        /* Top Bar */
        .top-bar {
            background: #fff;
            padding: 15px 30px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .top-bar h5 {
            margin: 0;
            color: #3F0D12;
            font-weight: 600;
        }

        .top-bar-actions {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .top-bar-actions .btn {
            padding: 8px 15px;
            font-size: 0.9rem;
        }

        /* Page Content */
        .page-content {
            padding: 30px;
        }

        /* Footer */
        .footer {
            background: #3F0D12;
            color: #D5BF86;
            text-align: center;
            padding: 20px;
            margin-left: 260px;
        }

        .footer a {
            color: #D5BF86;
            text-decoration: none;
        }

        .footer a:hover {
            color: #fff;
        }

        /* Mobile Toggle */
        .mobile-toggle {
            display: none;
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 1001;
            background: #A71D31;
            color: #fff;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                left: -260px;
            }

            .sidebar.active {
                left: 0;
            }

            .main-content {
                margin-left: 0;
            }

            .footer {
                margin-left: 0;
            }

            .mobile-toggle {
                display: block;
            }

            .page-content {
                padding: 20px 15px;
            }
        }

        /* Card Styles */
        .dashboard-card {
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }

        .stat-card {
            background: linear-gradient(135deg, #8D775F 0%, #D5BF86 100%);
            color: #fff;
            border-radius: 10px;
            padding: 25px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .stat-card i {
            font-size: 2.5rem;
            margin-bottom: 15px;
            opacity: 0.9;
        }

        .stat-card h3 {
            font-size: 2rem;
            margin: 10px 0;
            font-weight: 700;
        }

        .stat-card p {
            margin: 0;
            font-size: 0.95rem;
            opacity: 0.9;
        }
    </style>
    
    <?php if (isset($extra_css)): ?>
        <?php foreach ($extra_css as $css): ?>
            <link rel="stylesheet" href="<?php echo $css; ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
    <button class="mobile-toggle" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </button>