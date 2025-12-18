<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_GET['lang']) && in_array($_GET['lang'], ['en', 'ar', 'fr'])) {
    $_SESSION['lang'] = $_GET['lang'];
}

$lang = $_SESSION['lang'] ?? 'en';

$langFile = __DIR__ . "/../lang/{$lang}.php";

if (file_exists($langFile)) {
    $translations = require $langFile;
} else {
    $translations = require __DIR__ . "/../lang/en.php";
}

function __($key) {
    global $translations;
    return $translations[$key] ?? $key;
}

function getCurrentLang() {
    return $_SESSION['lang'] ?? 'en';
}