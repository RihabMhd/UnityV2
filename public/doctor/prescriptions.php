<?php
$page_title = "My Prescriptions";
require_once '../../includes/auth_check.php';
$router->requireRole('doctor');
require_once '../../includes/header.php';
require_once '../../includes/navbar.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use Repositories\PrescriptionRepository;
use Repositories\PatientRepository;
use Repositories\MedicationRepository;
use Repositories\DoctorRepository;
use Config\Database;

$db = (new Database)->connect();
$prescriptionsRepo = new PrescriptionRepository($db);
$patientsRepo = new PatientRepository($db);
$medicationsRepo = new MedicationRepository($db);
$doctorsRepo = new DoctorRepository($db);

$authenticatedUserId = $_SESSION['user_id'];

$currentDoctor = $doctorsRepo->findById($authenticatedUserId);

if (!$currentDoctor) {
    $_SESSION['error'] = 'Doctor profile not found. Please contact administrator.';
    header('Location: ../../index.php');
    exit;
}

$authenticatedDoctorId = $currentDoctor->getDoctorId();

// Get prescriptions for this doctor
$prescriptions = $prescriptionsRepo->findByDoctor($authenticatedDoctorId);
$patients = $patientsRepo->findByDoctor($authenticatedDoctorId);
$medications = $medicationsRepo->findAll();

// Filter prescriptions
$filteredPrescriptions = $prescriptions;
if (isset($_GET['patient']) && !empty($_GET['patient'])) {
    $filteredPrescriptions = array_filter($filteredPrescriptions, function($presc) {
        return $presc->getPatientId() == $_GET['patient'];
    });
}
if (isset($_GET['date']) && !empty($_GET['date'])) {
    $filteredPrescriptions = array_filter($filteredPrescriptions, function($presc) {
        return $presc->getPrescriptionDate() === $_GET['date'];
    });
}
?>

<div class="main-content">
    <div class="content-header">
        <button class="mobile-toggle" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>
        <div class="flex-grow-1">
            <h4 class="mb-0">My Prescriptions</h4>
            <p class="text-muted mb-0">Total Prescriptions: <?= count($prescriptions) ?></p>
        </div>
        <button class="btn btn-primary" style="background-color: #A71D31; border-color: #A71D31;" data-bs-toggle="modal" data-bs-target="#prescriptionModal" onclick="openAddModal()">
            <i class="fas fa-plus-circle me-2"></i>New Prescription
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
                            <select class="form-select" name="patient" id="patientFilter" onchange="filterTable()">
                                <option value="">All Patients</option>
                                <?php foreach ($patients as $patient): ?>
                                    <option value="<?= $patient->getPatientId() ?>" <?= (isset($_GET['patient']) && $_GET['patient'] == $patient->getPatientId()) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($patient->getFullName()) ?>
                                    </option>
                                <?php endforeach; ?>
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

        <!-- Prescriptions Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="prescriptionsTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Patient</th>
                                <th>Medication</th>
                                <th>Date</th>
                                <th>Dosage Instructions</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($filteredPrescriptions)): ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <i class="fas fa-prescription fa-3x mb-3 d-block"></i>
                                        No prescriptions found
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($filteredPrescriptions as $presc): ?>
                                    <?php
                                    // Get patient and medication names
                                    $patient = null;
                                    foreach ($patients as $p) {
                                        if ($p->getPatientId() == $presc->getPatientId()) {
                                            $patient = $p;
                                            break;
                                        }
                                    }
                                    
                                    $medication = null;
                                    foreach ($medications as $m) {
                                        if ($m->getMedicationId() == $presc->getMedicationId()) {
                                            $medication = $m;
                                            break;
                                        }
                                    }
                                    ?>
                                    <tr>
                                        <td><strong>#RX<?= str_pad($presc->getPrescriptionId(), 3, '0', STR_PAD_LEFT) ?></strong></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm me-2">
                                                    <?php 
                                                    if ($patient) {
                                                        $pName = $patient->getFullName();
                                                        $initials = strtoupper(substr($patient->getFirstName(), 0, 1) . substr($patient->getLastName(), 0, 1));
                                                        echo $initials;
                                                    } else {
                                                        echo 'NA';
                                                    }
                                                    ?>
                                                </div>
                                                <span><?= $patient ? htmlspecialchars($patient->getFullName()) : 'Unknown Patient' ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <strong><?= $medication ? htmlspecialchars($medication->getMedicationName()) : 'Unknown Medication' ?></strong>
                                            <?php if ($medication): ?>
                                                <br><small class="text-muted"><?= htmlspecialchars($medication->getDosage()) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= date('M d, Y', strtotime($presc->getPrescriptionDate())) ?></td>
                                        <td>
                                            <small><?= htmlspecialchars(substr($presc->getDosageInstructions(), 0, 50)) ?><?= strlen($presc->getDosageInstructions()) > 50 ? '...' : '' ?></small>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary" onclick="viewPrescription(<?= $presc->getPrescriptionId() ?>)" title="View">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#prescriptionModal" onclick="editPrescription(<?= $presc->getPrescriptionId() ?>)" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" onclick="deletePrescription(<?= $presc->getPrescriptionId() ?>)" title="Delete">
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

