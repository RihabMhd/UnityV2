<?php
// Only start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class Router {
    private $basePath = '/UnityV2';
    
    public function isAuthenticated() {
        return isset($_SESSION['user_id']) && isset($_SESSION['role']);
    }
    
    public function getRole() {
        return $_SESSION['role'] ?? null;
    }
    
    public function redirectToLogin() {
        header("Location: {$this->basePath}/public/auth/login.php");
        exit();
    }
    
    public function redirectToDashboard() {
        if (!$this->isAuthenticated()) {
            $this->redirectToLogin();
        }
        
        // Convert role to lowercase for case-insensitive comparison
        $role = strtolower($this->getRole());
        
        switch ($role) {
            case 'admin':
                header("Location: {$this->basePath}/public/admin/index.php");
                break;
            case 'doctor':
                header("Location: {$this->basePath}/public/doctor/index.php");
                break;
            case 'patient':
                header("Location: {$this->basePath}/public/patient/index.php");
                break;
            default:
                // Log the invalid role for debugging
                error_log("Invalid role attempted: " . ($this->getRole() ?? 'NULL'));
                $this->logout();
        }
        exit();
    }
    
    public function navigateTo($file) {
        if (!$this->isAuthenticated()) {
            $this->redirectToLogin();
        }
        
        $role = strtolower($this->getRole());
        $targetPath = $this->basePath . '/public/' . $role . '/' . ltrim($file, '/');
        
        header("Location: {$targetPath}");
        exit();
    }
    
    public function navigateToRole($role, $file) {
        if (!$this->isAuthenticated()) {
            $this->redirectToLogin();
        }
        
        $currentRole = strtolower($this->getRole());
        $targetRole = strtolower($role);
        
        if ($currentRole !== 'admin' && $currentRole !== $targetRole) {
            $this->redirectToDashboard();
        }
        
        $targetPath = $this->basePath . '/public/' . $targetRole . '/' . ltrim($file, '/');
        
        header("Location: {$targetPath}");
        exit();
    }
    
    public function requireRole($allowedRoles) {
        if (!$this->isAuthenticated()) {
            $this->redirectToLogin();
        }
        
        if (!is_array($allowedRoles)) {
            $allowedRoles = [$allowedRoles];
        }
        
        // Case-insensitive role comparison
        $currentRole = strtolower($this->getRole());
        $allowedRoles = array_map('strtolower', $allowedRoles);
        
        if (!in_array($currentRole, $allowedRoles)) {
            $this->redirectToDashboard();
        }
    }
    
    public function logout() {
        session_destroy();
        $this->redirectToLogin();
    }
    
    public function getBaseUrl() {
        return $this->basePath;
    }
    
    public function asset($path) {
        return $this->basePath . '/public/assets/' . ltrim($path, '/');
    }
    
    // Enhanced URL method - now accepts specific files
    public function url($role, $page = 'index.php') {
        $role = strtolower($role);
        return $this->basePath . '/public/' . $role . '/' . ltrim($page, '/');
    }
    
    // Get current page URL
    public function getCurrentUrl() {
        return $_SERVER['REQUEST_URI'];
    }
    
    // Check if current page matches
    public function isCurrentPage($page) {
        return basename($_SERVER['PHP_SELF']) === $page;
    }
}

$router = new Router();