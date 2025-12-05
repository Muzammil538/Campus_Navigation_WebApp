<?php
session_start();

define('APP_URL', rtrim(dirname($_SERVER['SCRIPT_NAME']), '/'));

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: " . APP_URL . "/pages/auth/login.php");
        exit;
    }
}

function redirect($path) {
    header("Location: " . APP_URL . $path);
    exit;
}

function sanitize($v) {
    return htmlspecialchars(trim($v), ENT_QUOTES, 'UTF-8');
}
?>
