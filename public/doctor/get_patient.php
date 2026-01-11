<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Repositories\PatientRepository;
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
    $patientRepo = new PatientRepository($db);
    
    $patient = $patientRepo->findById((int)$_GET['id']);
    
    if (!$patient) {
        echo json_encode(['error' => 'Patient not found']);
        exit;
    }
    
    echo json_encode([
        'patient_id' => $patient->getPatientId(),
        'first_name' => $patient->getFirstName(),
        'last_name' => $patient->getLastName(),
        'date_of_birth' => $patient->getDateOfBirth(),
        'phone_number' => $patient->getPhoneNumber(),
        'email' => $patient->getEmail(),
        'address' => $patient->getAddress(),
        'username' => $patient->getUsername()
    ]);
    
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}