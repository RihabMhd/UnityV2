<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Repositories\PatientRepository;
use Repositories\DoctorRepository;
use Config\Database;

$page_title = "View Patient";
require_once '../../includes/auth_check.php';
$router->requireRole('doctor');
require_once '../../includes/header.php';
require_once '../../includes/navbar.php';

// Check if ID is provided
if (!isset($_GET['id'])) {
    header('Location: patients.php');
    exit;
}

$patientId = (int)$_GET['id'];

$db = (new Database())->connect();
$patientRepo = new PatientRepository($db);
$doctorRepo = new DoctorRepository($db);

// Get current doctor
$currentDoctor = $doctorRepo->findById($_SESSION['user_id']);

if (!$currentDoctor) {
    $_SESSION['error'] = 'Doctor profile not found';
    header('Location: patients.php');
    exit;
}

$patientData = $patientRepo->findWithUserDetails($patientId);

if (!$patientData) {
    $_SESSION['error'] = 'Patient not found';
    header('Location: patients.php');
    exit;
}

// Verify this patient belongs to this doctor
if ($patientData['doctor_id'] !== $currentDoctor->getDoctorId()) {
    $_SESSION['error'] = 'Unauthorized access to this patient';
    header('Location: patients.php');
    exit;
}
?>

<div class="main-content">
    <div class="content-header">
        <button class="mobile-toggle" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>
        <div class="flex-grow-1">
            <h4 class="mb-0">Patient Details</h4>
            <p class="text-muted mb-0">View patient information</p>
        </div>
        <a href="patients.php" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to List
        </a>
    </div>

    <div class="container-fluid">
        <div class="row">
            <!-- Patient Profile Card -->
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-body text-center">
                        <div class="avatar-lg mx-auto mb-3" style="background-color: #A71D31; width: 100px; height: 100px; display: flex; align-items: center; justify-content: center; border-radius: 50%; font-size: 2.5rem; color: white;">
                            <?= strtoupper(substr($patientData['first_name'], 0, 1) . substr($patientData['last_name'], 0, 1)) ?>
                        </div>
                        <h5 class="mb-1"><?= htmlspecialchars($patientData['first_name'] . ' ' . $patientData['last_name']) ?></h5>
                        <p class="text-muted mb-2">
                            <i class="fas fa-id-badge me-2"></i>Patient #P<?= str_pad($patientData['patient_id'], 3, '0', STR_PAD_LEFT) ?>
                        </p>
                        <span class="badge bg-success">Active</span>
                        
                        <hr class="my-3">
                        
                        <div class="text-start">
                            <p class="mb-2"><strong>Age:</strong> 
                                <?php
                                    $dob = new DateTime($patientData['date_of_birth']);
                                    $now = new DateTime();
                                    echo $now->diff($dob)->y . ' years';
                                ?>
                            </p>
                            <p class="mb-2"><strong>Date of Birth:</strong> <?= date('M d, Y', strtotime($patientData['date_of_birth'])) ?></p>
                            <p class="mb-2"><strong>Member Since:</strong> <?= date('M d, Y', strtotime($patientData['created_at'])) ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Patient Information -->
            <div class="col-lg-8">
                <!-- Contact Information -->
                <div class="card mb-4">
                    <div class="card-header" style="background-color: #F1F0CC;">
                        <h5 class="mb-0"><i class="fas fa-address-card me-2"></i>Contact Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small">Email Address</label>
                                <p class="mb-0">
                                    <i class="fas fa-envelope me-2 text-primary"></i>
                                    <a href="mailto:<?= htmlspecialchars($patientData['email']) ?>">
                                        <?= htmlspecialchars($patientData['email']) ?>
                                    </a>
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small">Phone Number</label>
                                <p class="mb-0">
                                    <i class="fas fa-phone me-2 text-success"></i>
                                    <a href="tel:<?= htmlspecialchars($patientData['phone_number']) ?>">
                                        <?= htmlspecialchars($patientData['phone_number']) ?>
                                    </a>
                                </p>
                            </div>
                            <div class="col-12">
                                <label class="text-muted small">Address</label>
                                <p class="mb-0">
                                    <i class="fas fa-map-marker-alt me-2 text-danger"></i>
                                    <?= nl2br(htmlspecialchars($patientData['address'])) ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Medical History / Appointments (placeholder) -->
                <div class="card">
                    <div class="card-header" style="background-color: #F1F0CC;">
                        <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Recent Appointments</h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-calendar-times fa-3x mb-3 d-block"></i>
                            <p class="mb-0">No appointments found</p>
                            <a href="appointments.php" class="btn btn-sm btn-primary mt-3" style="background-color: #A71D31; border-color: #A71D31;">
                                <i class="fas fa-plus me-2"></i>Schedule Appointment
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>