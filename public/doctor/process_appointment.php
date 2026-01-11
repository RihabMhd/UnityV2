<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Repositories\AppointmentRepository;
use Repositories\DoctorRepository;
use Models\Appointment;
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
    header('Location: appointments.php');
    exit;
}

try {
    $db = (new Database)->connect();
    $appointmentRepo = new AppointmentRepository($db);
    $doctorRepo = new DoctorRepository($db);
    
    // Get current doctor
    $currentDoctor = $doctorRepo->findById($_SESSION['user_id']);
    
    if (!$currentDoctor) {
        $_SESSION['error'] = 'Doctor profile not found';
        header('Location: appointments.php');
        exit;
    }
    
    $authenticatedDoctorId = $currentDoctor->getDoctorId();
    $action = $_POST['action'] ?? '';
    
    // Validate required fields
    $requiredFields = ['patient_id', 'appointment_date', 'appointment_time', 'reason'];
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            $_SESSION['error'] = "Missing required field: " . ucfirst(str_replace('_', ' ', $field));
            header('Location: appointments.php');
            exit;
        }
    }
    
    if ($action === 'add') {
        // Create new appointment
        $appointment = new Appointment();
        
        try {
            $appointment->setPatientId((int)$_POST['patient_id']);
            $appointment->setDoctorId($authenticatedDoctorId); // Use authenticated doctor ID
            $appointment->setAppointmentDate($_POST['appointment_date']);
            $appointment->setAppointmentTime($_POST['appointment_time']);
            $appointment->setReason($_POST['reason']);
            $appointment->setStatus($_POST['status'] ?? 'Pending');
            
            if (!empty($_POST['notes'])) {
                $appointment->setNotes($_POST['notes']);
            }
        } catch (Exception $e) {
            $_SESSION['error'] = 'Validation error: ' . $e->getMessage();
            header('Location: appointments.php');
            exit;
        }
        
        // Check for time conflicts
        if ($appointmentRepo->hasConflict(
            $appointment->getDoctorId(),
            $appointment->getAppointmentDate(),
            $appointment->getAppointmentTime()
        )) {
            $_SESSION['error'] = 'This time slot is already booked.';
            header('Location: appointments.php');
            exit;
        }
        
        // Create the appointment
        if ($appointmentRepo->create($appointment)) {
            $_SESSION['success'] = 'Appointment scheduled successfully!';
        } else {
            $_SESSION['error'] = 'Failed to schedule appointment. Please try again.';
        }
        
    } elseif ($action === 'edit') {
        // Update existing appointment
        if (empty($_POST['appointment_id'])) {
            $_SESSION['error'] = 'Appointment ID is required for updates';
            header('Location: appointments.php');
            exit;
        }
        
        $appointmentId = (int)$_POST['appointment_id'];
        $appointment = $appointmentRepo->findById($appointmentId);
        
        if (!$appointment) {
            $_SESSION['error'] = 'Appointment not found.';
            header('Location: appointments.php');
            exit;
        }
        
        // Verify this doctor owns the appointment
        if ($appointment->getDoctorId() !== $authenticatedDoctorId) {
            $_SESSION['error'] = 'Unauthorized to edit this appointment';
            header('Location: appointments.php');
            exit;
        }
        
        try {
            $appointment->setPatientId((int)$_POST['patient_id']);
            $appointment->setDoctorId($authenticatedDoctorId); // Keep same doctor
            $appointment->setAppointmentDate($_POST['appointment_date']);
            $appointment->setAppointmentTime($_POST['appointment_time']);
            $appointment->setReason($_POST['reason']);
            $appointment->setStatus($_POST['status'] ?? 'Pending');
            
            if (!empty($_POST['notes'])) {
                $appointment->setNotes($_POST['notes']);
            } else {
                $appointment->setNotes(null);
            }
        } catch (Exception $e) {
            $_SESSION['error'] = 'Validation error: ' . $e->getMessage();
            header('Location: appointments.php');
            exit;
        }
        
        // Check for time conflicts (excluding current appointment)
        if ($appointmentRepo->hasConflict(
            $appointment->getDoctorId(),
            $appointment->getAppointmentDate(),
            $appointment->getAppointmentTime(),
            $appointmentId
        )) {
            $_SESSION['error'] = 'This time slot is already booked.';
            header('Location: appointments.php');
            exit;
        }
        
        // Update the appointment
        if ($appointmentRepo->update($appointment)) {
            $_SESSION['success'] = 'Appointment updated successfully!';
        } else {
            $_SESSION['error'] = 'Failed to update appointment. Please try again.';
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

header('Location: appointments.php');
exit;