<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Repositories\DoctorRepository;
use Repositories\UserRepository;
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
    
    if (!isset($input['doctor_id'])) {
        echo json_encode(['success' => false, 'message' => 'Missing doctor ID']);
        exit;
    }
    
    $doctorId = (int)$input['doctor_id'];
    
    $db = (new Database)->connect();
    $doctorRepo = new DoctorRepository($db);
    $userRepo = new UserRepository($db);
    
    $doctor = $doctorRepo->findById($doctorId);
    if (!$doctor) {
        echo json_encode(['success' => false, 'message' => 'Doctor not found']);
        exit;
    }
    
    $db->beginTransaction();
    
    try {
        if ($doctorRepo->delete($doctorId)) {
            if ($userRepo->delete($doctor->getId())) {
                $db->commit();
                echo json_encode(['success' => true, 'message' => 'Doctor deleted successfully']);
            } else {
                $db->rollBack();
                echo json_encode(['success' => false, 'message' => 'Failed to delete user account']);
            }
        } else {
            $db->rollBack();
            echo json_encode(['success' => false, 'message' => 'Failed to delete doctor']);
        }
    } catch (Exception $e) {
        $db->rollBack();
        throw $e;
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}