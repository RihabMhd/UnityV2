<?php 
use Repositories\AppointmentRepository;
use Repositories\PatientRepository;
use Config\Database;

$page_title = "Patient Dashboard";
require_once '../../includes/auth_check.php';
$router->requireRole('patient');
require_once '../../includes/header.php';
require_once '../../includes/navbar.php';
require_once __DIR__ . '/../../vendor/autoload.php';

$db = (new Database())->connect();
$appointmentsRepo = new AppointmentRepository($db);
$patientsRepo = new PatientRepository($db);

$authenticatedUserId = $_SESSION['user_id'];

$currentPatient = $patientsRepo->findById($authenticatedUserId);

if (!$currentPatient) {
    $_SESSION['error'] = 'Patient profile not found. Please contact administrator.';
    header('Location: ./index.php');
    exit;
}

$authenticatedPatientId = $currentPatient->getPatientId();

// Get appointments for this patient
$appointments = $appointmentsRepo->findByPatient($authenticatedPatientId);
$upcomingAppointments = $appointmentsRepo->getUpcomingForPatient($authenticatedPatientId, 5);
$totalAppointments = count($appointments);
?>

<div class="main-content">
    <div class="content-header">
        <button class="mobile-toggle" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>
        <h4 class="mb-0">Welcome, <?= htmlspecialchars($currentPatient->getFirstName()) ?>!</h4>
    </div>

    <div class="container-fluid">
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-lg-4 col-md-6 mb-3">
                <div class="stat-card" style="background: linear-gradient(135deg, #3F0D12 0%, #A71D31 100%);">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-details">
                        <h3><?= $totalAppointments ?></h3>
                        <p>Total Appointments</p>
                        <small><?= count($upcomingAppointments) ?> upcoming</small>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-3">
                <div class="stat-card" style="background: linear-gradient(135deg, #A71D31 0%, #3F0D12 100%);">
                    <div class="stat-icon">
                        <i class="fas fa-prescription"></i>
                    </div>
                    <div class="stat-details">
                        <h3>5</h3>
                        <p>Active Prescriptions</p>
                        <small>Last updated today</small>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-3">
                <div class="stat-card" style="background: linear-gradient(135deg, #D5BF86 0%, #F1F0CC 100%); color: #3F0D12;">
                    <div class="stat-icon" style="color: #3F0D12;">
                        <i class="fas fa-user-md"></i>
                    </div>
                    <div class="stat-details">
                        <h3><?= $currentPatient->getDoctorId() ? '1' : '0' ?></h3>
                        <p>Assigned Doctor</p>
                        <small><?= $currentPatient->getDoctorId() ? 'Active' : 'Not assigned' ?></small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upcoming Appointments -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Upcoming Appointments</h5>
            </div>
            <div class="card-body">
                <?php if (empty($upcomingAppointments)): ?>
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-calendar-times fa-3x mb-3 d-block"></i>
                        <p>No upcoming appointments</p>
                        <a href="appointments.php" class="btn btn-primary" style="background-color: #A71D31; border-color: #A71D31;">
                            <i class="fas fa-plus-circle me-2"></i>Book an Appointment
                        </a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date & Time</th>
                                    <th>Doctor</th>
                                    <th>Reason</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($upcomingAppointments as $app): ?>
                                    <tr>
                                        <td>
                                            <strong><?= date('M d, Y', strtotime($app->getAppointmentDate())) ?></strong><br>
                                            <small class="text-muted"><?= date('h:i A', strtotime($app->getAppointmentTime())) ?></small>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm me-2">
                                                    <i class="fas fa-user-md"></i>
                                                </div>
                                                <span><?= htmlspecialchars($app->getDoctorName() ?? 'N/A') ?></span>
                                            </div>
                                        </td>
                                        <td><?= htmlspecialchars($app->getReason()) ?></td>
                                        <td>
                                            <?php
                                            $status = $app->getStatus();
                                            $badgeClass = 'badge-secondary';
                                            switch($status) {
                                                case 'Pending': $badgeClass = 'badge-warning'; break;
                                                case 'Confirmed': $badgeClass = 'badge-success'; break;
                                                case 'Completed': $badgeClass = 'badge-info'; break;
                                                case 'Cancelled': $badgeClass = 'badge-danger'; break;
                                            }
                                            ?>
                                            <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($status) ?></span>
                                        </td>
                                        <td>
                                            <a href="view_appointment.php?id=<?= $app->getAppointmentId() ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-end mt-3">
                        <a href="appointments.php" class="btn btn-outline-primary" style="color: #A71D31; border-color: #A71D31;">
                            View All Appointments <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <a href="appointments.php" class="btn btn-outline-primary btn-block" style="border-color: #A71D31; color: #A71D31;">
                            <i class="fas fa-calendar-plus me-2"></i> Book Appointment
                        </a>
                    </div>
                    <div class="col-md-4 mb-3">
                        <a href="prescriptions.php" class="btn btn-outline-primary btn-block" style="border-color: #8D775F; color: #8D775F;">
                            <i class="fas fa-prescription me-2"></i> View Prescriptions
                        </a>
                    </div>
                    <div class="col-md-4 mb-3">
                        <a href="medical_records.php" class="btn btn-outline-primary btn-block" style="border-color: #3F0D12; color: #3F0D12;">
                            <i class="fas fa-file-medical me-2"></i> Medical Records
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Patient Information -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-user me-2"></i>My Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Full Name:</strong> <?= htmlspecialchars($currentPatient->getFullName()) ?></p>
                        <p><strong>Patient ID:</strong> #P<?= str_pad($authenticatedPatientId, 3, '0', STR_PAD_LEFT) ?></p>
                        <p><strong>Email:</strong> <?= htmlspecialchars($currentPatient->getEmail() ?? 'N/A') ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Phone:</strong> <?= htmlspecialchars($currentPatient->getPhoneNumber() ?? 'N/A') ?></p>
                        <p><strong>Date of Birth:</strong> <?= $currentPatient->getDateOfBirth() ? date('M d, Y', strtotime($currentPatient->getDateOfBirth())) : 'N/A' ?></p>
                    </div>
                </div>
                <div class="text-end mt-3">
                    <a href="profile.php" class="btn btn-primary" style="background-color: #3F0D12; border-color: #3F0D12;">
                        <i class="fas fa-edit me-2"></i>Edit Profile
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>