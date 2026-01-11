<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Repositories\AppointmentRepository;
use Config\Database;

$page_title = "View Appointment";
require_once '../../includes/auth_check.php';
$router->requireRole('doctor'); 
require_once '../../includes/header.php';
require_once '../../includes/navbar.php';

if (!isset($_GET['id'])) {
    header('Location: appointments.php');
    exit;
}

$db = (new Database)->connect();
$appointmentRepo = new AppointmentRepository($db);

$appointmentId = (int)$_GET['id'];
$appointmentDetails = $appointmentRepo->findWithDetails($appointmentId);

if (!$appointmentDetails) {
    $_SESSION['error'] = 'Appointment not found.';
    header('Location: appointments.php');
    exit;
}
?>

<div class="main-content">
    <div class="content-header">
        <button class="mobile-toggle" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>
        <div class="flex-grow-1">
            <h4 class="mb-0">Appointment Details</h4>
            <p class="text-muted mb-0">View appointment information</p>
        </div>
        <a href="appointments.php" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to List
        </a>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-8">
                <!-- Appointment Information Card -->
                <div class="card mb-4">
                    <div class="card-header" style="background-color: #F1F0CC;">
                        <h5 class="mb-0">
                            <i class="fas fa-calendar-check me-2"></i>Appointment #A<?= str_pad($appointmentDetails['appointment_id'], 3, '0', STR_PAD_LEFT) ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="text-muted small">Patient Name</label>
                                    <p class="mb-0 fw-bold">
                                        <i class="fas fa-user-injured me-2" style="color: #A71D31;"></i>
                                        <?= htmlspecialchars($appointmentDetails['patient_name']) ?>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="text-muted small">Patient Phone</label>
                                    <p class="mb-0">
                                        <i class="fas fa-phone me-2" style="color: #A71D31;"></i>
                                        <?= htmlspecialchars($appointmentDetails['patient_phone']) ?>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="text-muted small">Doctor Name</label>
                                    <p class="mb-0 fw-bold">
                                        <i class="fas fa-user-md me-2" style="color: #3F0D12;"></i>
                                        <?= htmlspecialchars($appointmentDetails['doctor_name']) ?>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="text-muted small">Specialization</label>
                                    <p class="mb-0">
                                        <i class="fas fa-stethoscope me-2" style="color: #3F0D12;"></i>
                                        <?= htmlspecialchars($appointmentDetails['doctor_specialization']) ?>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="text-muted small">Appointment Date</label>
                                    <p class="mb-0">
                                        <i class="fas fa-calendar me-2" style="color: #A71D31;"></i>
                                        <?= date('l, F d, Y', strtotime($appointmentDetails['appointment_date'])) ?>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="text-muted small">Appointment Time</label>
                                    <p class="mb-0">
                                        <i class="fas fa-clock me-2" style="color: #A71D31;"></i>
                                        <?= date('h:i A', strtotime($appointmentDetails['appointment_time'])) ?>
                                    </p>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="info-item">
                                    <label class="text-muted small">Reason for Visit</label>
                                    <p class="mb-0">
                                        <i class="fas fa-notes-medical me-2" style="color: #A71D31;"></i>
                                        <?= htmlspecialchars($appointmentDetails['reason']) ?>
                                    </p>
                                </div>
                            </div>
                            <?php if (!empty($appointmentDetails['notes'])): ?>
                            <div class="col-12">
                                <div class="info-item">
                                    <label class="text-muted small">Additional Notes</label>
                                    <div class="alert alert-light mb-0">
                                        <i class="fas fa-sticky-note me-2"></i>
                                        <?= nl2br(htmlspecialchars($appointmentDetails['notes'])) ?>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Status Card -->
                <div class="card mb-4">
                    <div class="card-header" style="background-color: #F1F0CC;">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>Status
                        </h5>
                    </div>
                    <div class="card-body text-center">
                        <?php
                        $status = $appointmentDetails['status'];
                        $badgeClass = 'badge-secondary';
                        $icon = 'fa-question-circle';
                        switch($status) {
                            case 'Pending':
                                $badgeClass = 'badge-warning';
                                $icon = 'fa-hourglass-half';
                                break;
                            case 'Confirmed':
                                $badgeClass = 'badge-success';
                                $icon = 'fa-check-circle';
                                break;
                            case 'Completed':
                                $badgeClass = 'badge-info';
                                $icon = 'fa-check-double';
                                break;
                            case 'Cancelled':
                                $badgeClass = 'badge-danger';
                                $icon = 'fa-times-circle';
                                break;
                        }
                        ?>
                        <i class="fas <?= $icon ?> fa-3x mb-3" style="color: var(--bs-<?= str_replace('badge-', '', $badgeClass) ?>);"></i>
                        <h3 class="mb-0">
                            <span class="badge <?= $badgeClass ?> fs-5"><?= htmlspecialchars($status) ?></span>
                        </h3>
                    </div>
                </div>

                <!-- Quick Actions Card -->
                <div class="card">
                    <div class="card-header" style="background-color: #F1F0CC;">
                        <h5 class="mb-0">
                            <i class="fas fa-bolt me-2"></i>Quick Actions
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <?php if ($status === 'Pending'): ?>
                                <button class="btn btn-success" onclick="confirmAppointment(<?= $appointmentDetails['appointment_id'] ?>)">
                                    <i class="fas fa-check me-2"></i>Confirm Appointment
                                </button>
                            <?php endif; ?>
                            
                            <?php if ($status === 'Confirmed'): ?>
                                <button class="btn btn-info text-white" onclick="completeAppointment(<?= $appointmentDetails['appointment_id'] ?>)">
                                    <i class="fas fa-check-double me-2"></i>Mark as Completed
                                </button>
                            <?php endif; ?>
                            
                            <?php if ($status !== 'Completed' && $status !== 'Cancelled'): ?>
                                <a href="appointments.php" class="btn btn-warning" onclick="event.preventDefault(); editAppointment(<?= $appointmentDetails['appointment_id'] ?>)">
                                    <i class="fas fa-edit me-2"></i>Edit Appointment
                                </a>
                                <button class="btn btn-danger" onclick="cancelAppointment(<?= $appointmentDetails['appointment_id'] ?>)">
                                    <i class="fas fa-times me-2"></i>Cancel Appointment
                                </button>
                            <?php endif; ?>
                            
                            <button class="btn btn-outline-danger" onclick="deleteAppointment(<?= $appointmentDetails['appointment_id'] ?>)">
                                <i class="fas fa-trash me-2"></i>Delete Appointment
                            </button>
                            
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.info-item {
    padding: 10px;
    border-left: 3px solid #A71D31;
    background-color: #f8f9fa;
    border-radius: 4px;
}

