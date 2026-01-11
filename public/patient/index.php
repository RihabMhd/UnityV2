<?php 
$page_title = "Patient Dashboard";

require_once '../../includes/auth_check.php';
$router->requireRole('patient');

require_once '../../includes/header.php';
require_once '../../includes/navbar.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use Repositories\AppointmentRepository;
use Repositories\PatientRepository;
use Repositories\PrescriptionRepository;
use Repositories\DoctorRepository;
use Config\Database;

$db = (new Database())->connect();
$appointmentsRepo = new AppointmentRepository($db);
$patientsRepo = new PatientRepository($db);
$prescriptionsRepo = new PrescriptionRepository($db);
$doctorsRepo = new DoctorRepository($db);

$authenticatedUserId = $_SESSION['user_id'];
$currentPatient = $patientsRepo->findById($authenticatedUserId);

if (!$currentPatient) {
    $_SESSION['error'] = 'Patient profile not found. Please contact administrator.';
    header('Location: /UnityV2/public/auth/logout.php');
    exit;
}

$authenticatedPatientId = $currentPatient->getPatientId();

$appointments = $appointmentsRepo->findByPatient($authenticatedPatientId);
$upcomingAppointments = $appointmentsRepo->getUpcomingForPatient($authenticatedPatientId, 3);
$prescriptions = $prescriptionsRepo->findByPatient($authenticatedPatientId);
$recentAppointments = array_slice($appointments, 0, 5);

$totalAppointments = count($appointments);
$totalPrescriptions = count($prescriptions);
$upcomingCount = count($upcomingAppointments);

$assignedDoctor = null;
if ($currentPatient->getDoctorId()) {
    $assignedDoctor = $doctorsRepo->findById($currentPatient->getDoctorId());
}
?>

