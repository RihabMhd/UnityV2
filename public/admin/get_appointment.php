<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Repositories\AppointmentRepository;
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
    $appointmentRepo = new AppointmentRepository($db);
    
    $appointment = $appointmentRepo->findById((int)$_GET['id']);
    
    if (!$appointment) {
        echo json_encode(['error' => 'Appointment not found']);
        exit;
    }
    
    echo json_encode([
        'appointment_id' => $appointment->getAppointmentId(),
        'patient_id' => $appointment->getPatientId(),
        'doctor_id' => $appointment->getDoctorId(),
        'appointment_date' => $appointment->getAppointmentDate(),
        'appointment_time' => $appointment->getAppointmentTime(),
        'reason' => $appointment->getReason(),
        'status' => $appointment->getStatus(),
        'notes' => $appointment->getNotes()
    ]);
    
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}