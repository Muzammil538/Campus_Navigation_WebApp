<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Error reporting for development (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Site configuration
define('SITE_URL', 'http://localhost/campus-navigator');
define('SITE_NAME', 'Campus Navigator');
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB

// Google Maps API Key (Get your own from Google Cloud Console)
define('GOOGLE_MAPS_API_KEY', 'AIzaSyB2t4hOvy4D0fX0nb8T_By3xp4ibdkjWTQ');

// Timezone
date_default_timezone_set('Asia/Kolkata');

// Security settings
define('PASSWORD_MIN_LENGTH', 8);
define('SESSION_TIMEOUT', 30 * 60); // 30 minutes

// Include database connection
require_once __DIR__ . '/database.php';

// Helper functions
function redirect($url) {
    header("Location: " . $url);
    exit();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        redirect('login.php');
    }
}

function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    return [
        'id' => $_SESSION['user_id'],
        'name' => $_SESSION['user_name'],
        'email' => $_SESSION['user_email'],
        'role' => $_SESSION['user_role']
    ];
}

function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

function generateToken() {
    return bin2hex(random_bytes(32));
}

function setFlashMessage($type, $message) {
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message
    ];
}

function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $message;
    }
    return null;
}

// Check session timeout
function checkSessionTimeout() {
    if (isLoggedIn()) {
        if (isset($_SESSION['last_activity'])) {
            $elapsed = time() - $_SESSION['last_activity'];
            if ($elapsed > SESSION_TIMEOUT) {
                session_unset();
                session_destroy();
                redirect('login.php?timeout=1');
            }
        }
        $_SESSION['last_activity'] = time();
    }
}

checkSessionTimeout();
?>
