<?php
$page_title = "My Appointments";
require_once '../../includes/auth_check.php';
$router->requireRole('doctor');
require_once '../../includes/header.php';
require_once '../../includes/navbar.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use Repositories\AppointmentRepository;
use Repositories\PatientRepository;
use Repositories\DoctorRepository;
use Config\Database;

$db = (new Database)->connect();
$appointmentsRepo = new AppointmentRepository($db);
$patientsRepo = new PatientRepository($db);
$doctorsRepo = new DoctorRepository($db);

$authenticatedUserId = $_SESSION['user_id'];

$currentDoctor = $doctorsRepo->findById($authenticatedUserId);

if (!$currentDoctor) {
    $_SESSION['error'] = 'Doctor profile not found. Please contact administrator.';
    header('Location: ../../index.php');
    exit;
}

$authenticatedDoctorId = $currentDoctor->getDoctorId();

$appointments = $appointmentsRepo->findByDoctor($authenticatedDoctorId);
$patients = $patientsRepo->findAll();

$filteredAppointments = $appointments;
if (isset($_GET['status']) && !empty($_GET['status'])) {
    $filteredAppointments = array_filter($filteredAppointments, function($app) {
        return $app->getStatus() === $_GET['status'];
    });
}
if (isset($_GET['date']) && !empty($_GET['date'])) {
    $filteredAppointments = array_filter($filteredAppointments, function($app) {
        return $app->getAppointmentDate() === $_GET['date'];
    });
}
?>