.info-item label {
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.75rem;
    margin-bottom: 0.5rem;
    display: block;
}

.info-item p {
    font-size: 1rem;
}
</style>

<script>
function confirmAppointment(id) {
    if(confirm('Confirm this appointment?')) {
        updateAppointmentStatus(id, 'Confirmed');
    }
}

function completeAppointment(id) {
    if(confirm('Mark this appointment as completed?')) {
        updateAppointmentStatus(id, 'Completed');
    }
}

function cancelAppointment(id) {
    if(confirm('Are you sure you want to cancel this appointment?')) {
        updateAppointmentStatus(id, 'Cancelled');
    }
}

function deleteAppointment(id) {
    if(confirm('Are you sure you want to permanently delete this appointment? This action cannot be undone.')) {
        fetch('delete_appointment.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ appointment_id: id })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert('Appointment deleted successfully');
                window.location.href = 'appointments.php';
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(err => {
            alert('Error deleting appointment');
            console.error(err);
        });
    }
}

function updateAppointmentStatus(id, status) {
    fetch('update_appointment_status.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ appointment_id: id, status: status })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('Appointment status updated successfully');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(err => {
        alert('Error updating appointment status');
        console.error(err);
    });
}

function editAppointment(id) {
    window.location.href = 'appointments.php#edit-' + id;
}
</script>

<?php require_once '../../includes/footer.php'; ?>