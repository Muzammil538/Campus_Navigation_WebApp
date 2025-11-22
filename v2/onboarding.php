<?php
require_once 'config/config.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $_SESSION['onboarding_completed'] = true;
    redirect('map.php');
}

$pageTitle = 'Welcome';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Campus Navigator</title>
    <link rel="stylesheet" href="css/onboarding.css">
</head>
<body>
    <div class="page-container">
        <div class="onboarding-content">
            <div class="logo-section">
                <svg class="campus-logo" width="80" height="80" viewBox="0 0 24 24" fill="none">
                    <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" stroke="#2196F3" stroke-width="2"/>
                </svg>
                <h1>Campus Guide</h1>
            </div>

            <div class="illustration">
                <div class="buildings-illustration">
                    <div class="building building-1"></div>
                    <div class="building building-2"></div>
                    <div class="building building-3"></div>
                </div>
                <div class="ground-line"></div>
            </div>

            <div class="text-content">
                <h2>Navigate Your Campus With Ease</h2>
                <p>Find buildings, services, and the fastest routes.</p>
                <p class="welcome-user">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</p>
            </div>

            <form method="POST">
                <button type="submit" class="btn-get-started">Get Started</button>
            </form>
        </div>
    </div>
    
    <script>
        if (<?php echo $_SESSION['voice_navigation'] ? 'true' : 'false'; ?>) {
            window.speechSynthesis.speak(new SpeechSynthesisUtterance('Welcome to Campus Navigator. Find buildings, services, and the fastest routes.'));
        }
    </script>
</body>
</html>
