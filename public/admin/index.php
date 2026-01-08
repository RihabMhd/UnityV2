<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Repositories\PatientRepository;
use Config\Database;
use Repositories\DoctorRepository;
use Repositories\DepartmentRepository;
use Repositories\AppointmentRepository;

$page_title = "Admin Dashboard";
require_once '../../includes/auth_check.php';
$router->requireRole('admin');
require_once '../../includes/header.php';
require_once '../../includes/navbar.php';


$db = (new Database())->connect();
$patientsRepo = new PatientRepository($db);
$patients = $patientsRepo->findAll();
$totalPatients = count($patients);


$doctorsRepo = new DoctorRepository($db);
$doctors = $doctorsRepo->findAll();
$totalDoctors = count($doctors);

$departmentsRepo = new DepartmentRepository($db);
$departments = $departmentsRepo->findAll();
$totalDepartments = count($departments);

$appointmentsRepo = new AppointmentRepository($db);
$appointments = $appointmentsRepo->findAll();
$totalAppoitments = count($appointments);

$Threeappointments = $appointmentsRepo->findRecent(3);
?>

<div class="main-content">
    <div class="content-header">
        <button class="mobile-toggle" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <div class="container-fluid">
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card" style="background: linear-gradient(135deg, #3F0D12 0%, #A71D31 100%);">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-details">
                        <h3><?php echo $totalPatients; ?></h3>
                        <p>Total Patients</p>
                        <small><i class="fas fa-arrow-up"></i> 12% from last month</small>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card" style="background: linear-gradient(135deg, #8D775F 0%, #D5BF86 100%);">
                    <div class="stat-icon">
                        <i class="fas fa-user-md"></i>
                    </div>
                    <div class="stat-details">
                        <h3><?php echo $totalDoctors; ?></h3>
                        <p>Total Doctors</p>
                        <small><i class="fas fa-arrow-up"></i> 3 new this month</small>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card" style="background: linear-gradient(135deg, #A71D31 0%, #3F0D12 100%);">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-details">
                        <h3><?php echo $totalAppoitments; ?></h3>
                        <p>Appointments Today</p>
                        <small>15 pending approval</small>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card" style="background: linear-gradient(135deg, #D5BF86 0%, #F1F0CC 100%); color: #3F0D12;">
                    <div class="stat-icon" style="color: #3F0D12;">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="stat-details">
                        <h3><?php echo $totalDepartments; ?></h3>
                        <p>Departments</p>
                        <small>All active</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <a href="patients.php" class="btn btn-outline-primary btn-block" style="border-color: #A71D31; color: #A71D31;">
                            <i class="fas fa-user-plus me-2"></i> Add New Patient
                        </a>
                    </div>
                    <div class="col-md-4 mb-3">
                        <a href="appointments.php" class="btn btn-outline-primary btn-block" style="border-color: #8D775F; color: #8D775F;">
                            <i class="fas fa-calendar-plus me-2"></i> Schedule Appointment
                        </a>
                    </div>
                    <div class="col-md-4 mb-3">
                        <a href="doctors.php" class="btn btn-outline-primary btn-block" style="border-color: #3F0D12; color: #3F0D12;">
                            <i class="fas fa-user-md me-2"></i> Manage Doctors
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Recent Appointments -->
            <div class="col-lg-8 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Recent Appointments</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Patient</th>
                                        <th>Doctor</th>
                                        <th>Reason</th>
                                        <th>Date & Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($Threeappointments as $app) : ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm me-2"><i class="fas fa-user"></i></div>
                                                    <span><?= $app->getPatientName() ?? $app->getPatientName() ?></span>
                                                </div>
                                            </td>
                                            <td>
                                                <?= $app->getDoctorName() ?? $app->getDoctorName() ?>
                                            </td>
                                            <td><?= $app->getReason() ?></td>
                                            <td>
                                                <?= date('d M Y', strtotime($app->getAppointmentDate())) ?>,
                                                <?= date('h:i A', strtotime($app->getAppointmentTime())) ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>


                        </div>
                    </div>
                </div>
            </div>

            <!-- System Overview -->
            <div class="col-lg-4 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>System Overview</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-2">
                                <small>Bed Occupancy</small>
                                <small class="fw-bold">78%</small>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar" style="width: 78%; background-color: #A71D31;"></div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-2">
                                <small>Staff Availability</small>
                                <small class="fw-bold">92%</small>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar" style="width: 92%; background-color: #8D775F;"></div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-2">
                                <small>Medication Stock</small>
                                <small class="fw-bold">65%</small>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar" style="width: 65%; background-color: #D5BF86;"></div>
                            </div>
                        </div>

                        <hr>

                        <h6 class="mb-3">Quick Stats</h6>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Emergency Cases</span>
                            <span class="fw-bold" style="color: #A71D31;">5</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Surgery Scheduled</span>
                            <span class="fw-bold" style="color: #3F0D12;">12</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Lab Tests Pending</span>
                            <span class="fw-bold" style="color: #8D775F;">28</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
</style>

<?php require_once '../../includes/footer.php'; ?>