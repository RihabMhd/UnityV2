<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Repositories\PatientRepository;
use Config\Database;

$page_title = "View Patient";
require_once '../../includes/auth_check.php';
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
$patientData = $patientRepo->findWithUserDetails($patientId);

if (!$patientData) {
    $_SESSION['error'] = 'Patient not found';
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

                        <hr class="my-3">

                        <div class="d-grid gap-2">
                            <button class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#editModal">
                                <i class="fas fa-edit me-2"></i>Edit Patient
                            </button>
                            <button class="btn btn-outline-danger" onclick="deletePatient(<?= $patientData['patient_id'] ?>)">
                                <i class="fas fa-trash me-2"></i>Delete Patient
                            </button>
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
                                    <?= htmlspecialchars($patientData['username']) ?>
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small">Role</label>
                                <p class="mb-0">
                                    <i class="fas fa-user-tag me-2"></i>
                                    <span class="badge bg-info"><?= ucfirst($patientData['role']) ?></span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Assigned Doctor -->
                <div class="card mb-4">
                    <div class="card-header" style="background-color: #F1F0CC;">
                        <h5 class="mb-0"><i class="fas fa-user-md me-2"></i>Assigned Doctor</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($patientData['doctor_name'])): ?>
                            <div class="d-flex align-items-center">
                                <div class="avatar-md me-3" style="background-color: #3F0D12; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; border-radius: 50%; color: white;">
                                    <i class="fas fa-user-md"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0"><?= htmlspecialchars($patientData['doctor_name']) ?></h6>
                                    <p class="text-muted mb-0 small"><?= htmlspecialchars($patientData['doctor_specialization']) ?></p>
                                </div>
                            </div>
                        <?php else: ?>
                            <p class="text-muted mb-0">
                                <i class="fas fa-info-circle me-2"></i>No doctor assigned yet
                            </p>
                        <?php endif; ?>
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
                            <p class="mb-0">No appointments scheduled</p>
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
                <h5 class="modal-title">Edit Patient Information</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editForm" method="POST" action="process_patient.php">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="patient_id" value="<?= $patientData['patient_id'] ?>">
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">First Name *</label>
                            <input type="text" class="form-control" name="first_name" value="<?= htmlspecialchars($patientData['first_name']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Last Name *</label>
                            <input type="text" class="form-control" name="last_name" value="<?= htmlspecialchars($patientData['last_name']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date of Birth *</label>
                            <input type="date" class="form-control" name="date_of_birth" value="<?= $patientData['date_of_birth'] ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone Number *</label>
                            <input type="tel" class="form-control" name="phone_number" value="<?= htmlspecialchars($patientData['phone_number']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email *</label>
                            <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($patientData['email']) ?>" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Address *</label>
                            <textarea class="form-control" name="address" rows="2" required><?= htmlspecialchars($patientData['address']) ?></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Username *</label>
                            <input type="text" class="form-control" name="username" value="<?= htmlspecialchars($patientData['username']) ?>" required>
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
                <button type="submit" form="editForm" class="btn btn-primary" style="background-color: #A71D31; border-color: #A71D31;">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<script>
function deletePatient(id) {
    if (confirm('Are you sure you want to delete this patient? This action cannot be undone.')) {
        fetch('delete_patient.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ patient_id: id })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert('Patient deleted successfully');
                window.location.href = 'patients.php';
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(err => {
            alert('Error deleting patient');
            console.error(err);
        });
    }
}
</script>

<?php require_once '../../includes/footer.php'; ?>
