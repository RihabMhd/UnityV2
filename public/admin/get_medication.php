<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Repositories\MedicationRepository;
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
    $medicationRepo = new MedicationRepository($db);
    
    $medication = $medicationRepo->findById((int)$_GET['id']);
    
    if (!$medication) {
        echo json_encode(['error' => 'Medication not found']);
        exit;
    }
    
    echo json_encode([
        'medication_id' => $medication->getMedicationId(),
        'medication_name' => $medication->getMedicationName(),
        'dosage' => $medication->getDosage(),
        'code' => $medication->getCode(),
        'category' => $medication->getCategory(),
        'manufacturer' => $medication->getManufacturer(),
        'stock_quantity' => $medication->getStockQuantity(),
        'unit_price' => $medication->getUnitPrice(),
        'expiry_date' => $medication->getExpiryDate(),
        'status' => $medication->getStatus()
    ]);
    
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}