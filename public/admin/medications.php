<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Repositories\MedicationRepository;
use Config\Database;

$page_title = "Medications Management";
require_once '../../includes/auth_check.php';
$router->requireRole('admin'); 
require_once '../../includes/header.php';
require_once '../../includes/navbar.php';

$db = (new Database)->connect();
$medicationRepo = new MedicationRepository($db);
$medications = $medicationRepo->findAll();
?>

<div class="main-content">
    <div class="content-header">
        <button class="mobile-toggle" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>
        <div class="flex-grow-1">
            <h4 class="mb-0">Medications Management</h4>
            <p class="text-muted mb-0">Total Medications: <?= count($medications) ?></p>
        </div>
        <button class="btn btn-primary" style="background-color: #A71D31; border-color: #A71D31;" data-bs-toggle="modal" data-bs-target="#medicationModal" onclick="openAddModal()">
            <i class="fas fa-plus-circle me-2"></i>Add New Medication
        </button>
    </div>

    <div class="container-fluid">
        <!-- Search and Filter -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-8">
                        <input type="text" class="form-control" placeholder="Search medications..." id="searchInput" onkeyup="searchMedications()">
                    </div>
                    <div class="col-md-4">
                        <button class="btn btn-outline-secondary w-100" onclick="resetSearch()">
                            <i class="fas fa-redo me-2"></i>Reset
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Medications Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="medicationsTable">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Stock Quantity</th>
                                <th>Unit Price</th>
                                <th>Expiry Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($medications)): ?>
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-5">
                                        <i class="fas fa-pills fa-4x mb-3"></i>
                                        <h5>No medications found</h5>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($medications as $med): 
                                    $stockQty = $med->getStockQuantity();
                                    if ($stockQty == 0) {
                                        $stockBadge = '<span class="badge badge-danger">Out of Stock</span>';
                                        $stockPercentage = 0;
                                        $stockColor = 'danger';
                                        $textClass = 'text-danger';
                                    } elseif ($stockQty <= 200) {
                                        $stockBadge = '<span class="badge badge-warning">Low Stock</span>';
                                        $stockPercentage = 30;
                                        $stockColor = 'warning';
                                        $textClass = 'text-warning';
                                    } else {
                                        $stockBadge = '<span class="badge badge-success">In Stock</span>';
                                        $stockPercentage = 85;
                                        $stockColor = 'success';
                                        $textClass = '';
                                    }
                                ?>
                                <tr class="medication-row">
                                    <td><strong><?= htmlspecialchars($med->getCode()) ?></strong></td>
                                    <td class="medication-name">
                                        <div>
                                            <strong><?= htmlspecialchars($med->getMedicationName()) ?></strong>
                                            <br><small class="text-muted"><?= htmlspecialchars($med->getDosage()) ?></small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="me-2 <?= $textClass ?>"><?= number_format($stockQty) ?></span>
                                            <div class="progress" style="width: 60px; height: 6px;">
                                                <div class="progress-bar bg-<?= $stockColor ?>" style="width: <?= $stockPercentage ?>%"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>$<?= number_format($med->getUnitPrice(), 2) ?></td>
                                    <td><?= date('M Y', strtotime($med->getExpiryDate())) ?></td>
                                    <td><?= $stockBadge ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" onclick="viewMedication(<?= $med->getMedicationId() ?>)" title="View">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#medicationModal" onclick="editMedication(<?= $med->getMedicationId() ?>)" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" onclick="deleteMedication(<?= $med->getMedicationId() ?>)" title="Delete">
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

<!-- Add/Edit Medication Modal -->
<div class="modal fade" id="medicationModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #F1F0CC;">
                <h5 class="modal-title" id="modalTitle">Add New Medication</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="medicationForm" method="POST" action="process_medication.php">
                    <input type="hidden" name="action" id="formAction" value="add">
                    <input type="hidden" name="medication_id" id="medicationId">
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Medication Name *</label>
                            <input type="text" class="form-control" name="medication_name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Code *</label>
                            <input type="text" class="form-control" name="code" placeholder="e.g., M001" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Dosage *</label>
                            <input type="text" class="form-control" name="dosage" placeholder="e.g., 500mg, Tablets" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Category *</label>
                            <input type="text" class="form-control" name="category" placeholder="e.g., Antibiotics" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Manufacturer *</label>
                            <input type="text" class="form-control" name="manufacturer" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Stock Quantity *</label>
                            <input type="number" class="form-control" name="stock_quantity" min="0" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Unit Price *</label>
                            <input type="number" step="0.01" class="form-control" name="unit_price" min="0" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Expiry Date *</label>
                            <input type="date" class="form-control" name="expiry_date" required>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="medicationForm" class="btn btn-primary" style="background-color: #A71D31; border-color: #A71D31;">Save Medication</button>
            </div>
        </div>
    </div>
</div>

<script>
function openAddModal() {
    document.getElementById('modalTitle').textContent = 'Add New Medication';
    document.getElementById('medicationForm').reset();
    document.getElementById('formAction').value = 'add';
}

function editMedication(id) {
    document.getElementById('modalTitle').textContent = 'Edit Medication';
    document.getElementById('formAction').value = 'edit';
    document.getElementById('medicationId').value = id;

    fetch(`get_medication.php?id=${id}`)
        .then(res => res.json())
        .then(data => {
            document.querySelector('[name="medication_name"]').value = data.medication_name;
            document.querySelector('[name="code"]').value = data.code;
            document.querySelector('[name="dosage"]').value = data.dosage;
            document.querySelector('[name="category"]').value = data.category;
            document.querySelector('[name="manufacturer"]').value = data.manufacturer;
            document.querySelector('[name="stock_quantity"]').value = data.stock_quantity;
            document.querySelector('[name="unit_price"]').value = data.unit_price;
            document.querySelector('[name="expiry_date"]').value = data.expiry_date;
        })
        .catch(err => {
            alert('Error loading medication data');
            console.error(err);
        });
}

function viewMedication(id) {
    window.location.href = `view_medication.php?id=${id}`;
}

function deleteMedication(id) {
    if(confirm('Are you sure you want to delete this medication? This action cannot be undone.')) {
        fetch('delete_medication.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ medication_id: id })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert('Medication deleted successfully');
                location.reload();
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

function searchMedications() {
    const input = document.getElementById('searchInput');
    const filter = input.value.toUpperCase();
    const rows = document.querySelectorAll('.medication-row');

    rows.forEach(row => {
        const name = row.querySelector('.medication-name').textContent;
        if (name.toUpperCase().indexOf(filter) > -1) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function resetSearch() {
    document.getElementById('searchInput').value = '';
    const rows = document.querySelectorAll('.medication-row');
    rows.forEach(row => {
        row.style.display = '';
    });
}
</script>

<?php require_once '../../includes/footer.php'; ?>