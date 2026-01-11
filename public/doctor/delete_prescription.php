<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Repositories\PrescriptionRepository;
use Repositories\DoctorRepository;
use Config\Database;

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['prescription_id'])) {
        echo json_encode(['success' => false, 'message' => 'Missing prescription ID']);
        exit;
    }
    
    $prescriptionId = (int)$input['prescription_id'];
    
    $db = (new Database)->connect();
    $prescriptionRepo = new PrescriptionRepository($db);
    $doctorRepo = new DoctorRepository($db);
    
    $currentDoctor = $doctorRepo->findById($_SESSION['user_id']);
    
    if (!$currentDoctor) {
        echo json_encode(['success' => false, 'message' => 'Doctor profile not found']);
        exit;
    }
    
    $prescription = $prescriptionRepo->findById($prescriptionId);
    if (!$prescription) {
        echo json_encode(['success' => false, 'message' => 'Prescription not found']);
        exit;
    }
    
    // Verify this doctor owns the prescription
    if ($prescription->getDoctorId() !== $currentDoctor->getDoctorId()) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized to delete this prescription']);
        exit;
    }
    
    if ($prescriptionRepo->delete($prescriptionId)) {
        echo json_encode(['success' => true, 'message' => 'Prescription deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete prescription']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}