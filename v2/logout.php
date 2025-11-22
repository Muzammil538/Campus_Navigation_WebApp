<?php
require_once 'config/config.php';

// Destroy session
session_unset();
session_destroy();

// Clear remember me cookie
if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, '/');
}

// Redirect to login
redirect('login.php?logout=1');
?>
