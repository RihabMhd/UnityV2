<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Repositories\PatientRepository;
use Config\Database;
use Repositories\DoctorRepository;
$page_title = "Patients Management";
require_once '../../includes/auth_check.php';
$router->requireRole('admin');
require_once '../../includes/header.php';
require_once '../../includes/navbar.php';

$db = (new Database())->connect();
$patientsRepo = new PatientRepository($db);
$patients = $patientsRepo->findAll();

$doctorRepo=new DoctorRepository($db);
$doctors=$doctorRepo->findAll();
?>

<div class="main-content">
    <div class="content-header">
        <button class="mobile-toggle" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>
        <div class="flex-grow-1">
            <h4 class="mb-0">Patients Management</h4>
            <p class="text-muted mb-0">Total Patients: <?= count($patients) ?></p>
        </div>
        <button class="btn btn-primary" style="background-color: #A71D31; border-color: #A71D31;" data-bs-toggle="modal" data-bs-target="#patientModal" onclick="openAddModal()">
            <i class="fas fa-plus-circle me-2"></i>Add New Patient
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
                        <input type="text" class="form-control" placeholder="Search patients..." id="searchInput" onkeyup="searchTable()">
                    </div>
                    <div class="col-md-5">
                        <select class="form-select" id="doctorFilter" onchange="filterTable()">
                            <option value="">All Doctors</option>
                            <?php 
                            foreach ($doctors as $doctor): ?>
                                <option value="<?= $doctor->getDoctorId() ?>">
                                    <?= htmlspecialchars($doctor->getFullName()) ?>
                                </option>
                            <?php endforeach; ?>?>
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
                                        No patients found
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
                                            <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#patientModal" onclick="editPatient(<?= $patient->getPatientId() ?>)" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger" onclick="deletePatient(<?= $patient->getPatientId() ?>)" title="Delete">
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
                            <a class="page-link" href="#" style="background-color: #A71D31; border-color: #A71D31;">1</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="#" style="color: #A71D31;">Next</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Patient Modal -->
<div class="modal fade" id="patientModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #F1F0CC;">
                <h5 class="modal-title" id="modalTitle">Add New Patient</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="patientForm" method="POST" action="process_patient.php">
                    <input type="hidden" name="action" id="formAction" value="add">
                    <input type="hidden" name="patient_id" id="patientId">
                    
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
                            <label class="form-label">Date of Birth *</label>
                            <input type="date" class="form-control" name="date_of_birth" id="dateOfBirth" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone Number *</label>
                            <input type="tel" class="form-control" name="phone_number" id="phoneNumber" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email *</label>
                            <input type="email" class="form-control" name="email" id="email" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Address *</label>
                            <textarea class="form-control" name="address" id="address" rows="2" required></textarea>
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
                <button type="submit" form="patientForm" class="btn btn-primary" style="background-color: #A71D31; border-color: #A71D31;">Save Patient</button>
            </div>
        </div>
    </div>
</div>

<script>
    function openAddModal() {
        document.getElementById('modalTitle').textContent = 'Add New Patient';
        document.getElementById('patientForm').reset();
        document.getElementById('formAction').value = 'add';
        document.getElementById('patientId').value = '';
        document.getElementById('passwordInput').required = true;
        document.getElementById('passwordRequired').style.display = 'inline';
    }

    // Handle form submission
    document.getElementById('patientForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitBtn = document.querySelector('#patientModal .btn-primary');
        const originalText = submitBtn.innerHTML;
        
        // Validate required fields
        const requiredFields = ['first_name', 'last_name', 'date_of_birth', 'phone_number', 'email', 'address', 'username'];
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
        
        fetch('process_patient.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            return response.text();
        })
        .then(text => {
            console.log('Response text:', text.substring(0, 200)); // Log first 200 chars
            
            // Check if it's a redirect or actual content
            if (text.includes('<!DOCTYPE') || text.includes('<html')) {
                // It's HTML, probably redirected - reload the page
                location.reload();
            } else {
                alert('Patient saved successfully!');
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error saving patient: ' + error.message);
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
    });

    function editPatient(id) {
        document.getElementById('modalTitle').textContent = 'Edit Patient';
        document.getElementById('formAction').value = 'edit';
        document.getElementById('patientId').value = id;
        document.getElementById('passwordInput').required = false;
        document.getElementById('passwordInput').value = '';
        document.getElementById('passwordRequired').style.display = 'none';

        fetch(`get_patient.php?id=${id}`)
            .then(res => res.json())
            .then(data => {
                if (data.error) {
                    alert('Error: ' + data.error);
                    return;
                }
                
                document.getElementById('firstName').value = data.first_name;
                document.getElementById('lastName').value = data.last_name;
                document.getElementById('dateOfBirth').value = data.date_of_birth;
                document.getElementById('phoneNumber').value = data.phone_number;
                document.getElementById('email').value = data.email;
                document.getElementById('address').value = data.address;
                document.getElementById('username').value = data.username;
            })
            .catch(err => {
                alert('Error loading patient data');
                console.error(err);
            });
    }

    function viewPatient(id) {
        window.location.href = `view_patient.php?id=${id}`;
    }

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
                    location.reload();
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

    function filterTable() {
        const doctorFilter = document.getElementById('doctorFilter').value;
    }

    function resetFilters() {
        document.getElementById('searchInput').value = '';
        document.getElementById('doctorFilter').value = '';
        
        const table = document.getElementById('patientsTable');
        const tr = table.getElementsByTagName('tr');
        for (let i = 1; i < tr.length; i++) {
            tr[i].style.display = '';
        }
    }
</script>

<?php require_once '../../includes/footer.php'; ?>