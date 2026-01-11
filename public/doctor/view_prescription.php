<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Repositories\PrescriptionRepository;
use Repositories\DoctorRepository;
use Config\Database;

$page_title = "View Prescription";
require_once '../../includes/auth_check.php';
$router->requireRole('doctor');
require_once '../../includes/header.php';
require_once '../../includes/navbar.php';

if (!isset($_GET['id'])) {
    header('Location: prescriptions.php');
    exit;
}

$db = (new Database)->connect();
$prescriptionRepo = new PrescriptionRepository($db);
$doctorRepo = new DoctorRepository($db);

$currentDoctor = $doctorRepo->findById($_SESSION['user_id']);

if (!$currentDoctor) {
    $_SESSION['error'] = 'Doctor profile not found';
    header('Location: prescriptions.php');
    exit;
}

$prescriptionId = (int)$_GET['id'];
$prescriptionDetails = $prescriptionRepo->findWithDetails($prescriptionId);

if (!$prescriptionDetails) {
    $_SESSION['error'] = 'Prescription not found.';
    header('Location: prescriptions.php');
    exit;
}

// Verify this doctor owns the prescription
if ($prescriptionDetails['doctor_id'] !== $currentDoctor->getDoctorId()) {
    $_SESSION['error'] = 'Unauthorized access to this prescription';
    header('Location: prescriptions.php');
    exit;
}
?>

<div class="main-content">
    <div class="content-header">
        <button class="mobile-toggle" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>
        <div class="flex-grow-1">
            <h4 class="mb-0">Prescription Details</h4>
            <p class="text-muted mb-0">View prescription information</p>
        </div>
        <a href="prescriptions.php" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to List
        </a>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-8">
                <!-- Prescription Information Card -->
                <div class="card mb-4">
                    <div class="card-header" style="background-color: #F1F0CC;">
                        <h5 class="mb-0">
                            <i class="fas fa-prescription me-2"></i>Prescription #RX<?= str_pad($prescriptionDetails['prescription_id'], 3, '0', STR_PAD_LEFT) ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="text-muted small">Patient Name</label>
                                    <p class="mb-0 fw-bold">
                                        <i class="fas fa-user-injured me-2" style="color: #A71D31;"></i>
                                        <?= htmlspecialchars($prescriptionDetails['patient_name']) ?>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="text-muted small">Patient Phone</label>
                                    <p class="mb-0">
                                        <i class="fas fa-phone me-2" style="color: #A71D31;"></i>
                                        <?= htmlspecialchars($prescriptionDetails['patient_phone']) ?>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="text-muted small">Doctor Name</label>
                                    <p class="mb-0 fw-bold">
                                        <i class="fas fa-user-md me-2" style="color: #3F0D12;"></i>
                                        <?= htmlspecialchars($prescriptionDetails['doctor_name']) ?>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="text-muted small">Specialization</label>
                                    <p class="mb-0">
                                        <i class="fas fa-stethoscope me-2" style="color: #3F0D12;"></i>
                                        <?= htmlspecialchars($prescriptionDetails['doctor_specialization']) ?>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="text-muted small">Prescription Date</label>
                                    <p class="mb-0">
                                        <i class="fas fa-calendar me-2" style="color: #A71D31;"></i>
                                        <?= date('l, F d, Y', strtotime($prescriptionDetails['prescription_date'])) ?>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="text-muted small">Medication</label>
                                    <p class="mb-0">
                                        <i class="fas fa-pills me-2" style="color: #8D775F;"></i>
                                        <strong><?= htmlspecialchars($prescriptionDetails['medication_name']) ?></strong>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="text-muted small">Medication Dosage</label>
                                    <p class="mb-0">
                                        <i class="fas fa-prescription-bottle me-2" style="color: #8D775F;"></i>
                                        <?= htmlspecialchars($prescriptionDetails['medication_dosage']) ?>
                                    </p>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="info-item">
                                    <label class="text-muted small">Dosage Instructions</label>
                                    <div class="alert alert-light mb-0">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <?= nl2br(htmlspecialchars($prescriptionDetails['dosage_instructions'])) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Quick Actions Card -->
                <div class="card">
                    <div class="card-header" style="background-color: #F1F0CC;">
                        <h5 class="mb-0">
                            <i class="fas fa-bolt me-2"></i>Quick Actions
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button class="btn btn-warning" onclick="editPrescription(<?= $prescriptionDetails['prescription_id'] ?>)">
                                <i class="fas fa-edit me-2"></i>Edit Prescription
                            </button>
                            
                            <button class="btn btn-info text-white" onclick="printPrescription()">
                                <i class="fas fa-print me-2"></i>Print Prescription
                            </button>
                            
                            <button class="btn btn-outline-danger" onclick="deletePrescription(<?= $prescriptionDetails['prescription_id'] ?>)">
                                <i class="fas fa-trash me-2"></i>Delete Prescription
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
function editPrescription(id) {
    window.location.href = 'prescriptions.php#edit-' + id;
}

function deletePrescription(id) {
    if(confirm('Are you sure you want to permanently delete this prescription? This action cannot be undone.')) {
        fetch('delete_prescription.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ prescription_id: id })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert('Prescription deleted successfully');
                window.location.href = 'prescriptions.php';
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(err => {
            alert('Error deleting prescription');
            console.error(err);
        });
    }
}

function printPrescription() {
    window.print();
}
</script>

<?php require_once '../../includes/footer.php'; ?>