<?php
session_start();

// If already logged in, redirect manually
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    $role = strtolower($_SESSION['role']);
    $redirectPath = match($role) {
        'admin' => '/UnityV2/public/admin/index.php',
        'doctor' => '/UnityV2/public/doctor/index.php',
        'patient' => '/UnityV2/public/patient/index.php',
        default => '/UnityV2/public/auth/login.php'
    };
    header("Location: $redirectPath");
    exit();
}

require_once __DIR__ . '/../../vendor/autoload.php';

use Repositories\UserRepository;
use Config\Database;

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password.';
    } else {
        try {
            $database = new Database();
            $db = $database->connect();
            $userRepo = new UserRepository($db);
            
            $user = $userRepo->findByUsername($username);
            
            if ($user && password_verify($password, $user->getPassword())) {
                // Set session variables
                $_SESSION['user_id'] = $user->getId();
                $_SESSION['username'] = $user->getUsername();
                $_SESSION['email'] = $user->getEmail();
                $_SESSION['role'] = $user->getRole();
                
                // Force write session
                session_write_close();
                
                // Manual redirect based on role
                $role = strtolower($user->getRole());
                $redirectPath = match($role) {
                    'admin' => '/UnityV2/public/admin/index.php',
                    'doctor' => '/UnityV2/public/doctor/index.php',
                    'patient' => '/UnityV2/public/patient/index.php',
                    default => '/UnityV2/public/auth/login.php'
                };
                
                header("Location: $redirectPath");
                exit();
            } else {
                $error = 'Invalid username or password.';
            }
        } catch (Exception $e) {
            $error = 'An error occurred. Please try again.';
            error_log("Login error: " . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Hospital Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-maroon: #6d1f3e;
            --light-maroon: #8b2d50;
            --accent-gold: #d4af37;
            --bg-light: #f8f4f0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, var(--primary-maroon) 0%, var(--light-maroon) 50%, #5a1830 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(212, 175, 55, 0.1) 0%, transparent 70%);
            animation: pulse 15s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        .login-container {
            position: relative;
            z-index: 1;
            max-width: 380px;
            width: 100%;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.98);
            border-radius: 16px;
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.4);
            overflow: hidden;
            backdrop-filter: blur(10px);
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-header {
            background: linear-gradient(135deg, var(--primary-maroon) 0%, var(--light-maroon) 100%);
            color: white;
            padding: 30px 25px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .login-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(212, 175, 55, 0.2) 0%, transparent 70%);
        }

        .login-header i {
            font-size: 2.8rem;
            margin-bottom: 12px;
            color: var(--accent-gold);
            filter: drop-shadow(0 5px 10px rgba(0, 0, 0, 0.3));
            animation: iconFloat 3s ease-in-out infinite;
        }

        @keyframes iconFloat {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        .login-header h3 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 6px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }

        .login-header p {
            font-size: 0.88rem;
            opacity: 0.95;
            margin: 0;
        }

        .login-body {
            padding: 30px 28px;
        }

        .form-label {
            color: var(--primary-maroon);
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 0.9rem;
        }

        .form-label i {
            color: var(--accent-gold);
            margin-right: 5px;
        }

        .form-control {
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            padding: 10px 14px;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }

        .form-control:focus {
            border-color: var(--accent-gold);
            box-shadow: 0 0 0 0.2rem rgba(212, 175, 55, 0.25);
            background-color: #fffef8;
        }

        .form-control:hover {
            border-color: var(--light-maroon);
        }

        .form-check {
            margin: 16px 0;
        }

        .form-check-input:checked {
            background-color: var(--primary-maroon);
            border-color: var(--primary-maroon);
        }

        .form-check-input:focus {
            box-shadow: 0 0 0 0.2rem rgba(109, 31, 62, 0.25);
        }

        .form-check-label {
            color: #555;
            font-size: 0.9rem;
        }

        .btn-login {
            background: linear-gradient(135deg, var(--primary-maroon) 0%, var(--light-maroon) 100%);
            border: none;
            width: 100%;
            padding: 12px;
            font-weight: 600;
            border-radius: 8px;
            color: white;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(109, 31, 62, 0.3);
            position: relative;
            overflow: hidden;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .btn-login:hover::before {
            left: 100%;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(109, 31, 62, 0.4);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .alert {
            border-radius: 8px;
            border: none;
            margin-bottom: 20px;
            animation: slideDown 0.4s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-danger {
            background-color: #fff0f0;
            color: #c41e3a;
            border-left: 4px solid #c41e3a;
        }

        .alert-success {
            background-color: #f0fff4;
            color: #2d8659;
            border-left: 4px solid #2d8659;
        }

        .forgot-password {
            text-align: center;
            margin-top: 16px;
        }

        .forgot-password a {
            color: var(--primary-maroon);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            position: relative;
        }

        .forgot-password a::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 50%;
            width: 0;
            height: 2px;
            background: var(--accent-gold);
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }

        .forgot-password a:hover {
            color: var(--light-maroon);
        }

        .forgot-password a:hover::after {
            width: 100%;
        }

        .mb-3 {
            margin-bottom: 16px !important;
        }

        @media (max-width: 576px) {
            .login-header {
                padding: 30px 20px;
            }

            .login-body {
                padding: 30px 25px;
            }

            .login-header h3 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <i class="fas fa-hospital"></i>
                <h3>Hospital Management System</h3>
                <p>Sign in to continue</p>
            </div>
            <div class="login-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="username" class="form-label">
                            <i class="fas fa-user"></i> Username
                        </label>
                        <input type="text" class="form-control" id="username" name="username" 
                               value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" 
                               required autofocus placeholder="Enter your username">
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock"></i> Password
                        </label>
                        <input type="password" class="form-control" id="password" name="password" 
                               required placeholder="Enter your password">
                    </div>
                    
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="remember">
                        <label class="form-check-label" for="remember">Remember me</label>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-login">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </button>
                </form>
                
                <div class="forgot-password">
                    <a href="forgot_password.php">
                        <i class="fas fa-key"></i> Forgot password?
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>