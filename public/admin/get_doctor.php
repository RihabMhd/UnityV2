<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Repositories\DoctorRepository;
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
    $doctorRepo = new DoctorRepository($db);
    
    $doctor = $doctorRepo->findById((int)$_GET['id']);
    
    if (!$doctor) {
        echo json_encode(['error' => 'Doctor not found']);
        exit;
    }
    
    echo json_encode([
        'doctor_id' => $doctor->getDoctorId(),
        'first_name' => $doctor->getFirstName(),
        'last_name' => $doctor->getLastName(),
        'specialization' => $doctor->getSpecialization(),
        'phone_number' => $doctor->getPhoneNumber(),
        'email' => $doctor->getEmail(),
        'department_id' => $doctor->getDepartmentId(),
        'username' => $doctor->getUsername()
    ]);
    
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}