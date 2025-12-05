<?php
require_once __DIR__ . '/../config/config.php';

if (!isLoggedIn()) {
    header("Location: " . APP_URL . "/pages/auth/login.php");
    exit;
}
