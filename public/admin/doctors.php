<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Repositories\DoctorRepository;
use Repositories\DepartmentRepository;
use Config\Database;

$page_title = "Doctors Management";
require_once '../../includes/auth_check.php';
$router->requireRole('admin');
require_once '../../includes/header.php';
require_once '../../includes/navbar.php';

$db = (new Database)->connect();
$doctorsRepo = new DoctorRepository($db);
$doctors = $doctorsRepo->findAll();

$departmentsRepo = new DepartmentRepository($db);
$departments = $departmentsRepo->findAll();
?>

<div class="main-content">
    <div class="content-header">
        <button class="mobile-toggle" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>
        <div class="flex-grow-1">
            <h4 class="mb-0">Doctors Management</h4>
            <p class="text-muted mb-0">Total Doctors: <?= count($doctors) ?></p>
        </div>
        <button class="btn btn-primary" style="background-color: #A71D31; border-color: #A71D31;" data-bs-toggle="modal" data-bs-target="#doctorModal" onclick="openAddModal()">
            <i class="fas fa-plus-circle me-2"></i>Add New Doctor
        </button>
    </div>

    <div class="container-fluid">
        <!-- Success/Error Messages -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i><?= $_SESSION['success'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i><?= $_SESSION['error'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <!-- Search and Filter -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-5">
                        <input type="text" class="form-control" placeholder="Search doctors..." id="searchInput" onkeyup="searchTable()">
                    </div>
                    <div class="col-md-5">
                        <select class="form-select" id="specializationFilter" onchange="filterTable()">
                            <option value="">All Specializations</option>
                            <?php
                            $specializations = array_unique(array_map(function($d) {
                                return $d->getSpecialization();
                            }, $doctors));
                            foreach ($specializations as $spec): ?>
                                <option value="<?= htmlspecialchars($spec) ?>">
                                    <?= htmlspecialchars($spec) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-outline-secondary w-100" onclick="resetFilters()">
                            <i class="fas fa-redo me-2"></i>Reset
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Doctors Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="doctorsTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Doctor</th>
                                <th>Specialization</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($doctors)): ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="fas fa-user-md fa-3x mb-3 d-block"></i>
                                        No doctors found
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($doctors as $doctor): ?>
                                    <tr>
                                        <td><strong>#D<?= str_pad($doctor->getDoctorId(), 3, '0', STR_PAD_LEFT) ?></strong></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-md me-3" style="background-color: #3F0D12;">
                                                    <i class="fas fa-user-md"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold"><?= htmlspecialchars($doctor->getFullName()) ?></div>
                                                    <small class="text-muted"><?= htmlspecialchars($doctor->getSpecialization()) ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?= htmlspecialchars($doctor->getSpecialization()) ?></td>
                                        <td><?= htmlspecialchars($doctor->getPhoneNumber()) ?></td>
                                        <td><?= htmlspecialchars($doctor->getEmail()) ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary" onclick="viewDoctor(<?= $doctor->getDoctorId() ?>)" title="View">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#doctorModal" onclick="editDoctor(<?= $doctor->getDoctorId() ?>)" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger" onclick="deleteDoctor(<?= $doctor->getDoctorId() ?>)" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <nav aria-label="Page navigation" class="mt-3">
                    <ul class="pagination justify-content-end">
                        <li class="page-item disabled">
                            <a class="page-link" href="#">Previous</a>
                        </li>
                        <li class="page-item active">
                            <a class="page-link" href="#" style="background-color: #8D775F; border-color: #8D775F;">1</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="#" style="color: #8D775F;">Next</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Doctor Modal -->
<div class="modal fade" id="doctorModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #F1F0CC;">
                <h5 class="modal-title" id="modalTitle">Add New Doctor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="doctorForm" method="POST" action="process_doctor.php">
                    <input type="hidden" name="action" id="formAction" value="add">
                    <input type="hidden" name="doctor_id" id="doctorId">
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">First Name *</label>
                            <input type="text" class="form-control" name="first_name" id="firstName" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Last Name *</label>
                            <input type="text" class="form-control" name="last_name" id="lastName" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Specialization *</label>
                            <input type="text" class="form-control" name="specialization" id="specialization" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Department *</label>
                            <select class="form-select" name="department_id" id="departmentId" required>
                                <option selected disabled value="">Select Department</option>
                                <?php foreach ($departments as $dept): ?>
                                    <option value="<?= $dept->getDepartmentId() ?>">
                                        <?= htmlspecialchars($dept->getDepartmentName()) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone Number *</label>
                            <input type="tel" class="form-control" name="phone_number" id="phoneNumber" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email *</label>
                            <input type="email" class="form-control" name="email" id="email" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Username *</label>
                            <input type="text" class="form-control" name="username" id="username" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Password <span id="passwordRequired">*</span></label>
                            <input type="password" class="form-control" name="password" id="passwordInput">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="doctorForm" class="btn btn-primary" style="background-color: #8D775F; border-color: #8D775F;">Save Doctor</button>
            </div>
        </div>
    </div>
</div>

<script>
    function openAddModal() {
        document.getElementById('modalTitle').textContent = 'Add New Doctor';
        document.getElementById('doctorForm').reset();
        document.getElementById('formAction').value = 'add';
        document.getElementById('doctorId').value = '';
        document.getElementById('passwordInput').required = true;
        document.getElementById('passwordRequired').style.display = 'inline';
    }

    // Handle form submission
    document.getElementById('doctorForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitBtn = document.querySelector('#doctorModal .btn-primary');
        const originalText = submitBtn.innerHTML;
        
        // Validate required fields
        const requiredFields = ['first_name', 'last_name', 'specialization', 'department_id', 'phone_number', 'email', 'username'];
        const action = document.getElementById('formAction').value;
        
        if (action === 'add') {
            requiredFields.push('password');
        }
        
        let isValid = true;
        let missingFields = [];
        
        for (let field of requiredFields) {
            const value = formData.get(field);
            if (!value || value.trim() === '') {
                isValid = false;
                missingFields.push(field.replace('_', ' '));
            }
        }
        
        if (!isValid) {
            alert('Please fill in all required fields: ' + missingFields.join(', '));
            return;
        }
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Saving...';
        
        // Debug: Log form data
        console.log('Submitting form with action:', action);
        for (let pair of formData.entries()) {
            console.log(pair[0] + ': ' + pair[1]);
        }
        
        fetch('process_doctor.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            console.log('Response status:', response.status);
            return response.text();
        })
        .then(text => {
            console.log('Response text:', text.substring(0, 200));
            
            // Check if it's a redirect or actual content
            if (text.includes('<!DOCTYPE') || text.includes('<html')) {
                location.reload();
            } else {
                alert('Doctor saved successfully!');
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error saving doctor: ' + error.message);
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
    });

    function editDoctor(id) {
        document.getElementById('modalTitle').textContent = 'Edit Doctor';
        document.getElementById('formAction').value = 'edit';
        document.getElementById('doctorId').value = id;
        document.getElementById('passwordInput').required = false;
        document.getElementById('passwordInput').value = '';
        document.getElementById('passwordRequired').style.display = 'none';

        fetch(`get_doctor.php?id=${id}`)
            .then(res => res.json())
            .then(data => {
                if (data.error) {
                    alert('Error: ' + data.error);
                    return;
                }
                
                document.getElementById('firstName').value = data.first_name;
                document.getElementById('lastName').value = data.last_name;
                document.getElementById('specialization').value = data.specialization;
                document.getElementById('departmentId').value = data.department_id;
                document.getElementById('phoneNumber').value = data.phone_number;
                document.getElementById('email').value = data.email;
                document.getElementById('username').value = data.username;
            })
            .catch(err => {
                alert('Error loading doctor data');
                console.error(err);
            });
    }

    function viewDoctor(id) {
        window.location.href = `view_doctor.php?id=${id}`;
    }

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
                    location.reload();
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

    function searchTable() {
        const input = document.getElementById('searchInput');
        const filter = input.value.toUpperCase();
        const table = document.getElementById('doctorsTable');
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
        const deptFilter = document.getElementById('departmentFilter').value;
        const specFilter = document.getElementById('specializationFilter').value;
        const table = document.getElementById('doctorsTable');
        const tr = table.getElementsByTagName('tr');

        for (let i = 1; i < tr.length; i++) {
            const deptCell = tr[i].getElementsByTagName('td')[3];
            const specCell = tr[i].getElementsByTagName('td')[2];
            
            if (deptCell && specCell) {
                const deptValue = deptCell.textContent || deptCell.innerText;
                const specValue = specCell.textContent || specCell.innerText;
                
                const deptMatch = !deptFilter || deptValue.includes(deptFilter);
                const specMatch = !specFilter || specValue === specFilter;
                
                tr[i].style.display = (deptMatch && specMatch) ? '' : 'none';
            }
        }
    }

    function resetFilters() {
        document.getElementById('searchInput').value = '';
        document.getElementById('departmentFilter').value = '';
        document.getElementById('specializationFilter').value = '';
        
        const table = document.getElementById('doctorsTable');
        const tr = table.getElementsByTagName('tr');
        for (let i = 1; i < tr.length; i++) {
            tr[i].style.display = '';
        }
    }
</script>

<?php require_once '../../includes/footer.php'; ?>