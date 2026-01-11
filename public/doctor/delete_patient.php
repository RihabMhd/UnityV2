<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Repositories\PatientRepository;
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
    
    if (!isset($input['patient_id'])) {
        echo json_encode(['success' => false, 'message' => 'Missing patient ID']);
        exit;
    }
    
    $patientId = (int)$input['patient_id'];
    
    $db = (new Database)->connect();
    $patientRepo = new PatientRepository($db);
    $userRepo = new UserRepository($db);
    
    $patient = $patientRepo->findById($patientId);
    if (!$patient) {
        echo json_encode(['success' => false, 'message' => 'Patient not found']);
        exit;
    }
    
    $db->beginTransaction();
    
    try {
        if ($patientRepo->delete($patientId)) {
            if ($userRepo->delete($patient->getPatientId())) {
                $db->commit();
                echo json_encode(['success' => true, 'message' => 'Patient deleted successfully']);
            } else {
                $db->rollBack();
                echo json_encode(['success' => false, 'message' => 'Failed to delete user account']);
            }
        } else {
            $db->rollBack();
            echo json_encode(['success' => false, 'message' => 'Failed to delete patient']);
        }
    } catch (Exception $e) {
        $db->rollBack();
        throw $e;
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}