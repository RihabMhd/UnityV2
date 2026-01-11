<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Repositories\PrescriptionRepository;
use Repositories\DoctorRepository;
use Models\Prescription;
use Config\Database;

session_start();

// Check authentication - only doctors
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    $_SESSION['error'] = 'Unauthorized access';
    header('Location: ../../login.php');
    exit;
}

// Check request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = 'Invalid request method';
    header('Location: prescriptions.php');
    exit;
}

try {
    $db = (new Database)->connect();
    $prescriptionRepo = new PrescriptionRepository($db);
    $doctorRepo = new DoctorRepository($db);
    
    // Get current doctor
    $currentDoctor = $doctorRepo->findById($_SESSION['user_id']);
    
    if (!$currentDoctor) {
        $_SESSION['error'] = 'Doctor profile not found';
        header('Location: prescriptions.php');
        exit;
    }
    
    $authenticatedDoctorId = $currentDoctor->getDoctorId();
    $action = $_POST['action'] ?? '';
    
    // Validate required fields
    $requiredFields = ['patient_id', 'medication_id', 'prescription_date', 'dosage_instructions'];
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            $_SESSION['error'] = "Missing required field: " . ucfirst(str_replace('_', ' ', $field));
            header('Location: prescriptions.php');
            exit;
        }
    }
    
    if ($action === 'add') {
        // Create new prescription
        $prescription = new Prescription();
        
        try {
            $prescription->setPatientId((int)$_POST['patient_id']);
            $prescription->setDoctorId($authenticatedDoctorId); // Use authenticated doctor ID
            $prescription->setMedicationId((int)$_POST['medication_id']);
            $prescription->setPrescriptionDate($_POST['prescription_date']);
            $prescription->setDosageInstructions($_POST['dosage_instructions']);
        } catch (Exception $e) {
            $_SESSION['error'] = 'Validation error: ' . $e->getMessage();
            header('Location: prescriptions.php');
            exit;
        }
        
        // Create the prescription
        if ($prescriptionRepo->create($prescription)) {
            $_SESSION['success'] = 'Prescription created successfully!';
        } else {
            $_SESSION['error'] = 'Failed to create prescription. Please try again.';
        }
        
    } elseif ($action === 'edit') {
        // Update existing prescription
        if (empty($_POST['prescription_id'])) {
            $_SESSION['error'] = 'Prescription ID is required for updates';
            header('Location: prescriptions.php');
            exit;
        }
        
        $prescriptionId = (int)$_POST['prescription_id'];
        $prescription = $prescriptionRepo->findById($prescriptionId);
        
        if (!$prescription) {
            $_SESSION['error'] = 'Prescription not found.';
            header('Location: prescriptions.php');
            exit;
        }
        
        // Verify this doctor owns the prescription
        if ($prescription->getDoctorId() !== $authenticatedDoctorId) {
            $_SESSION['error'] = 'Unauthorized to edit this prescription';
            header('Location: prescriptions.php');
            exit;
        }
        
        try {
            $prescription->setPatientId((int)$_POST['patient_id']);
            $prescription->setDoctorId($authenticatedDoctorId); // Keep same doctor
            $prescription->setMedicationId((int)$_POST['medication_id']);
            $prescription->setPrescriptionDate($_POST['prescription_date']);
            $prescription->setDosageInstructions($_POST['dosage_instructions']);
        } catch (Exception $e) {
            $_SESSION['error'] = 'Validation error: ' . $e->getMessage();
            header('Location: prescriptions.php');
            exit;
        }
        
        // Update the prescription
        if ($prescriptionRepo->update($prescription)) {
            $_SESSION['success'] = 'Prescription updated successfully!';
        } else {
            $_SESSION['error'] = 'Failed to update prescription. Please try again.';
        }
        
    } else {
        $_SESSION['error'] = 'Invalid action specified';
    }
    
} catch (PDOException $e) {
    error_log("Database Error: " . $e->getMessage());
    $_SESSION['error'] = 'Database error occurred. Please contact the administrator.';
} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    $_SESSION['error'] = 'An error occurred: ' . $e->getMessage();
}

header('Location: prescriptions.php');
exit;