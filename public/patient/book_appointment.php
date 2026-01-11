<?php
use Repositories\DoctorRepository;
use Repositories\DepartmentRepository;
use Repositories\PatientRepository;
use Repositories\AppointmentRepository;
use Models\Appointment;
use Config\Database;

$page_title = "Book Appointment";
require_once '../../includes/auth_check.php';
$router->requireRole('patient');
require_once '../../includes/header.php';
require_once '../../includes/navbar.php';
require_once __DIR__ . '/../../vendor/autoload.php';

$db = (new Database())->connect();
$doctorsRepo = new DoctorRepository($db);
$departmentsRepo = new DepartmentRepository($db);
$patientsRepo = new PatientRepository($db);
$appointmentsRepo = new AppointmentRepository($db);

$authenticatedUserId = $_SESSION['user_id'];
$currentPatient = $patientsRepo->findById($authenticatedUserId);

if (!$currentPatient) {
    $_SESSION['error'] = 'Patient profile not found.';
    header('Location: index.php');
    exit;
}

$doctors = $doctorsRepo->findAll();
$departments = $departmentsRepo->findAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $doctorId = $_POST['doctor_id'] ?? null;
        $appointmentDate = $_POST['appointment_date'] ?? null;
        $appointmentTime = $_POST['appointment_time'] ?? null;
        $reason = $_POST['reason'] ?? '';
        
        if (!$doctorId || !$appointmentDate || !$appointmentTime || !$reason) {
            throw new Exception('All fields are required.');
        }
        
        $appointment = new Appointment();
        $appointment->setPatientId($currentPatient->getPatientId());
        $appointment->setDoctorId($doctorId);
        $appointment->setAppointmentDate($appointmentDate);
        $appointment->setAppointmentTime($appointmentTime);
        $appointment->setReason($reason);
        $appointment->setStatus('Pending');
        
        $appointmentsRepo->create($appointment);
        
        $_SESSION['success'] = 'Appointment booked successfully! Waiting for confirmation.';
        header('Location: appointments.php');
        exit;
        
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }
}
?>