<!-- Add/Edit Prescription Modal -->
<div class="modal fade" id="prescriptionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #F1F0CC;">
                <h5 class="modal-title" id="modalTitle">New Prescription</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="prescriptionForm" method="POST" action="process_prescription.php" onsubmit="return validateForm()">
                    <input type="hidden" name="action" id="formAction" value="add">
                    <input type="hidden" name="prescription_id" id="prescriptionId">
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
                            <label class="form-label">Medication *</label>
                            <select class="form-select" name="medication_id" id="medicationSelect" required>
                                <option value="" selected disabled>Select Medication</option>
                                <?php foreach ($medications as $medication): ?>
                                    <option value="<?= $medication->getMedicationId() ?>" data-dosage="<?= htmlspecialchars($medication->getDosage()) ?>">
                                        <?= htmlspecialchars($medication->getMedicationName()) ?> - <?= htmlspecialchars($medication->getDosage()) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Prescription Date *</label>
                            <input type="date" class="form-control" name="prescription_date" id="prescriptionDate" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Dosage Instructions *</label>
                            <textarea class="form-control" name="dosage_instructions" id="dosageInstructions" rows="4" placeholder="e.g., Take 1 tablet twice daily after meals for 7 days" required></textarea>
                            <small class="text-muted">Provide detailed instructions on how the patient should take this medication</small>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="prescriptionForm" class="btn btn-primary" style="background-color: #3F0D12; border-color: #3F0D12;">Save Prescription</button>
            </div>
        </div>
    </div>
</div>

<script>
function validateForm() {
    const patientId = document.getElementById('patientSelect').value;
    const medicationId = document.getElementById('medicationSelect').value;
    const date = document.getElementById('prescriptionDate').value;
    const instructions = document.getElementById('dosageInstructions').value.trim();

    if (!patientId || !medicationId || !date || !instructions) {
        alert('Please fill all required fields');
        return false;
    }

    return true;
}

function openAddModal() {
    document.getElementById('modalTitle').textContent = 'New Prescription';
    document.getElementById('prescriptionForm').reset();
    document.getElementById('formAction').value = 'add';
    document.getElementById('prescriptionId').value = '';
    document.getElementById('patientSelect').value = '';
    document.getElementById('medicationSelect').value = '';
    document.getElementById('prescriptionDate').value = '<?= date('Y-m-d') ?>';
}

function editPrescription(id) {
    document.getElementById('modalTitle').textContent = 'Edit Prescription';
    document.getElementById('formAction').value = 'edit';
    document.getElementById('prescriptionId').value = id;

    fetch(`get_prescription.php?id=${id}`)
        .then(res => res.json())
        .then(data => {
            if (data.error) {
                alert('Error: ' + data.error);
                return;
            }
            document.getElementById('patientSelect').value = data.patient_id;
            document.getElementById('medicationSelect').value = data.medication_id;
            document.getElementById('prescriptionDate').value = data.prescription_date;
            document.getElementById('dosageInstructions').value = data.dosage_instructions;
        })
        .catch(err => {
            alert('Error loading prescription data');
            console.error(err);
        });
}

function viewPrescription(id) {
    window.location.href = `view_prescription.php?id=${id}`;
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
                location.reload();
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

function searchTable() {
    const input = document.getElementById('searchInput');
    const filter = input.value.toUpperCase();
    const table = document.getElementById('prescriptionsTable');
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