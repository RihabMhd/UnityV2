<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Repositories\AppointmentRepository;
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
    
    if ($appointmentRepo->delete($appointmentId)) {
        echo json_encode(['success' => true, 'message' => 'Appointment deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete appointment']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}