<div class="main-content">
    <div class="content-header">
        <button class="mobile-toggle" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <div class="container-fluid">
        <div class="mb-4">
            <a href="appointments.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Appointments
            </a>
        </div>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?= htmlspecialchars($_SESSION['error']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-calendar-plus me-2"></i>Appointment Details</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" id="appointmentForm">
                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-building me-2"></i>Select Department
                                </label>
                                <select class="form-select" id="department_id" onchange="filterDoctors()">
                                    <option value="">All Departments</option>
                                    <?php foreach ($departments as $dept): ?>
                                        <option value="<?= $dept->getDepartmentId() ?>">
                                            <?= htmlspecialchars($dept->getName()) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label for="doctor_id" class="form-label fw-bold">
                                    <i class="fas fa-user-md me-2"></i>Select Doctor *
                                </label>
                                <select class="form-select" id="doctor_id" name="doctor_id" required>
                                    <option value="">Choose a doctor...</option>
                                    <?php foreach ($doctors as $doctor): ?>
                                        <option value="<?= $doctor->getUserId() ?>" 
                                                data-department="<?= $doctor->getDepartmentId() ?>"
                                                data-specialization="<?= htmlspecialchars($doctor->getSpecialization()) ?>">
                                            Dr. <?= htmlspecialchars($doctor->getFullName()) ?> 
                                            - <?= htmlspecialchars($doctor->getSpecialization()) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="text-muted">Select your preferred doctor</small>
                            </div>

                            <div class="mb-4">
                                <label for="appointment_date" class="form-label fw-bold">
                                    <i class="fas fa-calendar me-2"></i>Appointment Date *
                                </label>
                                <input type="date" 
                                       class="form-control" 
                                       id="appointment_date" 
                                       name="appointment_date" 
                                       min="<?= date('Y-m-d') ?>" 
                                       required>
                                <small class="text-muted">Choose your preferred date</small>
                            </div>

                            <div class="mb-4">
                                <label for="appointment_time" class="form-label fw-bold">
                                    <i class="fas fa-clock me-2"></i>Appointment Time *
                                </label>
                                <select class="form-select" id="appointment_time" name="appointment_time" required>
                                    <option value="">Select time...</option>
                                    <option value="09:00:00">09:00 AM</option>
                                    <option value="09:30:00">09:30 AM</option>
                                    <option value="10:00:00">10:00 AM</option>
                                    <option value="10:30:00">10:30 AM</option>
                                    <option value="11:00:00">11:00 AM</option>
                                    <option value="11:30:00">11:30 AM</option>
                                    <option value="14:00:00">02:00 PM</option>
                                    <option value="14:30:00">02:30 PM</option>
                                    <option value="15:00:00">03:00 PM</option>
                                    <option value="15:30:00">03:30 PM</option>
                                    <option value="16:00:00">04:00 PM</option>
                                    <option value="16:30:00">04:30 PM</option>
                                </select>
                                <small class="text-muted">Choose your preferred time slot</small>
                            </div>

                            <div class="mb-4">
                                <label for="reason" class="form-label fw-bold">
                                    <i class="fas fa-notes-medical me-2"></i>Reason for Visit *
                                </label>
                                <textarea class="form-control" 
                                          id="reason" 
                                          name="reason" 
                                          rows="4" 
                                          placeholder="Please describe your symptoms or reason for consultation..."
                                          required></textarea>
                                <small class="text-muted">Provide details about your medical concern</small>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg" style="background-color: #A71D31; border-color: #A71D31;">
                                    <i class="fas fa-calendar-check me-2"></i>Book Appointment
                                </button>
                                <a href="appointments.php" class="btn btn-outline-secondary">
                                    Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card mb-3">
                    <div class="card-header" style="background-color: #A71D31; color: white;">
                        <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Booking Guidelines</h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                Book at least 24 hours in advance
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                Arrive 15 minutes before your appointment
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                Bring your ID and insurance card
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                You'll receive a confirmation email
                            </li>
                            <li class="mb-0">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                Cancel at least 2 hours before if needed
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header" style="background-color: #3F0D12; color: white;">
                        <h6 class="mb-0"><i class="fas fa-user me-2"></i>Your Information</h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-2">
                            <strong>Name:</strong><br>
                            <?= htmlspecialchars($currentPatient->getFullName()) ?>
                        </p>
                        <p class="mb-2">
                            <strong>Patient ID:</strong><br>
                            #P<?= str_pad($currentPatient->getPatientId(), 3, '0', STR_PAD_LEFT) ?>
                        </p>
                        <p class="mb-0">
                            <strong>Contact:</strong><br>
                            <?= htmlspecialchars($currentPatient->getPhoneNumber() ?? 'N/A') ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.form-label {
    color: #3F0D12;
}

.form-control:focus,
.form-select:focus {
    border-color: #A71D31;
    box-shadow: 0 0 0 0.2rem rgba(167, 29, 49, 0.25);
}
</style>

<script>
function filterDoctors() {
    const departmentId = document.getElementById('department_id').value;
    const doctorSelect = document.getElementById('doctor_id');
    const options = doctorSelect.querySelectorAll('option');
    
    options.forEach(option => {
        if (option.value === '') {
            option.style.display = 'block';
            return;
        }
        
        const optionDepartment = option.getAttribute('data-department');
        
        if (departmentId === '' || optionDepartment === departmentId) {
            option.style.display = 'block';
        } else {
            option.style.display = 'none';
        }
    });
    
    // Reset doctor selection if current selection is hidden
    if (doctorSelect.value && doctorSelect.selectedOptions[0].style.display === 'none') {
        doctorSelect.value = '';
    }
}

// Form validation
document.getElementById('appointmentForm').addEventListener('submit', function(e) {
    const date = document.getElementById('appointment_date').value;
    const today = new Date().toISOString().split('T')[0];
    
    if (date < today) {
        e.preventDefault();
        alert('Please select a future date for your appointment.');
        return false;
    }
});
</script>

<?php require_once '../../includes/footer.php'; ?>