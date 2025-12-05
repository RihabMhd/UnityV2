<?php

class Helper {
    

    public static function clean($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    public static function showSuccess($message) {
        return '<div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> ' . $message . '
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>';
    }

    public static function showError($message) {
        return '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle"></i> ' . $message . '
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>';
    }

    public static function showInfo($message) {
        return '<div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="fas fa-info-circle"></i> ' . $message . '
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>';
    }

  

}
?>