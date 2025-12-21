<?php
session_start();

require_once 'config/lang.php';

require_once 'config/database.php';

require_once 'controllers/patients/PatientsController.php';
require_once 'controllers/departments/DepartmentsController.php';
require_once 'controllers/doctors/DoctorsController.php';
require_once 'controllers/dashboard/DashboardController.php';
require_once 'controllers/auth/AuthController.php';

$database = new Database();
$db = $database->connect();

$controllerName = isset($_GET['controller']) ? $_GET['controller'] : 'dashboard';
$action = isset($_GET['action']) ? $_GET['action'] : 'index';
$id = isset($_GET['id']) ? intval($_GET['id']) : null;

if ($controllerName !== 'auth' && !isset($_SESSION['logged_in'])) {
    header('Location: index.php?controller=auth&action=login');
    exit;
}

if ($controllerName === 'auth' && $action === 'login' && isset($_SESSION['logged_in'])) {
    header('Location: index.php?controller=dashboard');
    exit;
}

switch ($controllerName) {
    case 'auth':
        $controller = new \Controllers\AuthController($db);
        break;

    case 'patients':
        $controller = new \Controllers\PatientController($db);
        break;

    case 'departments':
        $controller = new \Controllers\DepartmentController($db);
        break;

    case 'doctors':
        $controller = new \Controllers\DoctorsController($db);
        break;

    case 'dashboard':
        $controller = new \Controllers\DashboardController($db);
        break;

    default:
        $controller = new \Controllers\DashboardController($db);
        break;
}

switch ($action) {
    case 'login':
        if ($controllerName === 'auth') {
            $controller->login();
        }
        break;

    case 'logout':
        if ($controllerName === 'auth') {
            $controller->logout();
        }
        break;

    case 'create':
        $controller->create();
        break;

    case 'edit':
        if ($id) {
            $controller->edit($id);
        } else {
            header("Location: index.php?controller=$controllerName");
            exit;
        }
        break;

    case 'delete':
        if ($id) {
            $controller->delete($id);
        } else {
            header("Location: index.php?controller=$controllerName");
            exit;
        }
        break;

    case 'index':
    default:
        $controller->index();
        break;
}