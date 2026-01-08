<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Repositories\DepartmentRepository;
use Repositories\DoctorRepository;
use Config\Database;

$page_title = "Departments Management";
require_once '../../includes/auth_check.php';
$router->requireRole('admin'); 
require_once '../../includes/header.php';
require_once '../../includes/navbar.php';

$db = (new Database)->connect();
$departmentsRepo = new DepartmentRepository($db);
$departments = $departmentsRepo->findAll();

$doctorsRepo = new DoctorRepository($db);
$allDoctors = $doctorsRepo->findAll();
?>

<div class="main-content">
    <div class="content-header">
        <button class="mobile-toggle" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>
        <div class="flex-grow-1">
            <h4 class="mb-0">Departments Management</h4>
            <p class="text-muted mb-0">Total Departments: <?= count($departments) ?></p>
        </div>
        <button class="btn btn-primary" style="background-color: #A71D31; border-color: #A71D31;" data-bs-toggle="modal" data-bs-target="#departmentModal" onclick="openAddModal()">
            <i class="fas fa-plus-circle me-2"></i>Add New Department
        </button>
    </div>

    <div class="container-fluid">
        <!-- Search and Filter -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-8">
                        <input type="text" class="form-control" placeholder="Search departments..." id="searchInput" onkeyup="searchDepartments()">
                    </div>
                    <div class="col-md-4">
                        <button class="btn btn-outline-secondary w-100" onclick="resetSearch()">
                            <i class="fas fa-redo me-2"></i>Reset
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Departments Grid -->
        <div class="row" id="departmentsGrid">
            <?php if (empty($departments)): ?>
                <div class="col-12 text-center text-muted py-5">
                    <i class="fas fa-building fa-4x mb-3"></i>
                    <h5>No departments found</h5>
                </div>
            <?php else: ?>
                <?php 
                $colors = ['#A71D31', '#8D775F', '#3F0D12', '#D5BF86', '#A71D31'];
                $icons = ['fa-heartbeat', 'fa-brain', 'fa-bone', 'fa-baby', 'fa-x-ray'];
                foreach ($departments as $index => $dept): 
                    $doctorsInDept = array_filter($allDoctors, function($d) use ($dept) {
                        return $d->getDepartmentId() == $dept->getDepartmentId();
                    });
                    $doctorCount = count($doctorsInDept);
                ?>
                    <div class="col-lg-4 col-md-6 mb-4 department-card">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="dept-icon" style="background-color: <?= $colors[$index % count($colors)] ?>;">
                                        <i class="fas <?= $icons[$index % count($icons)] ?>"></i>
                                    </div>
                                    <span class="badge badge-success">Active</span>
                                </div>
                                <h5 class="card-title department-name"><?= htmlspecialchars($dept->getDepartmentName()) ?></h5>
                                <p class="text-muted mb-3"><?= htmlspecialchars($dept->getDescription() ?? 'No description available') ?></p>
                                <div class="dept-stats mb-3">
                                    <div class="d-flex justify-content-between mb-2">
                                        <small class="text-muted">Doctors:</small>
                                        <strong><?= $doctorCount ?></strong>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <small class="text-muted">Location:</small>
                                        <strong><?= htmlspecialchars($dept->getLocation() ?? 'N/A') ?></strong>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <small class="text-muted">Contact:</small>
                                        <strong><?= htmlspecialchars($dept->getContactNumber() ?? 'N/A') ?></strong>
                                    </div>
                                </div>
                                <div class="btn-group w-100">
                                    <button class="btn btn-sm btn-outline-primary" onclick="viewDepartment(<?= $dept->getDepartmentId() ?>)">
                                        <i class="fas fa-eye me-1"></i>View
                                    </button>
                                    <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#departmentModal" onclick="editDepartment(<?= $dept->getDepartmentId() ?>)">
                                        <i class="fas fa-edit me-1"></i>Edit
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteDepartment(<?= $dept->getDepartmentId() ?>)">
                                        <i class="fas fa-trash me-1"></i>Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Add/Edit Department Modal -->
<div class="modal fade" id="departmentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #F1F0CC;">
                <h5 class="modal-title" id="modalTitle">Add New Department</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="departmentForm" method="POST" action="process_department.php">
                    <input type="hidden" name="action" id="formAction" value="add">
                    <input type="hidden" name="department_id" id="departmentId">
                    
                    <div class="mb-3">
                        <label class="form-label">Department Name *</label>
                        <input type="text" class="form-control" name="department_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contact Number</label>
                        <input type="tel" class="form-control" name="contact_number">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Location</label>
                        <input type="text" class="form-control" name="location" placeholder="Building/Floor">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="departmentForm" class="btn btn-primary" style="background-color: #D5BF86; border-color: #D5BF86; color: #3F0D12;">Save Department</button>
            </div>
        </div>
    </div>
</div>

<script>
function openAddModal() {
    document.getElementById('modalTitle').textContent = 'Add New Department';
    document.getElementById('departmentForm').reset();
    document.getElementById('formAction').value = 'add';
}

function editDepartment(id) {
    document.getElementById('modalTitle').textContent = 'Edit Department';
    document.getElementById('formAction').value = 'edit';
    document.getElementById('departmentId').value = id;

    fetch(`get_department.php?id=${id}`)
        .then(res => res.json())
        .then(data => {
            document.querySelector('[name="department_name"]').value = data.department_name;
            document.querySelector('[name="description"]').value = data.description || '';
            document.querySelector('[name="contact_number"]').value = data.contact_number || '';
            document.querySelector('[name="email"]').value = data.email || '';
            document.querySelector('[name="location"]').value = data.location || '';
        })
        .catch(err => {
            alert('Error loading department data');
            console.error(err);
        });
}

function viewDepartment(id) {
    window.location.href = `view_department.php?id=${id}`;
}

function deleteDepartment(id) {
    if(confirm('Are you sure you want to delete this department? This will affect all associated doctors.')) {
        fetch('delete_department.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ department_id: id })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert('Department deleted successfully');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(err => {
            alert('Error deleting department');
            console.error(err);
        });
    }
}

function searchDepartments() {
    const input = document.getElementById('searchInput');
    const filter = input.value.toUpperCase();
    const cards = document.querySelectorAll('.department-card');

    cards.forEach(card => {
        const name = card.querySelector('.department-name').textContent;
        if (name.toUpperCase().indexOf(filter) > -1) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
}

function resetSearch() {
    document.getElementById('searchInput').value = '';
    const cards = document.querySelectorAll('.department-card');
    cards.forEach(card => {
        card.style.display = '';
    });
}
</script>

<style>
.dept-icon {
    width: 50px;
    height: 50px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
}

.dept-stats {
    border-top: 1px solid #e9ecef;
    border-bottom: 1px solid #e9ecef;
    padding: 10px 0;
}
</style>

<?php require_once '../../includes/footer.php'; ?>