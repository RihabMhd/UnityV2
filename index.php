<?php

require_once 'config/database.php';

require_once 'controllers/patients/PatientsController.php';
require_once 'controllers/departments/DepartmentsController.php';
require_once 'controllers/doctors/DoctorsController.php';

$database = new Database();
$db = $database->connect();

$controllerName = isset($_GET['controller']) ? $_GET['controller'] : 'patients';
$action = isset($_GET['action']) ? $_GET['action'] : 'index';
$id = isset($_GET['id']) ? intval($_GET['id']) : null;

// error_log($_GET['controller'],3, "file.log");m
switch ($controllerName) {
    case 'patients':
        $controller = new \Controllers\PatientController($db);
        break;

    case 'departments':
        $controller = new \Controllers\DepartmentController($db);
        break;

    case 'doctors':
        $controller = new \Controllers\DoctorsController($db);
        break;

    default:
        $controller = new \Controllers\PatientController($db);
        break;
}

switch ($action) {
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
