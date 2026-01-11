<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Repositories\AppointmentRepository;
use Config\Database;

session_start();
header('Content-Type: application/json');

// Allow both admin and doctor to delete appointments
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'doctor'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['appointment_id'])) {
        echo json_encode(['success' => false, 'message' => 'Missing appointment ID']);
        exit;
    }
    
    $appointmentId = (int)$input['appointment_id'];
    
    $db = (new Database)->connect();
    $appointmentRepo = new AppointmentRepository($db);
    
    $appointment = $appointmentRepo->findById($appointmentId);
    if (!$appointment) {
        echo json_encode(['success' => false, 'message' => 'Appointment not found']);
        exit;
    }
    
    // If user is a doctor, verify they own this appointment
    if ($_SESSION['role'] === 'doctor') {
        $doctorRepo = new \Repositories\DoctorRepository($db);
        $doctor = $doctorRepo->findById($_SESSION['user_id']);
        
        if (!$doctor || $appointment->getDoctorId() !== $doctor->getDoctorId()) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized to delete this appointment']);
            exit;
        }
    }
    
    if ($appointmentRepo->delete($appointmentId)) {
        echo json_encode(['success' => true, 'message' => 'Appointment deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete appointment']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}