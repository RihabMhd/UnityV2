<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Repositories\DepartmentRepository;
use Config\Database;

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'ID required']);
    exit;
}

try {
    $db = (new Database)->connect();
    $departmentRepo = new DepartmentRepository($db);
    
    $department = $departmentRepo->findById((int)$_GET['id']);
    
    if (!$department) {
        echo json_encode(['error' => 'Department not found']);
        exit;
    }
    
    echo json_encode([
        'department_id' => $department->getDepartmentId(),
        'department_name' => $department->getDepartmentName(),
        'description' => $department->getDescription(),
        'contact_number' => $department->getContactNumber(),
        'email' => $department->getEmail(),
        'location' => $department->getLocation()
    ]);
    
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}