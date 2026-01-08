<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Repositories\MedicationRepository;
use Config\Database;

$page_title = "Medication Details";
require_once '../../includes/auth_check.php';
require_once '../../includes/header.php';
require_once '../../includes/navbar.php';

// Get medication ID from URL
if (!isset($_GET['id'])) {
    header('Location: medications.php');
    exit;
}

$medicationId = (int)$_GET['id'];

try {
    $db = (new Database)->connect();
    $medicationRepo = new MedicationRepository($db);
    
    // Get medication details
    $medication = $medicationRepo->findById($medicationId);
    
    if (!$medication) {
        $_SESSION['error'] = 'Medication not found';
        header('Location: medications.php');
        exit;
    }
    
    // Get prescription count
    $stmt = $db->prepare("SELECT COUNT(*) FROM prescriptions WHERE medication_id = :med_id");
    $stmt->bindParam(':med_id', $medicationId);
    $stmt->execute();
    $prescriptionCount = $stmt->fetchColumn();
    
    // Calculate stock status
    $stockQty = $medication->getStockQuantity();
    if ($stockQty == 0) {
        $stockBadge = '<span class="badge badge-danger">Out of Stock</span>';
        $stockPercentage = 0;
        $stockColor = 'danger';
    } elseif ($stockQty <= 200) {
        $stockBadge = '<span class="badge badge-warning">Low Stock</span>';
        $stockPercentage = 30;
        $stockColor = 'warning';
    } else {
        $stockBadge = '<span class="badge badge-success">In Stock</span>';
        $stockPercentage = 85;
        $stockColor = 'success';
    }
    
    // Check if expired
    $expiryDate = new DateTime($medication->getExpiryDate());
    $today = new DateTime();
    $isExpired = $expiryDate < $today;
    $daysUntilExpiry = $today->diff($expiryDate)->days;
    
} catch (Exception $e) {
    $_SESSION['error'] = 'Error loading medication: ' . $e->getMessage();
    header('Location: medications.php');
    exit;
}
?>

