<?php 

spl_autoload_register(function ($class) {
    $file = "classes/models/$class.php";
    if (file_exists($file)) require_once $file;
    
    $file = "classes/repositories/$class.php";
    if (file_exists($file)) require_once $file;
});?>