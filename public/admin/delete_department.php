<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Repositories\DepartmentRepository;
use Config\Database;

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['department_id'])) {
        echo json_encode(['success' => false, 'message' => 'Missing department ID']);
        exit;
    }
    
    $departmentId = (int)$input['department_id'];
    
    $db = (new Database)->connect();
    $departmentRepo = new DepartmentRepository($db);
    
    $department = $departmentRepo->findById($departmentId);
    if (!$department) {
        echo json_encode(['success' => false, 'message' => 'Department not found']);
        exit;
    }
    
    if ($departmentRepo->delete($departmentId)) {
        echo json_encode(['success' => true, 'message' => 'Department deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete department']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}