<div class="main-content">
    <div class="content-header">
        <button class="mobile-toggle" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>
        <div class="flex-grow-1">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="medications.php">Medications</a></li>
                    <li class="breadcrumb-item active"><?= htmlspecialchars($medication->getMedicationName()) ?></li>
                </ol>
            </nav>
        </div>
        <a href="medications.php" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Medications
        </a>
    </div>

    <div class="container-fluid">
        <!-- Medication Info Card -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header" style="background-color: #F1F0CC;">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-pills me-2" style="color: #A71D31;"></i>
                                <?= htmlspecialchars($medication->getMedicationName()) ?>
                            </h5>
                            <?= $stockBadge ?>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-muted mb-3">Basic Information</h6>
                                <table class="table table-borderless">
                                    <tr>
                                        <td class="text-muted" style="width: 150px;"><i class="fas fa-barcode me-2"></i>Code:</td>
                                        <td><strong><?= htmlspecialchars($medication->getCode()) ?></strong></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted"><i class="fas fa-capsules me-2"></i>Dosage:</td>
                                        <td><strong><?= htmlspecialchars($medication->getDosage()) ?></strong></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted"><i class="fas fa-tag me-2"></i>Category:</td>
                                        <td><strong><?= htmlspecialchars($medication->getCategory()) ?></strong></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted"><i class="fas fa-industry me-2"></i>Manufacturer:</td>
                                        <td><strong><?= htmlspecialchars($medication->getManufacturer()) ?></strong></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted"><i class="fas fa-dollar-sign me-2"></i>Unit Price:</td>
                                        <td><strong>$<?= number_format($medication->getUnitPrice(), 2) ?></strong></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted mb-3">Stock & Expiry</h6>
                                <div class="mb-4">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Stock Quantity:</span>
                                        <strong><?= number_format($stockQty) ?> units</strong>
                                    </div>
                                    <div class="progress" style="height: 10px;">
                                        <div class="progress-bar bg-<?= $stockColor ?>" 
                                             style="width: <?= $stockPercentage ?>%"></div>
                                    </div>
                                </div>
                                
                                <div class="alert alert-<?= $isExpired ? 'danger' : 'info' ?> mb-3">
                                    <i class="fas fa-calendar-alt me-2"></i>
                                    <strong>Expiry Date:</strong> <?= date('M d, Y', strtotime($medication->getExpiryDate())) ?>
                                    <?php if ($isExpired): ?>
                                        <br><small><i class="fas fa-exclamation-triangle"></i> This medication has expired!</small>
                                    <?php elseif ($daysUntilExpiry <= 90): ?>
                                        <br><small><i class="fas fa-exclamation-circle"></i> Expires in <?= $daysUntilExpiry ?> days</small>
                                    <?php endif; ?>
                                </div>

                                <div class="stat-box" style="background-color: #F1F0CC; padding: 15px; border-radius: 8px;">
                                    <div class="d-flex align-items-center">
                                        <div class="stat-icon" style="background-color: #A71D31; width: 50px; height: 50px; border-radius: 10px; display: flex; justify-content: center; color: white; font-size: 24px;">
                                            <i class="fas fa-prescription"></i>
                                        </div>
                                        <div class="ms-3">
                                            <h4 class="mb-0"><?= $prescriptionCount ?></h4>
                                            <small class="text-muted">Total Prescriptions</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php if ($_SESSION['role'] === 'admin'): ?>
                        <div class="mt-4 pt-3 border-top">
                            <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editMedicationModal">
                                <i class="fas fa-edit me-2"></i>Edit Medication
                            </button>
                            <button class="btn btn-danger" onclick="confirmDelete()">
                                <i class="fas fa-trash me-2"></i>Delete Medication
                            </button>
                            <?php if ($stockQty <= 200): ?>
                            <button class="btn btn-success" onclick="reorderMedication()">
                                <i class="fas fa-redo me-2"></i>Reorder Stock
                            </button>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Prescriptions -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header" style="background-color: #F1F0CC;">
                        <h5 class="mb-0">
                            <i class="fas fa-prescription me-2" style="color: #A71D31;"></i>
                            Recent Prescriptions
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php
                        // Get recent prescriptions
                        $stmt = $db->prepare("
                            SELECT p.*, 
                                   pat.first_name, pat.last_name,
                                   d.first_name as doctor_first_name, d.last_name as doctor_last_name
                            FROM prescriptions p
                            JOIN patients pat ON p.patient_id = pat.patient_id
                            JOIN doctors d ON p.doctor_id = d.doctor_id
                            WHERE p.medication_id = :med_id
                            ORDER BY p.prescription_date DESC
                            LIMIT 10
                        ");
                        $stmt->bindParam(':med_id', $medicationId);
                        $stmt->execute();
                        $prescriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        ?>
                        
                        <?php if (empty($prescriptions)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-prescription fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No prescriptions found for this medication.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Prescription ID</th>
                                            <th>Patient</th>
                                            <th>Doctor</th>
                                            <th>Date</th>
                                            <th>Dosage Instructions</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($prescriptions as $prescription): ?>
                                        <tr>
                                            <td><strong>#<?= $prescription['prescription_id'] ?></strong></td>
                                            <td><?= htmlspecialchars($prescription['first_name'] . ' ' . $prescription['last_name']) ?></td>
                                            <td>Dr. <?= htmlspecialchars($prescription['doctor_first_name'] . ' ' . $prescription['doctor_last_name']) ?></td>
                                            <td><?= date('M d, Y', strtotime($prescription['prescription_date'])) ?></td>
                                            <td><?= htmlspecialchars($prescription['dosage_instructions'] ?? 'N/A') ?></td>
                                            <td>
                                                <a href="../prescriptions/view_prescription.php?id=<?= $prescription['prescription_id'] ?>" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
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
    </div>
</div>

<!-- Edit Medication Modal -->
<div class="modal fade" id="editMedicationModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #F1F0CC;">
                <h5 class="modal-title">Edit Medication</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editMedicationForm" method="POST" action="process_medication.php">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="medication_id" value="<?= $medication->getMedicationId() ?>">
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Medication Name *</label>
                            <input type="text" class="form-control" name="medication_name" 
                                   value="<?= htmlspecialchars($medication->getMedicationName()) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Code *</label>
                            <input type="text" class="form-control" name="code" 
                                   value="<?= htmlspecialchars($medication->getCode()) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Dosage *</label>
                            <input type="text" class="form-control" name="dosage" 
                                   value="<?= htmlspecialchars($medication->getDosage()) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Category *</label>
                            <input type="text" class="form-control" name="category" 
                                   value="<?= htmlspecialchars($medication->getCategory()) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Manufacturer *</label>
                            <input type="text" class="form-control" name="manufacturer" 
                                   value="<?= htmlspecialchars($medication->getManufacturer()) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Stock Quantity *</label>
                            <input type="number" class="form-control" name="stock_quantity" 
                                   value="<?= $medication->getStockQuantity() ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Unit Price *</label>
                            <input type="number" step="0.01" class="form-control" name="unit_price" 
                                   value="<?= $medication->getUnitPrice() ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Expiry Date *</label>
                            <input type="date" class="form-control" name="expiry_date" 
                                   value="<?= $medication->getExpiryDate() ?>" required>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="editMedicationForm" class="btn btn-primary" 
                        style="background-color: #A71D31; border-color: #A71D31;">
                    Update Medication
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete() {
    if(confirm('Are you sure you want to delete this medication? This action cannot be undone.')) {
        fetch('delete_medication.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ medication_id: <?= $medication->getMedicationId() ?> })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert('Medication deleted successfully');
                window.location.href = 'medications.php';
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(err => {
            alert('Error deleting medication');
            console.error(err);
        });
    }
}

function reorderMedication() {
    const quantity = prompt('Enter quantity to reorder:');
    if (quantity && parseInt(quantity) > 0) {
        alert('Reorder request created for ' + quantity + ' units. Stock will be updated upon delivery.');
        // Here you would typically send this to a backend endpoint
    }
}
</script>

<style>
.table-borderless td {
    padding: 0.5rem 0;
}

.breadcrumb {
    background: none;
    padding: 0;
}

.breadcrumb-item + .breadcrumb-item::before {
    content: "›";
}
</style>

<?php require_once '../../includes/footer.php'; ?>