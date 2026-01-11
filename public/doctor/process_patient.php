<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Repositories\PatientRepository;
use Repositories\UserRepository;
use Models\Patient;
use Models\User;
use Config\Database;

session_start();

// Check authentication
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['error'] = 'Unauthorized access';
    header('Location: ../../login.php');
    exit;
}

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = 'Invalid request method';
    header('Location: patients.php');
    exit;
}

// Validate required fields
$requiredFields = ['action', 'first_name', 'last_name', 'date_of_birth', 'phone_number', 'email', 'address', 'username'];
foreach ($requiredFields as $field) {
    if (!isset($_POST[$field]) || trim($_POST[$field]) === '') {
        $_SESSION['error'] = 'All required fields must be filled';
        header('Location: patients.php');
        exit;
    }
}

try {
    $db = (new Database)->connect();
    $patientRepo = new PatientRepository($db);
    $userRepo = new UserRepository($db);
    
    $action = $_POST['action'];
    
    if ($action === 'add') {
        // Validate password for new patient
        if (!isset($_POST['password']) || trim($_POST['password']) === '') {
            $_SESSION['error'] = 'Password is required for new patients';
            header('Location: patients.php');
            exit;
        }
        
        // Check if username exists
        if ($userRepo->findByUsername($_POST['username'])) {
            $_SESSION['error'] = 'Username already exists.';
            header('Location: patients.php');
            exit;
        }
        
        // Check if email exists
        if ($userRepo->findByEmail($_POST['email'])) {
            $_SESSION['error'] = 'Email already exists.';
            header('Location: patients.php');
            exit;
        }
        
        $db->beginTransaction();
        
        try {
            // Create user account first
            $user = new User();
            $user->setUsername(trim($_POST['username']));
            $user->setEmail(trim($_POST['email']));
            $user->setPassword(trim($_POST['password'])); // This should hash the password in the model
            $user->setRole(User::ROLE_PATIENT);
            
            if (!$userRepo->create($user)) {
                throw new Exception('Failed to create user account');
            }
            
            // Create patient record
            $patient = new Patient();
            $patient->setId($user->getId());
            $patient->setPatientId($user->getId());
            $patient->setFirstName(trim($_POST['first_name']));
            $patient->setLastName(trim($_POST['last_name']));
            $patient->setDateOfBirth($_POST['date_of_birth']);
            $patient->setPhoneNumber(trim($_POST['phone_number']));
            $patient->setEmail(trim($_POST['email']));
            $patient->setAddress(trim($_POST['address']));
            $patient->setUsername(trim($_POST['username']));
            $patient->setPasswordRaw($user->getPassword()); // Store hashed password
            
            if (!$patientRepo->create($patient)) {
                throw new Exception('Failed to create patient record');
            }
            
            $db->commit();
            $_SESSION['success'] = 'Patient added successfully!';
            
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
        
    } elseif ($action === 'edit') {
        // Validate patient ID
        if (!isset($_POST['patient_id']) || empty($_POST['patient_id'])) {
            $_SESSION['error'] = 'Patient ID is required for update';
            header('Location: patients.php');
            exit;
        }
        
        $patientId = (int)$_POST['patient_id'];
        $patient = $patientRepo->findById($patientId);
        
        if (!$patient) {
            $_SESSION['error'] = 'Patient not found.';
            header('Location: patients.php');
            exit;
        }
        
        $db->beginTransaction();
        
        try {
            // Update patient information
            $patient->setFirstName(trim($_POST['first_name']));
            $patient->setLastName(trim($_POST['last_name']));
            $patient->setDateOfBirth($_POST['date_of_birth']);
            $patient->setPhoneNumber(trim($_POST['phone_number']));
            $patient->setAddress(trim($_POST['address']));
            
            // Update user account
            $user = $userRepo->findById($patient->getPatientId());
            if (!$user) {
                throw new Exception('User account not found');
            }
            
            // Check username uniqueness if changed
            if (trim($_POST['username']) !== $user->getUsername()) {
                $existingUser = $userRepo->findByUsername(trim($_POST['username']));
                if ($existingUser && $existingUser->getId() !== $user->getId()) {
                    throw new Exception('Username already exists');
                }
                $user->setUsername(trim($_POST['username']));
            }
            
            // Check email uniqueness if changed
            if (trim($_POST['email']) !== $user->getEmail()) {
                $existingUser = $userRepo->findByEmail(trim($_POST['email']));
                if ($existingUser && $existingUser->getId() !== $user->getId()) {
                    throw new Exception('Email already exists');
                }
                $user->setEmail(trim($_POST['email']));
                $patient->setEmail(trim($_POST['email']));
            }
            
            // Update password if provided
            if (isset($_POST['password']) && !empty(trim($_POST['password']))) {
                $user->setPassword(trim($_POST['password']));
            }
            
            // Save changes
            if (!$userRepo->update($user)) {
                throw new Exception('Failed to update user account');
            }
            
            if (!$patientRepo->update($patient)) {
                throw new Exception('Failed to update patient record');
            }
            
            $db->commit();
            $_SESSION['success'] = 'Patient updated successfully!';
            
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
        
    } else {
        $_SESSION['error'] = 'Invalid action';
    }
    
} catch (Exception $e) {
    error_log('Patient processing error: ' . $e->getMessage());
    $_SESSION['error'] = 'Error: ' . $e->getMessage();
}

header('Location: patients.php');
exit;