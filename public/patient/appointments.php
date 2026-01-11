<?php 
use Repositories\AppointmentRepository;
use Repositories\PatientRepository;
use Repositories\DoctorRepository;
use Config\Database;

$page_title = "My Appointments";
require_once '../../includes/auth_check.php';
$router->requireRole('patient');
require_once '../../includes/header.php';
require_once '../../includes/navbar.php';
require_once __DIR__ . '/../../vendor/autoload.php';

$db = (new Database())->connect();
$appointmentsRepo = new AppointmentRepository($db);
$patientsRepo = new PatientRepository($db);
$doctorsRepo = new DoctorRepository($db);

$authenticatedUserId = $_SESSION['user_id'];
$currentPatient = $patientsRepo->findById($authenticatedUserId);

if (!$currentPatient) {
    $_SESSION['error'] = 'Patient profile not found. Please contact administrator.';
    header('Location: ../../dashboard.php');
    exit;
}

$authenticatedPatientId = $currentPatient->getPatientId();

// Get appointments for this patient
$appointments = $appointmentsRepo->findByPatient($authenticatedPatientId);
$doctors = $doctorsRepo->findAll();

// Apply filters
$filteredAppointments = $appointments;
if (isset($_GET['status']) && !empty($_GET['status'])) {
    $filteredAppointments = array_filter($filteredAppointments, function($app) {
        return $app->getStatus() === $_GET['status'];
    });
}


?>