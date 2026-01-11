<?php
$page_title = "My Patients";
require_once '../../includes/auth_check.php';
$router->requireRole('doctor');
require_once '../../includes/header.php';
require_once '../../includes/navbar.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use Repositories\PatientRepository;
use Config\Database;
use Repositories\DoctorRepository;

$db = (new Database())->connect();
$patientsRepo = new PatientRepository($db);
$doctorRepo = new DoctorRepository($db);

// Get current doctor
$currentDoctor = $doctorRepo->findById($_SESSION['user_id']);

if (!$currentDoctor) {
    $_SESSION['error'] = 'Doctor profile not found. Please contact administrator.';
    header('Location: ../../index.php');
    exit;
}

// Get only patients for this doctor
$patients = $patientsRepo->findByDoctor($currentDoctor->getDoctorId());
?>

<div class="main-content">
    <div class="content-header">
        <button class="mobile-toggle" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>
        <div class="flex-grow-1">
            <h4 class="mb-0">My Patients</h4>
            <p class="text-muted mb-0">Total Patients: <?= count($patients) ?></p>
        </div>
    </div>

    <div class="container-fluid">
        <!-- Success/Error Messages -->
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

        <!-- Search -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-10">
                        <input type="text" class="form-control" placeholder="Search patients..." id="searchInput" onkeyup="searchTable()">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-outline-secondary w-100" onclick="resetFilters()">
                            <i class="fas fa-redo me-2"></i>Reset
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Patients Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="patientsTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Age</th>
                                <th>Contact</th>
                                <th>Email</th>
                                <th>DOB</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($patients)): ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                        No patients assigned to you yet
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($patients as $patient): ?>
                                    <tr>
                                        <td><strong>#P<?= str_pad($patient->getPatientId(), 3, '0', STR_PAD_LEFT) ?></strong></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm me-2" style="background-color: <?= ['#A71D31', '#3F0D12', '#8D775F', '#D5BF86'][($patient->getPatientId() % 4)] ?>;">
                                                    <?= strtoupper(substr($patient->getFirstName(), 0, 1) . substr($patient->getLastName(), 0, 1)) ?>
                                                </div>
                                                <span><?= htmlspecialchars($patient->getFullName()) ?></span>
                                            </div>
                                        </td>
                                        <td><?= $patient->getAge() ?></td>
                                        <td><?= htmlspecialchars($patient->getPhoneNumber()) ?></td>
                                        <td><?= htmlspecialchars($patient->getEmail()) ?></td>
                                        <td><?= date('M d, Y', strtotime($patient->getDateOfBirth())) ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary" onclick="viewPatient(<?= $patient->getPatientId() ?>)" title="View">
                                                <i class="fas fa-eye"></i>
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

<script>
    function viewPatient(id) {
        window.location.href = `view_patient.php?id=${id}`;
    }

    function searchTable() {
        const input = document.getElementById('searchInput');
        const filter = input.value.toUpperCase();
        const table = document.getElementById('patientsTable');
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

    function resetFilters() {
        document.getElementById('searchInput').value = '';
        
        const table = document.getElementById('patientsTable');
        const tr = table.getElementsByTagName('tr');
        for (let i = 1; i < tr.length; i++) {
            tr[i].style.display = '';
        }
    }
</script>

<?php require_once '../../includes/footer.php'; ?>