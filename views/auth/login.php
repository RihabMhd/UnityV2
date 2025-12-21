<?php 
require_once __DIR__ . '/../../config/lang.php';
?>
<!DOCTYPE html>
<html lang="<?php echo getCurrentLang(); ?>" dir="<?php echo getCurrentLang() === 'ar' ? 'rtl' : 'ltr'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo __('Login'); ?> - <?php echo __('Hospital Management'); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link rel="stylesheet" href="./assets/css/loginStyle.css">
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <i class="fa-solid fa-house-medical-flag"></i>
            <h1><?php echo __('Unity Care'); ?></h1>
            <p><?php echo __('Hospital Management'); ?></p>
        </div>
        
        <div class="login-body">
            <h2 style="margin-bottom: 10px; color: #333; font-size: 24px;"><?php echo __('Welcome Back'); ?></h2>
            <p style="margin-bottom: 30px; color: #666; font-size: 14px;"><?php echo __('Please login to your account'); ?></p>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger">
                    <i class="fa-solid fa-circle-exclamation"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="index.php?controller=auth&action=login">
                <div class="form-group">
                    <label for="username"><?php echo __('Username or Email'); ?></label>
                    <div class="input-group">
                        <i class="fa-solid fa-user"></i>
                        <input type="text" 
                               class="form-control" 
                               id="username" 
                               name="username" 
                               placeholder="<?php echo __('Enter your username or email'); ?>"
                               required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password"><?php echo __('Password'); ?></label>
                    <div class="input-group">
                        <i class="fa-solid fa-lock"></i>
                        <input type="password" 
                               class="form-control" 
                               id="password" 
                               name="password" 
                               placeholder="<?php echo __('Enter your password'); ?>"
                               required>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-right-to-bracket"></i> <?php echo __('Login'); ?>
                </button>
            </form>

            <div class="language-switcher">
                <a href="?<?php echo http_build_query(array_merge($_GET, ['lang' => 'en'])); ?>" 
                   class="<?php echo getCurrentLang() === 'en' ? 'active' : ''; ?>">EN</a>
                <a href="?<?php echo http_build_query(array_merge($_GET, ['lang' => 'fr'])); ?>" 
                   class="<?php echo getCurrentLang() === 'fr' ? 'active' : ''; ?>">FR</a>
                <a href="?<?php echo http_build_query(array_merge($_GET, ['lang' => 'ar'])); ?>" 
                   class="<?php echo getCurrentLang() === 'ar' ? 'active' : ''; ?>">AR</a>
            </div>
        </div>
    </div>
</body>
</html>