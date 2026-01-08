<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Repositories\DepartmentRepository;
use Models\Department;
use Config\Database;

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: departments.php');
    exit;
}

try {
    $db = (new Database)->connect();
    $departmentRepo = new DepartmentRepository($db);
    
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        $department = new Department();
        $department->setDepartmentName($_POST['department_name']);
        $department->setDescription($_POST['description'] ?? null);
        $department->setContactNumber($_POST['contact_number'] ?? null);
        $department->setEmail($_POST['email'] ?? null);
        $department->setLocation($_POST['location'] ?? null);
        
        if ($departmentRepo->create($department)) {
            $_SESSION['success'] = 'Department added successfully!';
        } else {
            $_SESSION['error'] = 'Failed to add department.';
        }
        
    } elseif ($action === 'edit') {
        $departmentId = (int)$_POST['department_id'];
        $department = $departmentRepo->findById($departmentId);
        
        if (!$department) {
            $_SESSION['error'] = 'Department not found.';
            header('Location: departments.php');
            exit;
        }
        
        $department->setDepartmentName($_POST['department_name']);
        $department->setDescription($_POST['description'] ?? null);
        $department->setContactNumber($_POST['contact_number'] ?? null);
        $department->setEmail($_POST['email'] ?? null);
        $department->setLocation($_POST['location'] ?? null);
        
        if ($departmentRepo->update($department)) {
            $_SESSION['success'] = 'Department updated successfully!';
        } else {
            $_SESSION['error'] = 'Failed to update department.';
        }
    }
    
} catch (Exception $e) {
    $_SESSION['error'] = 'Error: ' . $e->getMessage();
}

header('Location: departments.php');
exit;