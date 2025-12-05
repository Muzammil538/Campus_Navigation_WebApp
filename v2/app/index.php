<?php
// NO REDIRECTS - DIRECT PAGE LOADING
if (isLoggedIn()) {
    require_once __DIR__ . '/pages/home/index.php';
} else {
    require_once __DIR__ . '/pages/auth/login.php';
}
?>
