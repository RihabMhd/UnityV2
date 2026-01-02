<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <i class="fas fa-hospital"></i>
        <h4>Hospital Management</h4>
    </div>
    
    <ul class="sidebar-menu">
        <?php if ($current_role === 'admin'): ?>
            <li>
                <a href="index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="doctors.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'doctors.php' ? 'active' : ''; ?>">
                    <i class="fas fa-user-md"></i>
                    <span>Doctors</span>
                </a>
            </li>
            <li>
                <a href="patients.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'patients.php' ? 'active' : ''; ?>">
                    <i class="fas fa-procedures"></i>
                    <span>Patients</span>
                </a>
            </li>
            <li>
                <a href="appointments.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'appointments.php' ? 'active' : ''; ?>">
                    <i class="fas fa-calendar-check"></i>
                    <span>Appointments</span>
                </a>
            </li>
            <li>
                <a href="departments.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'departments.php' ? 'active' : ''; ?>">
                    <i class="fas fa-building"></i>
                    <span>Departments</span>
                </a>
            </li>
            <li>
                <a href="medications.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'medications.php' ? 'active' : ''; ?>">
                    <i class="fas fa-pills"></i>
                    <span>Medications</span>
                </a>
            </li>
            <li>
                <a href="reports.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'active' : ''; ?>">
                    <i class="fas fa-chart-bar"></i>
                    <span>Reports</span>
                </a>
            </li>
        
        <?php elseif ($current_role === 'doctor'): ?>
            <li>
                <a href="index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="my_patients.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'my_patients.php' ? 'active' : ''; ?>">
                    <i class="fas fa-procedures"></i>
                    <span>My Patients</span>
                </a>
            </li>
            <li>
                <a href="my_appointments.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'my_appointments.php' ? 'active' : ''; ?>">
                    <i class="fas fa-calendar-check"></i>
                    <span>My Appointments</span>
                </a>
            </li>
            <li>
                <a href="prescriptions.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'prescriptions.php' ? 'active' : ''; ?>">
                    <i class="fas fa-prescription"></i>
                    <span>Prescriptions</span>
                </a>
            </li>
            <li>
                <a href="schedule.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'schedule.php' ? 'active' : ''; ?>">
                    <i class="fas fa-clock"></i>
                    <span>My Schedule</span>
                </a>
            </li>
        
        <?php elseif ($current_role === 'patient'): ?>
            <li>
                <a href="index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="my_appointments.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'my_appointments.php' ? 'active' : ''; ?>">
                    <i class="fas fa-calendar-check"></i>
                    <span>My Appointments</span>
                </a>
            </li>
            <li>
                <a href="my_prescriptions.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'my_prescriptions.php' ? 'active' : ''; ?>">
                    <i class="fas fa-prescription"></i>
                    <span>My Prescriptions</span>
                </a>
            </li>
            <li>
                <a href="book_appointment.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'book_appointment.php' ? 'active' : ''; ?>">
                    <i class="fas fa-plus-circle"></i>
                    <span>Book Appointment</span>
                </a>
            </li>
            <li>
                <a href="medical_history.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'medical_history.php' ? 'active' : ''; ?>">
                    <i class="fas fa-history"></i>
                    <span>Medical History</span>
                </a>
            </li>
        <?php endif; ?>
    </ul>

    <div class="sidebar-user">
        <div class="sidebar-user-info">
            <div class="sidebar-user-avatar">
                <i class="fas fa-user"></i>
            </div>
            <div class="sidebar-user-details">
                <h6><?php echo htmlspecialchars($current_username); ?></h6>
                <small><?php echo ucfirst($current_role); ?></small>
            </div>
        </div>
        <div class="sidebar-user-actions">
            <a href="profile.php" title="Profile">
                <i class="fas fa-user-circle"></i>
            </a>
            <a href="settings.php" title="Settings">
                <i class="fas fa-cog"></i>
            </a>
            <a href="../auth/logout.php" title="Logout">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </div>
    </div>
</aside>

<script>
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('active');
}

document.addEventListener('click', function(event) {
    const sidebar = document.getElementById('sidebar');
    const toggle = document.querySelector('.mobile-toggle');
    
    if (window.innerWidth <= 768) {
        if (!sidebar.contains(event.target) && !toggle.contains(event.target)) {
            sidebar.classList.remove('active');
        }
    }
});
</script>