<?php
session_start();

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
        
        switch ($this->getRole()) {
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
                $this->logout();
        }
        exit();
    }
    
    // navigate to specific file in user's role folder
    public function navigateTo($file) {
        if (!$this->isAuthenticated()) {
            $this->redirectToLogin();
        }
        
        $role = $this->getRole();
        $targetPath = $this->basePath . '/public/' . $role . '/' . ltrim($file, '/');
        
        header("Location: {$targetPath}");
        exit();
    }
    
    // New method: Navigate to specific file in any role folder (with permission check)
    public function navigateToRole($role, $file) {
        if (!$this->isAuthenticated()) {
            $this->redirectToLogin();
        }
        
        // Optional: Add role permission check here
        // For example, only admins can navigate to other roles' pages
        $currentRole = $this->getRole();
        if ($currentRole !== 'admin' && $currentRole !== $role) {
            // Redirect to their own dashboard if they don't have permission
            $this->redirectToDashboard();
        }
        
        $targetPath = $this->basePath . '/public/' . $role . '/' . ltrim($file, '/');
        
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
        
        if (!in_array($this->getRole(), $allowedRoles)) {
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
?>