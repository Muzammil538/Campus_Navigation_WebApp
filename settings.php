<?php
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'models/User.php';

requireLogin();

$database = new Database();
$db = $database->getConnection();
$userModel = new User($db);

$userData = $userModel->getUserById($_SESSION['user_id']);

// Get user settings
$query = "SELECT * FROM user_settings WHERE user_id = :user_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$settings = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'update_accessibility') {
        $accessibility_mode = isset($_POST['accessibility_mode']) ? 1 : 0;
        $voice_navigation = isset($_POST['voice_navigation']) ? 1 : 0;
        
        $userModel->id = $_SESSION['user_id'];
        $userModel->updateAccessibility($accessibility_mode, $voice_navigation);
        
        $_SESSION['accessibility_mode'] = $accessibility_mode;
        $_SESSION['voice_navigation'] = $voice_navigation;
        
        setFlashMessage('success', 'Accessibility settings updated');
        redirect('settings.php');
    } elseif ($action === 'update_profile') {
        $userModel->id = $_SESSION['user_id'];
        $userModel->full_name = sanitizeInput($_POST['full_name']);
        $userModel->phone = sanitizeInput($_POST['phone']);
        $userModel->department = sanitizeInput($_POST['department']);
        $userModel->student_id = sanitizeInput($_POST['student_id']);
        
        if ($userModel->updateProfile()) {
            $_SESSION['user_name'] = $userModel->full_name;
            setFlashMessage('success', 'Profile updated successfully');
        } else {
            setFlashMessage('error', 'Failed to update profile');
        }
        redirect('settings.php');
    }
}

$pageTitle = 'Settings';
$customCSS = 'settings.css';
?>
<?php include 'includes/header.php'; ?>

<div class="page-container">
    <header class="top-bar">
        <button class="back-btn" onclick="window.history.back()">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </button>
        <h1>Settings</h1>
        <div class="spacer"></div>
    </header>

    <div class="content">
        <div class="profile-section">
            <img src="<?php echo $userData['profile_image'] ?? 'images/default-avatar.svg'; ?>" alt="Profile" class="profile-img">
            <div class="profile-info">
                <h2><?php echo htmlspecialchars($userData['full_name']); ?></h2>
                <p class="role-badge"><?php echo ucfirst($userData['role']); ?></p>
                <div class="profile-actions">
                    <button class="btn-secondary" onclick="showEditProfile()">Edit Profile</button>
                </div>
            </div>
        </div>

        <!-- Edit Profile Modal (Hidden by default) -->
        <div id="editProfileModal" class="modal" style="display: none;">
            <div class="modal-content">
                <span class="close" onclick="closeEditProfile()">&times;</span>
                <h2>Edit Profile</h2>
                <form method="POST" action="settings.php">
                    <input type="hidden" name="action" value="update_profile">
                    
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="full_name" value="<?php echo htmlspecialchars($userData['full_name']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="tel" name="phone" value="<?php echo htmlspecialchars($userData['phone']); ?>">
                    </div>
                    
                    <?php if ($userData['role'] === 'student' || $userData['role'] === 'staff'): ?>
                    <div class="form-group">
                        <label>Department</label>
                        <input type="text" name="department" value="<?php echo htmlspecialchars($userData['department']); ?>">
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($userData['role'] === 'student'): ?>
                    <div class="form-group">
                        <label>Student ID</label>
                        <input type="text" name="student_id" value="<?php echo htmlspecialchars($userData['student_id']); ?>">
                    </div>
                    <?php endif; ?>
                    
                    <button type="submit" class="btn-primary">Save Changes</button>
                </form>
            </div>
        </div>

        <div class="settings-list">
            <div class="settings-item" onclick="window.location.href='accessibility.php'">
                <div class="icon-wrapper blue">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                        <path d="M12 8v8M8 12h8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </div>
                <div class="item-content">
                    <h3>Accessibility Options</h3>
                    <p>Voice navigation, text size, contrast</p>
                </div>
                <svg class="chevron" width="20" height="20" viewBox="0 0 24 24" fill="none">
                    <path d="M9 18l6-6-6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </div>

            <form method="POST" style="border: none;">
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

            <div class="settings-item" onclick="window.location.href='navigation-history.php'">
                <div class="icon-wrapper orange">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                        <path d="M12 6v6l4 2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </div>
                <div class="item-content">
                    <h3>Navigation History</h3>
                    <p>View your recent routes</p>
                </div>
                <svg class="chevron" width="20" height="20" viewBox="0 0 24 24" fill="none">
                    <path d="M9 18l6-6-6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </div>

            <div class="settings-item" onclick="window.location.href='feedback.php'">
                <div class="icon-wrapper teal">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                        <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2v10z" stroke="currentColor" stroke-width="2"/>
                    </svg>
                </div>
                <div class="item-content">
                    <h3>Send Feedback</h3>
                    <p>Help us improve the app</p>
                </div>
                <svg class="chevron" width="20" height="20" viewBox="0 0 24 24" fill="none">
                    <path d="M9 18l6-6-6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </div>

            <button class="settings-item logout-item" onclick="logout()">
                <div class="icon-wrapper red">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                        <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </div>
                <div class="item-content">
                    <h3>Logout</h3>
                    <p>Sign out of your account</p>
                </div>
            </button>
        </div>

        <div class="app-description">
            <p>Campus Navigator v1.0.0</p>
            <p>Made with ❤️ by ND Solutions</p>
        </div>
    </div>

    <?php include 'includes/bottom-nav.php'; ?>
</div>

<script>
function showEditProfile() {
    document.getElementById('editProfileModal').style.display = 'block';
}

function closeEditProfile() {
    document.getElementById('editProfileModal').style.display = 'none';
}

function logout() {
    if (confirm('Are you sure you want to logout?')) {
        <?php if ($_SESSION['voice_navigation']): ?>
        speak('Logging out. Goodbye!');
        setTimeout(() => {
            window.location.href = 'logout.php';
        }, 2000);
        <?php else: ?>
        window.location.href = 'logout.php';
        <?php endif; ?>
    }
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('editProfileModal');
    if (event.target == modal) {
        closeEditProfile();
    }
}
</script>

<style>
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.4);
}

.modal-content {
    background-color: white;
    margin: 10% auto;
    padding: 30px;
    border-radius: 12px;
    width: 90%;
    max-width: 500px;
}

.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}

.close:hover {
    color: #000;
}

.logout-item {
    background: none;
    border: none;
    width: 100%;
    text-align: left;
    cursor: pointer;
}

.icon-wrapper.red {
    background: #FFEBEE;
}

.icon-wrapper.red svg {
    color: #E74C3C;
}
</style>

<?php include 'includes/footer.php'; ?>