<div class="main-content">

    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card" style="background: linear-gradient(135deg, #3F0D12 0%, #A71D31 100%);">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-details">
                        <h3><?= $totalAppointments ?></h3>
                        <p>Total Appointments</p>
                        <small><?= $upcomingCount ?> upcoming</small>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card" style="background: linear-gradient(135deg, #A71D31 0%, #3F0D12 100%);">
                    <div class="stat-icon">
                        <i class="fas fa-prescription"></i>
                    </div>
                    <div class="stat-details">
                        <h3><?= $totalPrescriptions ?></h3>
                        <p>Active Prescriptions</p>
                        <small>All prescriptions</small>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card" style="background: linear-gradient(135deg, #D5BF86 0%, #F1F0CC 100%); color: #3F0D12;">
                    <div class="stat-icon" style="color: #3F0D12;">
                        <i class="fas fa-user-md"></i>
                    </div>
                    <div class="stat-details">
                        <h3><?= $assignedDoctor ? '1' : '0' ?></h3>
                        <p>Assigned Doctor</p>
                        <small><?= $assignedDoctor ? htmlspecialchars($assignedDoctor->getFullName()) : 'Not assigned' ?></small>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card" style="background: linear-gradient(135deg, #8D775F 0%, #D5BF86 100%); color: white;">
                    <div class="stat-icon">
                        <i class="fas fa-file-medical"></i>
                    </div>
                    <div class="stat-details">
                        <h3><?= $upcomingCount ?></h3>
                        <p>Upcoming Visits</p>
                        <small>Next 30 days</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <a href="book_appointment.php" class="btn btn-outline-primary btn-block w-100" style="border-color: #A71D31; color: #A71D31;">
                            <i class="fas fa-calendar-plus me-2"></i> Book Appointment
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="appointments.php" class="btn btn-outline-primary btn-block w-100" style="border-color: #8D775F; color: #8D775F;">
                            <i class="fas fa-calendar-alt me-2"></i> View Appointments
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="prescriptions.php" class="btn btn-outline-primary btn-block w-100" style="border-color: #3F0D12; color: #3F0D12;">
                            <i class="fas fa-prescription me-2"></i> My Prescriptions
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="medical_records.php" class="btn btn-outline-primary btn-block w-100" style="border-color: #D5BF86; color: #8D775F;">
                            <i class="fas fa-file-medical me-2"></i> Medical Records
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8 mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Upcoming Appointments</h5>
                        <a href="appointments.php" class="btn btn-sm btn-outline-primary" style="border-color: #A71D31; color: #A71D31;">View All</a>
                    </div>
                    <div class="card-body">
                        <?php if (empty($upcomingAppointments)): ?>
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-calendar-times fa-3x mb-3 d-block"></i>
                                <p>No upcoming appointments</p>
                                <a href="book_appointment.php" class="btn btn-primary" style="background-color: #A71D31; border-color: #A71D31;">
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
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 mb-4">
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-user me-2"></i>My Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <div class="avatar-lg mx-auto mb-3" style="width: 80px; height: 80px; background: linear-gradient(135deg, #3F0D12 0%, #A71D31 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-user fa-2x text-white"></i>
                            </div>
                            <h5 class="mb-0"><?= htmlspecialchars($currentPatient->getFullName()) ?></h5>
                            <small class="text-muted">Patient ID: #P<?= str_pad($authenticatedPatientId, 3, '0', STR_PAD_LEFT) ?></small>
                        </div>
                        <hr>
                        <div class="info-item mb-2">
                            <small class="text-muted">Email:</small><br>
                            <span><?= htmlspecialchars($currentPatient->getEmail() ?? 'N/A') ?></span>
                        </div>
                        <div class="info-item mb-2">
                            <small class="text-muted">Phone:</small><br>
                            <span><?= htmlspecialchars($currentPatient->getPhoneNumber() ?? 'N/A') ?></span>
                        </div>
                        <div class="info-item mb-2">
                            <small class="text-muted">Date of Birth:</small><br>
                            <span><?= $currentPatient->getDateOfBirth() ? date('M d, Y', strtotime($currentPatient->getDateOfBirth())) : 'N/A' ?></span>
                        </div>
                        <div class="info-item mb-3">
                            <small class="text-muted">Assigned Doctor:</small><br>
                            <span><?= $assignedDoctor ? htmlspecialchars($assignedDoctor->getFullName()) : 'Not assigned' ?></span>
                        </div>
                        <a href="profile.php" class="btn btn-primary w-100" style="background-color: #3F0D12; border-color: #3F0D12;">
                            <i class="fas fa-edit me-2"></i>Edit Profile
                        </a>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-heartbeat me-2"></i>Health Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Total Visits</span>
                            <span class="fw-bold" style="color: #A71D31;"><?= $totalAppointments ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Active Prescriptions</span>
                            <span class="fw-bold" style="color: #3F0D12;"><?= $totalPrescriptions ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Upcoming Appointments</span>
                            <span class="fw-bold" style="color: #8D775F;"><?= $upcomingCount ?></span>
                        </div>
                        <hr>
                        <h6 class="mb-3">Recent Activity</h6>
                        <div class="timeline">
                            <?php if (!empty($recentAppointments)): ?>
                                <?php foreach (array_slice($recentAppointments, 0, 3) as $app): ?>
                                    <div class="timeline-item mb-2">
                                        <small class="text-muted"><?= date('M d, Y', strtotime($app->getAppointmentDate())) ?></small><br>
                                        <small><?= htmlspecialchars($app->getReason()) ?></small>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <small class="text-muted">No recent activity</small>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.stat-card {
    border-radius: 12px;
    padding: 20px;
    color: white;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
}

.stat-icon {
    font-size: 2.5rem;
    opacity: 0.8;
    margin-bottom: 10px;
}

.avatar-sm {
    width: 35px;
    height: 35px;
    background: linear-gradient(135deg, #3F0D12 0%, #A71D31 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

.badge-warning {
    background-color: #ffc107;
    color: #000;
}

.badge-success {
    background-color: #28a745;
}

.badge-info {
    background-color: #17a2b8;
}

.badge-danger {
    background-color: #dc3545;
}

.badge-secondary {
    background-color: #6c757d;
}

.timeline-item {
    padding-left: 15px;
    border-left: 2px solid #A71D31;
}
</style>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
