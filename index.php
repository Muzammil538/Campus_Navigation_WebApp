<?php
require_once 'config/config.php';

// Check authentication and redirect
if (isLoggedIn()) {
    if (!isset($_SESSION['onboarding_completed'])) {
        redirect('onboarding.php');
    } else {
        redirect('map.php');
    }
} else {
    redirect('login.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campus Navigator</title>
    <link rel="stylesheet" href="css/index.css">
</head>
<body>
    <div class="splash-screen">
        <div class="splash-content">
            <div class="logo-container">
                <svg class="app-logo" width="100" height="100" viewBox="0 0 24 24" fill="none">
                    <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" stroke="#2196F3" stroke-width="2"/>
                </svg>
            </div>
            <h1 class="app-title">Campus Navigator</h1>
            <p class="app-tagline">Find Your Way</p>
            <div class="loading-spinner"></div>
        </div>
    </div>
</body>
</html>
