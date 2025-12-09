<?php
chdir(dirname(__DIR__));  // Go to /v2/ (project root)
require_once 'app/config/config.php';
// NO REDIRECTS - DIRECT PAGE LOADING
if (isLoggedIn()) {
    require_once __DIR__ . '/pages/home/index.php';
} else {
    require_once __DIR__ . '/pages/auth/login.php';
}
?>
