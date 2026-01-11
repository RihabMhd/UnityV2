<?php
use Repositories\AppointmentRepository;
use Repositories\PatientRepository;
use Repositories\DoctorRepository;
use Config\Database;

$page_title = "Appointment Details";
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
    $_SESSION['error'] = 'Patient profile not found.';
    header('Location: index.php');
    exit;
}

$appointmentId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$appointment = $appointmentsRepo->findById($appointmentId);

if (!$appointment || $appointment->getPatientId() !== $currentPatient->getPatientId()) {
    $_SESSION['error'] = 'Appointment not found or access denied.';
    header('Location: appointments.php');
    exit;
}

$doctor = null;
if ($appointment->getDoctorId()) {
    $doctor = $doctorsRepo->findById($appointment->getDoctorId());
}
?>

<div class="main-content">
    <div class="content-header">
        <button class="mobile-toggle" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <div class="container-fluid">
        <div class="mb-4">
            <a href="appointments.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Appointments
            </a>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header" style="background: linear-gradient(135deg, #3F0D12 0%, #A71D31 100%); color: white;">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-calendar-alt me-2"></i>
                                Appointment #A<?= str_pad($appointment->getAppointmentId(), 4, '0', STR_PAD_LEFT) ?>
                            </h5>
                            <?php
                            $status = $appointment->getStatus();
                            $badgeClass = 'badge-secondary';
                            switch(strtolower($status)) {
                                case 'pending': $badgeClass = 'badge-warning'; break;
                                case 'confirmed': $badgeClass = 'badge-success'; break;
                                case 'completed': $badgeClass = 'badge-info'; break;
                                case 'cancelled': $badgeClass = 'badge-danger'; break;
                            }
                            ?>
                            <span class="badge <?= $badgeClass ?> fs-6"><?= htmlspecialchars($status) ?></span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small d-block mb-1">
                                    <i class="fas fa-calendar me-1"></i>Date
                                </label>
                                <h5 class="mb-0"><?= date('l, F d, Y', strtotime($appointment->getAppointmentDate())) ?></h5>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small d-block mb-1">
                                    <i class="fas fa-clock me-1"></i>Time
                                </label>
                                <h5 class="mb-0"><?= date('h:i A', strtotime($appointment->getAppointmentTime())) ?></h5>
                            </div>
                        </div>

                        <hr>

                        <div class="mb-4">
                            <label class="text-muted small d-block mb-2">
                                <i class="fas fa-notes-medical me-1"></i>Reason for Visit
                            </label>
                            <p class="mb-0"><?= htmlspecialchars($appointment->getReason()) ?></p>
                        </div>

                        <?php if ($appointment->getNotes()): ?>
                            <div class="mb-4">
                                <label class="text-muted small d-block mb-2">
                                    <i class="fas fa-comment-medical me-1"></i>Doctor's Notes
                                </label>
                                <div class="alert alert-info mb-0">
                                    <?= nl2br(htmlspecialchars($appointment->getNotes())) ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="mb-3">
                            <label class="text-muted small d-block mb-2">
                                <i class="fas fa-history me-1"></i>Created On
                            </label>
                        </div>

                        <?php if (strtolower($status) === 'pending' || strtolower($status) === 'confirmed'): ?>
                            <hr>
                            <div class="d-flex gap-2 mt-4">
                                <a href="edit_appointment.php?id=<?= $appointment->getAppointmentId() ?>" 
                                   class="btn btn-warning">
                                    <i class="fas fa-edit me-2"></i>Reschedule
                                </a>
                                <button onclick="cancelAppointment(<?= $appointment->getAppointmentId() ?>)" 
                                        class="btn btn-danger">
                                    <i class="fas fa-times me-2"></i>Cancel Appointment
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Important Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-primary">
                            <h6 class="alert-heading">
                                <i class="fas fa-exclamation-circle me-2"></i>Before Your Appointment
                            </h6>
                            <ul class="mb-0">
                                <li>Please arrive 15 minutes before your scheduled time</li>
                                <li>Bring your ID card and insurance information</li>
                                <li>Bring any relevant medical records or test results</li>
                                <li>Make a list of current medications you're taking</li>
                            </ul>
                        </div>

                        <?php if (strtolower($status) === 'pending' || strtolower($status) === 'confirmed'): ?>
                            <div class="alert alert-warning">
                                <h6 class="alert-heading">
                                    <i class="fas fa-clock me-2"></i>Cancellation Policy
                                </h6>
                                <p class="mb-0">
                                    Please cancel or reschedule at least 24 hours in advance. 
                                    Late cancellations may incur a fee.
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <?php if ($doctor): ?>
                    <div class="card mb-3">
                        <div class="card-header" style="background-color: #A71D31; color: white;">
                            <h6 class="mb-0"><i class="fas fa-user-md me-2"></i>Your Doctor</h6>
                        </div>
                        <div class="card-body text-center">
                            <div class="avatar-xl mx-auto mb-3" style="width: 80px; height: 80px; background: linear-gradient(135deg, #3F0D12 0%, #A71D31 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-user-md fa-2x text-white"></i>
                            </div>
                            <h5 class="mb-1">Dr. <?= htmlspecialchars($doctor->getFullName()) ?></h5>
                            <p class="text-muted mb-2"><?= htmlspecialchars($doctor->getSpecialization()) ?></p>
                            <hr>
                            <div class="text-start">
                                <?php if ($doctor->getEmail()): ?>
                                    <p class="mb-2">
                                        <i class="fas fa-envelope text-muted me-2"></i>
                                        <small><?= htmlspecialchars($doctor->getEmail()) ?></small>
                                    </p>
                                <?php endif; ?>
                                <?php if ($doctor->getPhoneNumber()): ?>
                                    <p class="mb-0">
                                        <i class="fas fa-phone text-muted me-2"></i>
                                        <small><?= htmlspecialchars($doctor->getPhoneNumber()) ?></small>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="card mb-3">
                    <div class="card-header" style="background-color: #3F0D12; color: white;">
                        <h6 class="mb-0"><i class="fas fa-user me-2"></i>Patient Information</h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-2">
                            <strong>Name:</strong><br>
                            <?= htmlspecialchars($currentPatient->getFullName()) ?>
                        </p>
                        <p class="mb-2">
                            <strong>Patient ID:</strong><br>
                            #P<?= str_pad($currentPatient->getPatientId(), 3, '0', STR_PAD_LEFT) ?>
                        </p>
                        <p class="mb-2">
                            <strong>Phone:</strong><br>
                            <?= htmlspecialchars($currentPatient->getPhoneNumber() ?? 'N/A') ?>
                        </p>
                        <p class="mb-0">
                            <strong>Email:</strong><br>
                            <?= htmlspecialchars($currentPatient->getEmail() ?? 'N/A') ?>
                        </p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-tasks me-2"></i>Quick Actions</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button onclick="window.print()" class="btn btn-outline-primary">
                                <i class="fas fa-print me-2"></i>Print Details
                            </button>
                            <a href="book_appointment.php" class="btn btn-outline-success">
                                <i class="fas fa-plus me-2"></i>Book Another
                            </a>
                            <a href="appointments.php" class="btn btn-outline-secondary">
                                <i class="fas fa-list me-2"></i>All Appointments
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
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

@media print {
    .sidebar, .navbar, .btn, .card-header, .content-header {
        display: none !important;
    }
    
    .main-content {
        margin: 0 !important;
        padding: 0 !important;
    }
}
</style>

<script>
function cancelAppointment(appointmentId) {
    if (confirm('Are you sure you want to cancel this appointment?')) {
          window.location.href = 'cancel_appointment.php?id=' + appointmentId;
    }
}
</script>

<?php require_once '../../includes/footer.php'; ?>
