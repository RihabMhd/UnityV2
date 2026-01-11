<?php
use Repositories\PrescriptionRepository;
use Repositories\PatientRepository;
use Config\Database;

$page_title = "My Prescriptions";
require_once '../../includes/auth_check.php';
$router->requireRole('patient');
require_once '../../includes/header.php';
require_once '../../includes/navbar.php';
require_once __DIR__ . '/../../vendor/autoload.php';

$db = (new Database())->connect();
$prescriptionsRepo = new PrescriptionRepository($db);
$patientsRepo = new PatientRepository($db);

$authenticatedUserId = $_SESSION['user_id'];
$currentPatient = $patientsRepo->findById($authenticatedUserId);

if (!$currentPatient) {
    $_SESSION['error'] = 'Patient profile not found.';
    header('Location: index.php');
    exit;
}

$authenticatedPatientId = $currentPatient->getPatientId();
$prescriptions = $prescriptionsRepo->findByPatient($authenticatedPatientId);

$statusFilter = isset($_GET['status']) && !empty($_GET['status']) ? $_GET['status'] : 'all';
$filteredPrescriptions = $prescriptions;

if ($statusFilter !== 'all') {
    $filteredPrescriptions = array_filter($filteredPrescriptions, function($pres) use ($statusFilter) {
        return strtolower($pres->getStatus()) === strtolower($statusFilter);
    });
}
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
        </div>

        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="stat-card" style="background: linear-gradient(135deg, #3F0D12 0%, #A71D31 100%);">
                    <div class="stat-icon">
                        <i class="fas fa-prescription"></i>
                    </div>
                    <div class="stat-details">
                        <h3><?= count($prescriptions) ?></h3>
                        <p>Total Prescriptions</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="stat-card" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-details">
                        <h3><?= count(array_filter($prescriptions, fn($p) => strtolower($p->getStatus()) === 'active')) ?></h3>
                        <p>Active Prescriptions</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="stat-card" style="background: linear-gradient(135deg, #D5BF86 0%, #F1F0CC 100%); color: #3F0D12;">
                    <div class="stat-icon" style="color: #3F0D12;">
                        <i class="fas fa-history"></i>
                    </div>
                    <div class="stat-details">
                        <h3><?= count(array_filter($prescriptions, fn($p) => strtolower($p->getStatus()) === 'expired')) ?></h3>
                        <p>Expired Prescriptions</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <ul class="nav nav-pills">
                    <li class="nav-item">
                        <a class="nav-link <?= $statusFilter === 'all' ? 'active' : '' ?>" 
                           href="?status=all" 
                           style="<?= $statusFilter === 'all' ? 'background-color: #A71D31; border-color: #A71D31;' : 'color: #A71D31;' ?>">
                            All (<?= count($prescriptions) ?>)
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $statusFilter === 'active' ? 'active' : '' ?>" 
                           href="?status=active"
                           style="<?= $statusFilter === 'active' ? 'background-color: #28a745; border-color: #28a745;' : 'color: #28a745;' ?>">
                            Active
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $statusFilter === 'expired' ? 'active' : '' ?>" 
                           href="?status=expired"
                           style="<?= $statusFilter === 'expired' ? 'background-color: #dc3545; border-color: #dc3545;' : 'color: #dc3545;' ?>">
                            Expired
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-pills me-2"></i>
                    <?= ucfirst($statusFilter) ?> Prescriptions
                    <span class="badge bg-secondary ms-2"><?= count($filteredPrescriptions) ?></span>
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($filteredPrescriptions)): ?>
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-prescription-bottle fa-4x mb-3"></i>
                        <h5>No prescriptions found</h5>
                        <p>You don't have any <?= $statusFilter !== 'all' ? $statusFilter : '' ?> prescriptions.</p>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($filteredPrescriptions as $prescription): ?>
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="prescription-card">
                                    <div class="prescription-header" style="background: linear-gradient(135deg, #3F0D12 0%, #A71D31 100%);">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="text-white mb-1"><?= htmlspecialchars($prescription->getMedicationName()) ?></h6>
                                                <small class="text-white-50">
                                                    #RX<?= str_pad($prescription->getPrescriptionId(), 4, '0', STR_PAD_LEFT) ?>
                                                </small>
                                            </div>
                                            <span class="badge <?= strtolower($prescription->getStatus()) === 'active' ? 'bg-success' : 'bg-danger' ?>">
                                                <?= htmlspecialchars($prescription->getStatus()) ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="prescription-body">
                                        <div class="mb-3">
                                            <small class="text-muted d-block">Prescribed by</small>
                                            <strong><?= htmlspecialchars($prescription->getDoctorName() ?? 'Dr. Unknown') ?></strong>
                                        </div>
                                        <div class="mb-3">
                                            <small class="text-muted d-block">Dosage</small>
                                            <strong><?= htmlspecialchars($prescription->getDosage()) ?></strong>
                                        </div>
                                        <div class="mb-3">
                                            <small class="text-muted d-block">Frequency</small>
                                            <strong><?= htmlspecialchars($prescription->getFrequency()) ?></strong>
                                        </div>
                                        <div class="mb-3">
                                            <small class="text-muted d-block">Duration</small>
                                            <strong><?= htmlspecialchars($prescription->getDuration()) ?></strong>
                                        </div>
                                        <div class="mb-3">
                                            <small class="text-muted d-block">Date Prescribed</small>
                                            <strong><?= date('M d, Y', strtotime($prescription->getCreatedAt())) ?></strong>
                                        </div>
                                        <?php if ($prescription->getInstructions()): ?>
                                            <div class="alert alert-info mb-0">
                                                <small>
                                                    <i class="fas fa-info-circle me-1"></i>
                                                    <?= htmlspecialchars($prescription->getInstructions()) ?>
                                                </small>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="prescription-footer">
                                        <button class="btn btn-sm btn-outline-primary w-100" onclick="viewPrescription(<?= $prescription->getPrescriptionId() ?>)">
                                            <i class="fas fa-eye me-2"></i>View Details
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
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

.prescription-card {
    border: 1px solid #dee2e6;
    border-radius: 10px;
    overflow: hidden;
    transition: all 0.3s ease;
    height: 100%;
}

.prescription-card:hover {
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    transform: translateY(-5px);
}

.prescription-header {
    padding: 15px;
}

.prescription-body {
    padding: 20px;
}

.prescription-footer {
    padding: 15px;
    background-color: #f8f9fa;
    border-top: 1px solid #dee2e6;
}

.nav-pills .nav-link {
    border-radius: 20px;
    margin-right: 10px;
    transition: all 0.3s ease;
}

.nav-pills .nav-link:not(.active) {
    background-color: transparent;
    border: 1px solid currentColor;
}
</style>

<script>
function viewPrescription(prescriptionId) {
    window.location.href = 'view_prescription.php?id=' + prescriptionId;
}
</script>

<?php require_once '../../includes/footer.php'; ?>