<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Repositories\MedicationRepository;
use Models\Medication;
use Config\Database;

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: medications.php');
    exit;
}

try {
    $db = (new Database)->connect();
    $medicationRepo = new MedicationRepository($db);
    
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        // Check if code already exists
        $stmt = $db->prepare("SELECT COUNT(*) FROM medications WHERE code = :code");
        $code = trim($_POST['code']);
        $stmt->bindParam(':code', $code);
        $stmt->execute();
        
        if ($stmt->fetchColumn() > 0) {
            $_SESSION['error'] = 'Medication code already exists!';
            header('Location: medications.php');
            exit;
        }
        
        $medication = new Medication();
        $medication->setMedicationName($_POST['medication_name']);
        $medication->setDosage($_POST['dosage']);
        $medication->setCode($code);
        $medication->setCategory($_POST['category']);
        $medication->setManufacturer($_POST['manufacturer']);
        $medication->setStockQuantity((int)$_POST['stock_quantity']);
        $medication->setUnitPrice((float)$_POST['unit_price']);
        $medication->setExpiryDate($_POST['expiry_date']);
        
        // Determine status based on stock
        $stockQty = (int)$_POST['stock_quantity'];
        if ($stockQty == 0) {
            $status = 'Out of Stock';
        } elseif ($stockQty <= 200) {
            $status = 'Low Stock';
        } else {
            $status = 'Available';
        }
        $medication->setStatus($status);
        
        if ($medicationRepo->create($medication)) {
            $_SESSION['success'] = 'Medication added successfully!';
        } else {
            $_SESSION['error'] = 'Failed to add medication.';
        }
        
    } elseif ($action === 'edit') {
        $medicationId = (int)$_POST['medication_id'];
        $medication = $medicationRepo->findById($medicationId);
        
        if (!$medication) {
            $_SESSION['error'] = 'Medication not found.';
            header('Location: medications.php');
            exit;
        }
        
        // Check if code already exists (excluding current medication)
        $stmt = $db->prepare("SELECT COUNT(*) FROM medications WHERE code = :code AND medication_id != :id");
        $code = trim($_POST['code']);
        $stmt->bindParam(':code', $code);
        $stmt->bindParam(':id', $medicationId);
        $stmt->execute();
        
        if ($stmt->fetchColumn() > 0) {
            $_SESSION['error'] = 'Medication code already exists!';
            header('Location: medications.php');
            exit;
        }
        
        $medication->setMedicationName($_POST['medication_name']);
        $medication->setDosage($_POST['dosage']);
        $medication->setCode($code);
        $medication->setCategory($_POST['category']);
        $medication->setManufacturer($_POST['manufacturer']);
        $medication->setStockQuantity((int)$_POST['stock_quantity']);
        $medication->setUnitPrice((float)$_POST['unit_price']);
        $medication->setExpiryDate($_POST['expiry_date']);
        
        // Determine status based on stock
        $stockQty = (int)$_POST['stock_quantity'];
        if ($stockQty == 0) {
            $status = 'Out of Stock';
        } elseif ($stockQty <= 200) {
            $status = 'Low Stock';
        } else {
            $status = 'Available';
        }
        $medication->setStatus($status);
        
        if ($medicationRepo->update($medication)) {
            $_SESSION['success'] = 'Medication updated successfully!';
        } else {
            $_SESSION['error'] = 'Failed to update medication.';
        }
    }
    
} catch (Exception $e) {
    $_SESSION['error'] = 'Error: ' . $e->getMessage();
}

header('Location: medications.php');
exit;