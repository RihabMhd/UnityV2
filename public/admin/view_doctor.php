<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Repositories\DoctorRepository;
use Config\Database;

$page_title = "View Doctor";
require_once '../../includes/auth_check.php';
require_once '../../includes/header.php';
require_once '../../includes/navbar.php';


if (!isset($_GET['id'])) {
    header('Location: doctors.php');
    exit;
}

$doctorId = (int)$_GET['id'];

$db = (new Database())->connect();
$doctorRepo = new DoctorRepository($db);
$doctorData = $doctorRepo->findWithUserDetails($doctorId);

if (!$doctorData) {
    $_SESSION['error'] = 'Doctor not found';
    header('Location: doctors.php');
    exit;
}
?>

<div class="main-content">
    <div class="content-header">
        <button class="mobile-toggle" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>
        <div class="flex-grow-1">
            <h4 class="mb-0">Doctor Details</h4>
            <p class="text-muted mb-0">View doctor information</p>
        </div>
        <a href="doctors.php" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to List
        </a>
    </div>

    <div class="container-fluid">
        <div class="row">
            <!-- Doctor Profile Card -->
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-body text-center">
                        <div class="avatar-lg mx-auto mb-3" style="background-color: #3F0D12; width: 100px; height: 100px; display: flex; align-items: center; justify-content: center; border-radius: 50%; font-size: 2.5rem; color: white;">
                            <i class="fas fa-user-md"></i>
                        </div>
                        <h5 class="mb-1">Dr. <?= htmlspecialchars($doctorData['first_name'] . ' ' . $doctorData['last_name']) ?></h5>
                        <p class="text-muted mb-2">
                            <i class="fas fa-id-badge me-2"></i>Doctor #D<?= str_pad($doctorData['doctor_id'], 3, '0', STR_PAD_LEFT) ?>
                        </p>
                        <span class="badge" style="background-color: #8D775F;"><?= htmlspecialchars($doctorData['specialization']) ?></span>
                        
                        <hr class="my-3">
                        
                        <div class="text-start">
                            <p class="mb-2"><strong>Department:</strong> 
                                <?= htmlspecialchars($doctorData['department_name'] ?? 'N/A') ?>
                            </p>
                            <p class="mb-2"><strong>Location:</strong> <?= htmlspecialchars($doctorData['location'] ?? 'N/A') ?></p>
                            <p class="mb-2"><strong>Member Since:</strong> <?= date('M d, Y', strtotime($doctorData['created_at'])) ?></p>
                        </div>

                        <hr class="my-3">

                        <div class="d-grid gap-2">
                            <button class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#editModal">
                                <i class="fas fa-edit me-2"></i>Edit Doctor
                            </button>
                            <button class="btn btn-outline-danger" onclick="deleteDoctor(<?= $doctorData['doctor_id'] ?>)">
                                <i class="fas fa-trash me-2"></i>Delete Doctor
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Doctor Information -->
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
                                    <a href="mailto:<?= htmlspecialchars($doctorData['email']) ?>">
                                        <?= htmlspecialchars($doctorData['email']) ?>
                                    </a>
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small">Phone Number</label>
                                <p class="mb-0">
                                    <i class="fas fa-phone me-2 text-success"></i>
                                    <a href="tel:<?= htmlspecialchars($doctorData['phone_number']) ?>">
                                        <?= htmlspecialchars($doctorData['phone_number']) ?>
                                    </a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Account Information -->
                <div class="card mb-4">
                    <div class="card-header" style="background-color: #F1F0CC;">
                        <h5 class="mb-0"><i class="fas fa-user-lock me-2"></i>Account Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small">Username</label>
                                <p class="mb-0">
                                    <i class="fas fa-user me-2"></i>
                                    <?= htmlspecialchars($doctorData['username']) ?>
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small">Role</label>
                                <p class="mb-0">
                                    <i class="fas fa-user-tag me-2"></i>
                                    <span class="badge bg-info"><?= ucfirst($doctorData['role']) ?></span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Professional Information -->
                <div class="card mb-4">
                    <div class="card-header" style="background-color: #F1F0CC;">
                        <h5 class="mb-0"><i class="fas fa-stethoscope me-2"></i>Professional Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small">Specialization</label>
                                <p class="mb-0">
                                    <i class="fas fa-graduation-cap me-2 text-primary"></i>
                                    <?= htmlspecialchars($doctorData['specialization']) ?>
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small">Department</label>
                                <p class="mb-0">
                                    <i class="fas fa-building me-2 text-success"></i>
                                    <?= htmlspecialchars($doctorData['department_name'] ?? 'N/A') ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Patients / Appointments (placeholder) -->
                <div class="card">
                    <div class="card-header" style="background-color: #F1F0CC;">
                        <h5 class="mb-0"><i class="fas fa-users me-2"></i>Assigned Patients</h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-user-injured fa-3x mb-3 d-block"></i>
                            <p class="mb-0">No patients assigned yet</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #F1F0CC;">
                <h5 class="modal-title">Edit Doctor Information</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editForm" method="POST" action="process_doctor.php">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="doctor_id" value="<?= $doctorData['doctor_id'] ?>">
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">First Name *</label>
                            <input type="text" class="form-control" name="first_name" value="<?= htmlspecialchars($doctorData['first_name']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Last Name *</label>
                            <input type="text" class="form-control" name="last_name" value="<?= htmlspecialchars($doctorData['last_name']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Specialization *</label>
                            <input type="text" class="form-control" name="specialization" value="<?= htmlspecialchars($doctorData['specialization']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Department *</label>
                            <select class="form-select" name="department_id" required>
                                <?php
                                use Repositories\DepartmentRepository;
                                $deptRepo = new DepartmentRepository($db);
                                $departments = $deptRepo->findAll();
                                foreach ($departments as $dept): ?>
                                    <option value="<?= $dept->getDepartmentId() ?>" <?= $dept->getDepartmentId() == $doctorData['department_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($dept->getDepartmentName()) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone Number *</label>
                            <input type="tel" class="form-control" name="phone_number" value="<?= htmlspecialchars($doctorData['phone_number']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email *</label>
                            <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($doctorData['email']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Username *</label>
                            <input type="text" class="form-control" name="username" value="<?= htmlspecialchars($doctorData['username']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">New Password <small class="text-muted">(leave blank to keep current)</small></label>
                            <input type="password" class="form-control" name="password">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="editForm" class="btn btn-primary" style="background-color: #8D775F; border-color: #8D775F;">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<script>
function deleteDoctor(id) {
    if (confirm('Are you sure you want to delete this doctor? This action cannot be undone.')) {
        fetch('delete_doctor.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ doctor_id: id })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert('Doctor deleted successfully');
                window.location.href = 'doctors.php';
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(err => {
            alert('Error deleting doctor');
            console.error(err);
        });
    }
}
</script>

<?php require_once '../../includes/footer.php'; ?>