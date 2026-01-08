<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Repositories\DepartmentRepository;
use Repositories\DoctorRepository;
use Config\Database;

$page_title = "Department Details";
require_once '../../includes/auth_check.php';
require_once '../../includes/header.php';
require_once '../../includes/navbar.php';

// Get department ID from URL
if (!isset($_GET['id'])) {
    header('Location: departments.php');
    exit;
}

$departmentId = (int)$_GET['id'];

try {
    $db = (new Database)->connect();
    $departmentRepo = new DepartmentRepository($db);
    $doctorRepo = new DoctorRepository($db);
    
    // Get department details
    $department = $departmentRepo->findById($departmentId);
    
    if (!$department) {
        $_SESSION['error'] = 'Department not found';
        header('Location: departments.php');
        exit;
    }
    
    // Get all doctors in this department
    $allDoctors = $doctorRepo->findAll();
    $departmentDoctors = array_filter($allDoctors, function($doctor) use ($departmentId) {
        return $doctor->getDepartmentId() == $departmentId;
    });
    
} catch (Exception $e) {
    $_SESSION['error'] = 'Error loading department: ' . $e->getMessage();
    header('Location: departments.php');
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
                    <li class="breadcrumb-item"><a href="departments.php">Departments</a></li>
                    <li class="breadcrumb-item active"><?= htmlspecialchars($department->getDepartmentName()) ?></li>
                </ol>
            </nav>
        </div>
        <a href="departments.php" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Departments
        </a>
    </div>

    <div class="container-fluid">
        <!-- Department Info Card -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header" style="background-color: #F1F0CC;">
                        <h5 class="mb-0">
                            <i class="fas fa-building me-2" style="color: #A71D31;"></i>
                            <?= htmlspecialchars($department->getDepartmentName()) ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-muted mb-3">Department Information</h6>
                                <table class="table table-borderless">
                                    <tr>
                                        <td class="text-muted" style="width: 150px;"><i class="fas fa-id-badge me-2"></i>Department ID:</td>
                                        <td><strong><?= $department->getDepartmentId() ?></strong></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted"><i class="fas fa-map-marker-alt me-2"></i>Location:</td>
                                        <td><strong><?= htmlspecialchars($department->getLocation() ?? 'N/A') ?></strong></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted"><i class="fas fa-phone me-2"></i>Contact Number:</td>
                                        <td><strong><?= htmlspecialchars($department->getContactNumber() ?? 'N/A') ?></strong></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted"><i class="fas fa-envelope me-2"></i>Email:</td>
                                        <td><strong><?= htmlspecialchars($department->getEmail() ?? 'N/A') ?></strong></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted mb-3">Description</h6>
                                <p><?= htmlspecialchars($department->getDescription() ?? 'No description available') ?></p>
                                
                                <div class="mt-4">
                                    <h6 class="text-muted mb-3">Statistics</h6>
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="stat-icon" style="background-color: #A71D31;">
                                            <i class="fas fa-user-md"></i>
                                        </div>
                                        <div class="ms-3">
                                            <h4 class="mb-0"><?= count($departmentDoctors) ?></h4>
                                            <small class="text-muted">Total Doctors</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php if ($_SESSION['role'] === 'admin'): ?>
                        <div class="mt-4 pt-3 border-top">
                            <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editDepartmentModal">
                                <i class="fas fa-edit me-2"></i>Edit Department
                            </button>
                            <button class="btn btn-danger" onclick="confirmDelete()">
                                <i class="fas fa-trash me-2"></i>Delete Department
                            </button>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Doctors in Department -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header" style="background-color: #F1F0CC;">
                        <h5 class="mb-0">
                            <i class="fas fa-user-md me-2" style="color: #A71D31;"></i>
                            Doctors in this Department
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($departmentDoctors)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-user-md fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No doctors assigned to this department yet.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Doctor ID</th>
                                            <th>Name</th>
                                            <th>Specialization</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($departmentDoctors as $doctor): ?>
                                        <tr>
                                            <td><?= $doctor->getDoctorId() ?></td>
                                            <td>
                                                <strong>Dr. <?= htmlspecialchars($doctor->getFirstName() . ' ' . $doctor->getLastName()) ?></strong>
                                            </td>
                                            <td><?= htmlspecialchars($doctor->getSpecialization() ?? 'N/A') ?></td>
                                            <td><?= htmlspecialchars($doctor->getEmail() ?? 'N/A') ?></td>
                                            <td><?= htmlspecialchars($doctor->getPhoneNumber() ?? 'N/A') ?></td>
                                            <td>
                                                <a href="./view_doctor.php?id=<?= $doctor->getDoctorId() ?>" class="btn btn-sm btn-outline-primary">
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

<!-- Edit Department Modal -->
<div class="modal fade" id="editDepartmentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #F1F0CC;">
                <h5 class="modal-title">Edit Department</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editDepartmentForm" method="POST" action="process_department.php">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="department_id" value="<?= $department->getDepartmentId() ?>">
                    
                    <div class="mb-3">
                        <label class="form-label">Department Name *</label>
                        <input type="text" class="form-control" name="department_name" value="<?= htmlspecialchars($department->getDepartmentName()) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3"><?= htmlspecialchars($department->getDescription() ?? '') ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contact Number</label>
                        <input type="tel" class="form-control" name="contact_number" value="<?= htmlspecialchars($department->getContactNumber() ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($department->getEmail() ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Location</label>
                        <input type="text" class="form-control" name="location" value="<?= htmlspecialchars($department->getLocation()) ?>" placeholder="Building/Floor">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="editDepartmentForm" class="btn btn-primary" style="background-color: #D5BF86; border-color: #D5BF86; color: #3F0D12;">
                    Update Department
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete() {
    if(confirm('Are you sure you want to delete this department? This will affect all associated doctors and cannot be undone.')) {
        fetch('delete_department.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ department_id: <?= $department->getDepartmentId() ?> })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert('Department deleted successfully');
                window.location.href = 'departments.php';
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
</script>

<style>
.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
}

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