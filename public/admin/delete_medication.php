<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Repositories\MedicationRepository;
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
    
    if (!isset($input['medication_id'])) {
        echo json_encode(['success' => false, 'message' => 'Missing medication ID']);
        exit;
    }
    
    $medicationId = (int)$input['medication_id'];
    
    $db = (new Database)->connect();
    $medicationRepo = new MedicationRepository($db);
    
    $medication = $medicationRepo->findById($medicationId);
    if (!$medication) {
        echo json_encode(['success' => false, 'message' => 'Medication not found']);
        exit;
    }
    
    // Check if medication is used in prescriptions
    $stmt = $db->prepare("SELECT COUNT(*) FROM prescriptions WHERE medication_id = :med_id");
    $stmt->bindParam(':med_id', $medicationId);
    $stmt->execute();
    $prescriptionCount = $stmt->fetchColumn();
    
    if ($prescriptionCount > 0) {
        echo json_encode([
            'success' => false, 
            'message' => 'Cannot delete medication. It is used in ' . $prescriptionCount . ' prescription(s).'
        ]);
        exit;
    }
    
    if ($medicationRepo->delete($medicationId)) {
        echo json_encode(['success' => true, 'message' => 'Medication deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete medication']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}