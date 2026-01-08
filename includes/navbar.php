<?php

$current_page = basename($_SERVER['PHP_SELF']);
$current_role = $router->getRole();
$current_username = $_SESSION['username'] ?? 'User';
?>

<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <i class="fas fa-hospital"></i>
        <h4>Hospital Management</h4>
    </div>
    
    <ul class="sidebar-menu">
        <?php if ($current_role === 'admin'): ?>
            <li>
                <a href="<?php echo $router->url('admin', 'index.php'); ?>" class="<?php echo $current_page === 'index.php' ? 'active' : ''; ?>">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="<?php echo $router->url('admin', 'doctors.php'); ?>" class="<?php echo $current_page === 'doctors.php' ? 'active' : ''; ?>">
                    <i class="fas fa-user-md"></i>
                    <span>Doctors</span>
                </a>
            </li>
            <li>
                <a href="<?php echo $router->url('admin', 'patients.php'); ?>" class="<?php echo $current_page === 'patients.php' ? 'active' : ''; ?>">
                    <i class="fas fa-procedures"></i>
                    <span>Patients</span>
                </a>
            </li>
            <li>
                <a href="<?php echo $router->url('admin', 'appointments.php'); ?>" class="<?php echo $current_page === 'appointments.php' ? 'active' : ''; ?>">
                    <i class="fas fa-calendar-check"></i>
                    <span>Appointments</span>
                </a>
            </li>
            <li>
                <a href="<?php echo $router->url('admin', 'departments.php'); ?>" class="<?php echo $current_page === 'departments.php' ? 'active' : ''; ?>">
                    <i class="fas fa-building"></i>
                    <span>Departments</span>
                </a>
            </li>
            <li>
                <a href="<?php echo $router->url('admin', 'medications.php'); ?>" class="<?php echo $current_page === 'medications.php' ? 'active' : ''; ?>">
                    <i class="fas fa-pills"></i>
                    <span>Medications</span>
                </a>
            </li>
        
        <?php elseif ($current_role === 'doctor'): ?>
            <li>
                <a href="<?php echo $router->url('doctor', 'index.php'); ?>" class="<?php echo $current_page === 'index.php' ? 'active' : ''; ?>">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="<?php echo $router->url('doctor', 'appointments.php'); ?>" class="<?php echo $current_page === 'appointments.php' ? 'active' : ''; ?>">
                    <i class="fas fa-calendar-check"></i>
                    <span>My Appointments</span>
                </a>
            </li>
            <li>
                <a href="<?php echo $router->url('doctor', 'prescriptions.php'); ?>" class="<?php echo $current_page === 'prescriptions.php' ? 'active' : ''; ?>">
                    <i class="fas fa-prescription"></i>
                    <span>Prescriptions</span>
                </a>
            </li>
        
        <?php elseif ($current_role === 'patient'): ?>
            <li>
                <a href="<?php echo $router->url('patient', 'index.php'); ?>" class="<?php echo $current_page === 'index.php' ? 'active' : ''; ?>">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="<?php echo $router->url('patient', 'appointments.php'); ?>" class="<?php echo $current_page === 'appointments.php' ? 'active' : ''; ?>">
                    <i class="fas fa-calendar-check"></i>
                    <span>My Appointments</span>
                </a>
            </li>
            <li>
                <a href="<?php echo $router->url('patient', 'prescriptions.php'); ?>" class="<?php echo $current_page === 'prescriptions.php' ? 'active' : ''; ?>">
                    <i class="fas fa-prescription"></i>
                    <span>My Prescriptions</span>
                </a>
            </li>
            <li>
                <a href="<?php echo $router->url('patient', 'book_appointment.php'); ?>" class="<?php echo $current_page === 'book_appointment.php' ? 'active' : ''; ?>">
                    <i class="fas fa-plus-circle"></i>
                    <span>Book Appointment</span>
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
            <a href="<?php echo $router->url($current_role, 'profile.php'); ?>" title="Profile">
                <i class="fas fa-user-circle"></i>
            </a>
            <a href="<?php echo $router->url($current_role, 'settings.php'); ?>" title="Settings">
                <i class="fas fa-cog"></i>
            </a>
            <a href="<?php echo $router->url('auth', 'logout.php'); ?>" title="Logout">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </div>
    </div>
</aside>

<script>
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    if (sidebar) {
        sidebar.classList.toggle('active');
    }
}

document.addEventListener('click', function(event) {
    const sidebar = document.getElementById('sidebar');
    const toggle = document.querySelector('.mobile-toggle');
    
    if (!sidebar || !toggle) return;
    
    if (window.innerWidth <= 768) {
        if (!sidebar.contains(event.target) && !toggle.contains(event.target)) {
            sidebar.classList.remove('active');
        }
    }
});

window.addEventListener('resize', function() {
    const sidebar = document.getElementById('sidebar');
    if (sidebar && window.innerWidth > 768) {
        sidebar.classList.remove('active');
    }
});
</script>