<div class="main-content">
    <div class="content-header">
        <button class="mobile-toggle" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>
        <div class="flex-grow-1">
            <h4 class="mb-0">My Appointments</h4>
            <p class="text-muted mb-0">Total Appointments: <?= count($appointments) ?></p>
        </div>
        <button class="btn btn-primary" style="background-color: #A71D31; border-color: #A71D31;" data-bs-toggle="modal" data-bs-target="#appointmentModal" onclick="openAddModal()">
            <i class="fas fa-plus-circle me-2"></i>Schedule Appointment
        </button>
    </div>

    <div class="container-fluid">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($_SESSION['success']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($_SESSION['error']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <!-- Search and Filter -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <input type="text" class="form-control" placeholder="Search..." name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" id="searchInput" onkeyup="searchTable()">
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" name="status" id="statusFilter" onchange="filterTable()">
                                <option value="">All Status</option>
                                <option value="Pending" <?= (isset($_GET['status']) && $_GET['status'] === 'Pending') ? 'selected' : '' ?>>Pending</option>
                                <option value="Confirmed" <?= (isset($_GET['status']) && $_GET['status'] === 'Confirmed') ? 'selected' : '' ?>>Confirmed</option>
                                <option value="Completed" <?= (isset($_GET['status']) && $_GET['status'] === 'Completed') ? 'selected' : '' ?>>Completed</option>
                                <option value="Cancelled" <?= (isset($_GET['status']) && $_GET['status'] === 'Cancelled') ? 'selected' : '' ?>>Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="date" class="form-control" name="date" value="<?= htmlspecialchars($_GET['date'] ?? '') ?>" id="dateFilter" onchange="filterTable()">
                        </div>
                        <div class="col-md-2">
                            <a href="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-redo me-2"></i>Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Appointments Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="appointmentsTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Patient</th>
                                <th>Date & Time</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($filteredAppointments)): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        <i class="fas fa-calendar-times fa-3x mb-3 d-block"></i>
                                        No appointments found
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($filteredAppointments as $app): ?>
                                    <tr>
                                        <td><strong>#A<?= str_pad($app->getAppointmentId(), 3, '0', STR_PAD_LEFT) ?></strong></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm me-2">
                                                    <?php 
                                                    $pName = $app->getPatientName() ?? 'NA';
                                                    $initials = '';
                                                    $parts = explode(' ', $pName);
                                                    foreach ($parts as $part) {
                                                        $initials .= strtoupper(substr($part, 0, 1));
                                                    }
                                                    echo $initials;
                                                    ?>
                                                </div>
                                                <span><?= htmlspecialchars($pName) ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <strong><?= date('M d, Y', strtotime($app->getAppointmentDate())) ?></strong><br>
                                                <small class="text-muted"><?= date('h:i A', strtotime($app->getAppointmentTime())) ?></small>
                                            </div>
                                        </td>
                                        <td>
                                            <?php
                                            $status = $app->getStatus();
                                            $badgeClass = 'badge-secondary';
                                            switch($status) {
                                                case 'Pending':
                                                    $badgeClass = 'badge-warning';
                                                    break;
                                                case 'Confirmed':
                                                    $badgeClass = 'badge-success';
                                                    break;
                                                case 'Completed':
                                                    $badgeClass = 'badge-info';
                                                    break;
                                                case 'Cancelled':
                                                    $badgeClass = 'badge-danger';
                                                    break;
                                            }
                                            ?>
                                            <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($status) ?></span>
                                        </td>
                                        <td>
                                            <?php if ($status === 'Pending'): ?>
                                                <button class="btn btn-sm btn-success" onclick="confirmAppointment(<?= $app->getAppointmentId() ?>)" title="Confirm">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            <?php endif; ?>
                                            
                                            <?php if ($status === 'Confirmed'): ?>
                                                <button class="btn btn-sm btn-info text-white" onclick="completeAppointment(<?= $app->getAppointmentId() ?>)" title="Complete">
                                                    <i class="fas fa-check-double"></i>
                                                </button>
                                            <?php endif; ?>
                                            
                                            <button class="btn btn-sm btn-outline-primary" onclick="viewAppointment(<?= $app->getAppointmentId() ?>)" title="View">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            
                                            <?php if ($status !== 'Completed' && $status !== 'Cancelled'): ?>
                                                <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#appointmentModal" onclick="editAppointment(<?= $app->getAppointmentId() ?>)" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger" onclick="cancelAppointment(<?= $app->getAppointmentId() ?>)" title="Cancel">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            <?php endif; ?>
                                            
                                            <button class="btn btn-sm btn-danger" onclick="deleteAppointment(<?= $app->getAppointmentId() ?>)" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Appointment Modal -->
<div class="modal fade" id="appointmentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #F1F0CC;">
                <h5 class="modal-title" id="modalTitle">Schedule New Appointment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="appointmentForm" method="POST" action="process_appointment.php" onsubmit="return validateForm()">
                    <input type="hidden" name="action" id="formAction" value="add">
                    <input type="hidden" name="appointment_id" id="appointmentId">
                    <input type="hidden" name="doctor_id" value="<?= $authenticatedDoctorId ?>">
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Patient *</label>
                            <select class="form-select" name="patient_id" id="patientSelect" required>
                                <option value="" selected disabled>Select Patient</option>
                                <?php foreach ($patients as $patient): ?>
                                    <option value="<?= $patient->getPatientId() ?>">
                                        <?= htmlspecialchars($patient->getFullName()) ?> - #P<?= str_pad($patient->getPatientId(), 3, '0', STR_PAD_LEFT) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Appointment Date *</label>
                            <input type="date" class="form-control" name="appointment_date" id="appointmentDate" min="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Appointment Time *</label>
                            <input type="time" class="form-control" name="appointment_time" id="appointmentTime" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" id="appointmentStatus">
                                <option value="Pending" selected>Pending</option>
                                <option value="Confirmed">Confirmed</option>
                                <option value="Completed">Completed</option>
                                <option value="Cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Reason for Visit *</label>
                            <textarea class="form-control" name="reason" id="appointmentReason" rows="3" required></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" name="notes" id="appointmentNotes" rows="2"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="appointmentForm" class="btn btn-primary" style="background-color: #3F0D12; border-color: #3F0D12;">Save Appointment</button>
            </div>
        </div>
    </div>
</div>

<script>
function validateForm() {
    const patientId = document.getElementById('patientSelect').value;
    const date = document.getElementById('appointmentDate').value;
    const time = document.getElementById('appointmentTime').value;
    const reason = document.getElementById('appointmentReason').value.trim();

    if (!patientId || !date || !time || !reason) {
        alert('Please fill all required fields');
        return false;
    }

    return true;
}

function openAddModal() {
    document.getElementById('modalTitle').textContent = 'Schedule New Appointment';
    document.getElementById('appointmentForm').reset();
    document.getElementById('formAction').value = 'add';
    document.getElementById('appointmentId').value = '';
    document.getElementById('patientSelect').value = '';
}

function editAppointment(id) {
    document.getElementById('modalTitle').textContent = 'Edit Appointment';
    document.getElementById('formAction').value = 'edit';
    document.getElementById('appointmentId').value = id;

    fetch(`get_appointment.php?id=${id}`)
        .then(res => res.json())
        .then(data => {
            if (data.error) {
                alert('Error: ' + data.error);
                return;
            }
            document.getElementById('patientSelect').value = data.patient_id;
            document.getElementById('appointmentDate').value = data.appointment_date;
            document.getElementById('appointmentTime').value = data.appointment_time;
            document.getElementById('appointmentStatus').value = data.status;
            document.getElementById('appointmentReason').value = data.reason;
            document.getElementById('appointmentNotes').value = data.notes || '';
        })
        .catch(err => {
            alert('Error loading appointment data');
            console.error(err);
        });
}

function viewAppointment(id) {
    window.location.href = `view_appointment.php?id=${id}`;
}

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
                location.reload();
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

function searchTable() {
    const input = document.getElementById('searchInput');
    const filter = input.value.toUpperCase();
    const table = document.getElementById('appointmentsTable');
    const tr = table.getElementsByTagName('tr');

    for (let i = 1; i < tr.length; i++) {
        let visible = false;
        const td = tr[i].getElementsByTagName('td');
        
        for (let j = 0; j < td.length; j++) {
            if (td[j]) {
                const txtValue = td[j].textContent || td[j].innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    visible = true;
                    break;
                }
            }
        }
        
        tr[i].style.display = visible ? '' : 'none';
    }
}

function filterTable() {
    document.querySelector('form').submit();
}
</script>

<?php require_once '../../includes/footer.php'; ?>