<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/models/User.php';
requireLogin();
$database = new Database();
$db = $database->getConnection();
$userModel = new User($db);
$userData = $userModel->getUserById($_SESSION['user_id']);
$pageTitle = 'Accessibility Options';
$customCSS = 'settings.css';
?>
<?php include __DIR__ . '/includes/header.php'; ?>
<div class="page-container">
    <header class="top-bar">
        <button class="back-btn" onclick="window.history.back()">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </button>
        <h1>Accessibility Options</h1>
        <div class="spacer"></div>
    </header>
    <div class="content">
        <form method="POST" action="settings.php">
            <input type="hidden" name="action" value="update_accessibility">
            <div class="settings-item toggle-item">
                <div class="icon-wrapper green">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                        <path d="M12 1a3 3 0 013 3v8a3 3 0 11-6 0V4a3 3 0 013-3z" stroke="currentColor" stroke-width="2"/>
                        <path d="M19 10v2a7 7 0 01-14 0v-2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </div>
                <div class="item-content">
                    <h3>Voice Navigation</h3>
                    <p>Turn-by-turn voice guidance</p>
                </div>
                <label class="toggle">
                    <input type="checkbox" name="voice_navigation" <?php echo $userData['voice_navigation'] ? 'checked' : ''; ?> onchange="this.form.submit()">
                    <span class="slider"></span>
                </label>
            </div>
            <div class="settings-item toggle-item">
                <div class="icon-wrapper purple">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" stroke="currentColor" stroke-width="2"/>
                        <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/>
                    </svg>
                </div>
                <div class="item-content">
                    <h3>Accessibility Mode</h3>
                    <p>Enhanced contrast and screen reader support</p>
                </div>
                <label class="toggle">
                    <input type="checkbox" name="accessibility_mode" <?php echo $userData['accessibility_mode'] ? 'checked' : ''; ?> onchange="this.form.submit()">
                    <span class="slider"></span>
                </label>
            </div>
        </form>
        <div class="settings-item">
            <div class="item-content">
                <h3>Text Size</h3>
                <p>Choose your preferred text size</p>
                <select onchange="document.body.style.fontSize=this.value">
                    <option value="16px">Default</option>
                    <option value="18px">Large</option>
                    <option value="20px">Extra Large</option>
                </select>
            </div>
        </div>
        <div class="settings-item">
            <div class="item-content">
                <h3>Contrast</h3>
                <p>Toggle high contrast mode</p>
                <button onclick="document.body.classList.toggle('high-contrast')">Toggle Contrast</button>
            </div>
        </div>
    </div>
    <?php include __DIR__ . '/includes/bottom-nav.php'; ?>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
