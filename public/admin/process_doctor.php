<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Repositories\DoctorRepository;
use Repositories\UserRepository;
use Models\Doctor;
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
    header('Location: doctors.php');
    exit;
}

// Validate required fields
$requiredFields = ['action', 'first_name', 'last_name', 'specialization', 'department_id', 'phone_number', 'email', 'username'];
foreach ($requiredFields as $field) {
    if (!isset($_POST[$field]) || trim($_POST[$field]) === '') {
        $_SESSION['error'] = 'All required fields must be filled';
        header('Location: doctors.php');
        exit;
    }
}

try {
    $db = (new Database)->connect();
    $doctorRepo = new DoctorRepository($db);
    $userRepo = new UserRepository($db);
    
    $action = $_POST['action'];
    
    if ($action === 'add') {
        // Validate password for new doctor
        if (!isset($_POST['password']) || trim($_POST['password']) === '') {
            $_SESSION['error'] = 'Password is required for new doctors';
            header('Location: doctors.php');
            exit;
        }
        
        // Check if username exists
        if ($userRepo->findByUsername($_POST['username'])) {
            $_SESSION['error'] = 'Username already exists.';
            header('Location: doctors.php');
            exit;
        }
        
        // Check if email exists
        if ($userRepo->findByEmail($_POST['email'])) {
            $_SESSION['error'] = 'Email already exists.';
            header('Location: doctors.php');
            exit;
        }
        
        $db->beginTransaction();
        
        try {
            // Create user account first
            $user = new User();
            $user->setUsername(trim($_POST['username']));
            $user->setEmail(trim($_POST['email']));
            $user->setPassword(trim($_POST['password'])); // This should hash the password in the model
            $user->setRole(User::ROLE_DOCTOR);
            
            if (!$userRepo->create($user)) {
                throw new Exception('Failed to create user account');
            }
            
            // Create doctor record
            $doctor = new Doctor();
            $doctor->setId($user->getId());
            $doctor->setDoctorId($user->getId());
            $doctor->setFirstName(trim($_POST['first_name']));
            $doctor->setLastName(trim($_POST['last_name']));
            $doctor->setSpecialization(trim($_POST['specialization']));
            $doctor->setPhoneNumber(trim($_POST['phone_number']));
            $doctor->setEmail(trim($_POST['email']));
            $doctor->setDepartmentId((int)$_POST['department_id']);
            $doctor->setUsername(trim($_POST['username']));
            $doctor->setPasswordRaw($user->getPassword()); // Store hashed password
            
            if (!$doctorRepo->create($doctor)) {
                throw new Exception('Failed to create doctor record');
            }
            
            $db->commit();
            $_SESSION['success'] = 'Doctor added successfully!';
            
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
        
    } elseif ($action === 'edit') {
        // Validate doctor ID
        if (!isset($_POST['doctor_id']) || empty($_POST['doctor_id'])) {
            $_SESSION['error'] = 'Doctor ID is required for update';
            header('Location: doctors.php');
            exit;
        }
        
        $doctorId = (int)$_POST['doctor_id'];
        $doctor = $doctorRepo->findById($doctorId);
        
        if (!$doctor) {
            $_SESSION['error'] = 'Doctor not found.';
            header('Location: doctors.php');
            exit;
        }
        
        $db->beginTransaction();
        
        try {
            // Update doctor information
            $doctor->setFirstName(trim($_POST['first_name']));
            $doctor->setLastName(trim($_POST['last_name']));
            $doctor->setSpecialization(trim($_POST['specialization']));
            $doctor->setPhoneNumber(trim($_POST['phone_number']));
            $doctor->setDepartmentId((int)$_POST['department_id']);
            
            // Update user account
            $user = $userRepo->findById($doctor->getId());
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
                $doctor->setEmail(trim($_POST['email']));
            }
            
            // Update password if provided
            if (isset($_POST['password']) && !empty(trim($_POST['password']))) {
                $user->setPassword(trim($_POST['password']));
            }
            
            // Save changes
            if (!$userRepo->update($user)) {
                throw new Exception('Failed to update user account');
            }
            
            if (!$doctorRepo->update($doctor)) {
                throw new Exception('Failed to update doctor record');
            }
            
            $db->commit();
            $_SESSION['success'] = 'Doctor updated successfully!';
            
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
        
    } else {
        $_SESSION['error'] = 'Invalid action';
    }
    
} catch (Exception $e) {
    error_log('Doctor processing error: ' . $e->getMessage());
    $_SESSION['error'] = 'Error: ' . $e->getMessage();
}

header('Location: doctors.php');
exit;