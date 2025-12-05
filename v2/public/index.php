<?php
// WORK FROM PROJECT ROOT - NO PATH ISSUES
chdir(dirname(__DIR__));  // Go to /v2/ (project root)
require_once 'app/config/config.php';

if (isLoggedIn()) {
    require_once 'app/pages/home/index.php';
} else {
    require_once 'app/pages/auth/login.php';
}
?>
