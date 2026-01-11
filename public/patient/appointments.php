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
    header('Location: index.php');
    exit;
}

$authenticatedPatientId = $currentPatient->getPatientId();

$appointments = $appointmentsRepo->findByPatient($authenticatedPatientId);

$filteredAppointments = $appointments;
$statusFilter = isset($_GET['status']) && !empty($_GET['status']) ? $_GET['status'] : 'all';

if ($statusFilter !== 'all') {
    $filteredAppointments = array_filter($filteredAppointments, function($app) use ($statusFilter) {
        return strtolower($app->getStatus()) === strtolower($statusFilter);
    });
}

usort($filteredAppointments, function($a, $b) {
    $dateA = strtotime($a->getAppointmentDate() . ' ' . $a->getAppointmentTime());
    $dateB = strtotime($b->getAppointmentDate() . ' ' . $b->getAppointmentTime());
    return $dateB - $dateA;
});
?>

<div class="main-content">
    <div class="content-header">
        <button class="mobile-toggle" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h5 class="mb-1"></h5>
                <p class="text-muted mb-0"></p>
            </div>
            <a href="book_appointment.php" class="btn btn-primary" style="background-color: #A71D31; border-color: #A71D31;">
                <i class="fas fa-plus-circle me-2"></i>Book New Appointment
            </a>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <ul class="nav nav-pills">
                    <li class="nav-item">
                        <a class="nav-link <?= $statusFilter === 'all' ? 'active' : '' ?>" 
                           href="?status=all" 
                           style="<?= $statusFilter === 'all' ? 'background-color: #A71D31; border-color: #A71D31;' : 'color: #A71D31;' ?>">
                            All Appointments (<?= count($appointments) ?>)
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $statusFilter === 'pending' ? 'active' : '' ?>" 
                           href="?status=pending"
                           style="<?= $statusFilter === 'pending' ? 'background-color: #ffc107; border-color: #ffc107; color: #000;' : 'color: #ffc107;' ?>">
                            Pending
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $statusFilter === 'confirmed' ? 'active' : '' ?>" 
                           href="?status=confirmed"
                           style="<?= $statusFilter === 'confirmed' ? 'background-color: #28a745; border-color: #28a745;' : 'color: #28a745;' ?>">
                            Confirmed
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $statusFilter === 'completed' ? 'active' : '' ?>" 
                           href="?status=completed"
                           style="<?= $statusFilter === 'completed' ? 'background-color: #17a2b8; border-color: #17a2b8;' : 'color: #17a2b8;' ?>">
                            Completed
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $statusFilter === 'cancelled' ? 'active' : '' ?>" 
                           href="?status=cancelled"
                           style="<?= $statusFilter === 'cancelled' ? 'background-color: #dc3545; border-color: #dc3545;' : 'color: #dc3545;' ?>">
                            Cancelled
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-calendar-alt me-2"></i>
                    <?= ucfirst($statusFilter) ?> Appointments
                    <span class="badge bg-secondary ms-2"><?= count($filteredAppointments) ?></span>
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($filteredAppointments)): ?>
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-calendar-times fa-4x mb-3"></i>
                        <h5>No appointments found</h5>
                        <p>You don't have any <?= $statusFilter !== 'all' ? $statusFilter : '' ?> appointments yet.</p>
                        <a href="book_appointment.php" class="btn btn-primary mt-3" style="background-color: #A71D31; border-color: #A71D31;">
                            <i class="fas fa-plus-circle me-2"></i>Book Your First Appointment
                        </a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Appointment ID</th>
                                    <th>Date & Time</th>
                                    <th>Doctor</th>
                                    <th>Reason</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($filteredAppointments as $app): ?>
                                    <tr>
                                        <td>
                                            <strong>#A<?= str_pad($app->getAppointmentId(), 4, '0', STR_PAD_LEFT) ?></strong>
                                        </td>
                                        <td>
                                            <strong><?= date('M d, Y', strtotime($app->getAppointmentDate())) ?></strong><br>
                                            <small class="text-muted">
                                                <i class="fas fa-clock me-1"></i>
                                                <?= date('h:i A', strtotime($app->getAppointmentTime())) ?>
                                            </small>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm me-2">
                                                    <i class="fas fa-user-md"></i>
                                                </div>
                                                <div>
                                                    <strong><?= htmlspecialchars($app->getDoctorName() ?? 'Not Assigned') ?></strong><br>
                                                    <small class="text-muted">Doctor</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?= htmlspecialchars($app->getReason()) ?></td>
                                        <td>
                                            <?php
                                            $status = $app->getStatus();
                                            $badgeClass = 'badge-secondary';
                                            switch(strtolower($status)) {
                                                case 'pending': 
                                                    $badgeClass = 'badge-warning'; 
                                                    $icon = 'fa-clock';
                                                    break;
                                                case 'confirmed': 
                                                    $badgeClass = 'badge-success'; 
                                                    $icon = 'fa-check-circle';
                                                    break;
                                                case 'completed': 
                                                    $badgeClass = 'badge-info'; 
                                                    $icon = 'fa-check-double';
                                                    break;
                                                case 'cancelled': 
                                                    $badgeClass = 'badge-danger'; 
                                                    $icon = 'fa-times-circle';
                                                    break;
                                                default:
                                                    $icon = 'fa-info-circle';
                                            }
                                            ?>
                                            <span class="badge <?= $badgeClass ?>">
                                                <i class="fas <?= $icon ?> me-1"></i>
                                                <?= htmlspecialchars($status) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="view_appointment.php?id=<?= $app->getAppointmentId() ?>" 
                                                   class="btn btn-sm btn-outline-primary" 
                                                   title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <?php if (strtolower($status) === 'pending' || strtolower($status) === 'confirmed'): ?>
                                                    <a href="edit_appointment.php?id=<?= $app->getAppointmentId() ?>" 
                                                       class="btn btn-sm btn-outline-warning" 
                                                       title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button onclick="cancelAppointment(<?= $app->getAppointmentId() ?>)" 
                                                            class="btn btn-sm btn-outline-danger" 
                                                            title="Cancel">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
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
</div>

<style>
.nav-pills .nav-link {
    border-radius: 20px;
    margin-right: 10px;
    transition: all 0.3s ease;
}

.nav-pills .nav-link:not(.active) {
    background-color: transparent;
    border: 1px solid currentColor;
}

.nav-pills .nav-link:hover {
    transform: translateY(-2px);
}

.avatar-sm {
    width: 40px;
    height: 40px;
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

.btn-group .btn {
    margin-right: 5px;
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