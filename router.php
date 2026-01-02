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
    
    public function requireRole($allowedRoles) {
        if (!$this->isAuthenticated()) {
            $this->redirectToLogin();
        }
        
        if (!is_array($allowedRoles)) {
            $allowedRoles = [$allowedRoles];
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
    
    public function url($role, $page = 'index.php') {
        return $this->basePath . '/public/' . $role . '/' . $page;
    }
}

$router = new Router();
?>
