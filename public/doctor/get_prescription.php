<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Repositories\PrescriptionRepository;
use Repositories\DoctorRepository;
use Config\Database;

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'ID required']);
    exit;
}

try {
    $db = (new Database)->connect();
    $prescriptionRepo = new PrescriptionRepository($db);
    $doctorRepo = new DoctorRepository($db);
    
    $currentDoctor = $doctorRepo->findById($_SESSION['user_id']);
    
    if (!$currentDoctor) {
        echo json_encode(['error' => 'Doctor profile not found']);
        exit;
    }
    
    $prescription = $prescriptionRepo->findById((int)$_GET['id']);
    
    if (!$prescription) {
        echo json_encode(['error' => 'Prescription not found']);
        exit;
    }
    
    // Verify this doctor owns the prescription
    if ($prescription->getDoctorId() !== $currentDoctor->getDoctorId()) {
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }
    
    echo json_encode([
        'prescription_id' => $prescription->getPrescriptionId(),
        'patient_id' => $prescription->getPatientId(),
        'doctor_id' => $prescription->getDoctorId(),
        'medication_id' => $prescription->getMedicationId(),
        'prescription_date' => $prescription->getPrescriptionDate(),
        'dosage_instructions' => $prescription->getDosageInstructions()
    ]);
    
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}