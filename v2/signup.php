<?php
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'models/User.php';

if (isLoggedIn()) {
    redirect('map.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = sanitizeInput($_POST['full_name']);
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = sanitizeInput($_POST['role']);
    $student_id = sanitizeInput($_POST['student_id'] ?? '');
    $department = sanitizeInput($_POST['department'] ?? '');
    $phone = sanitizeInput($_POST['phone'] ?? '');
    $agree_terms = isset($_POST['agree_terms']);
    
    // Validation
    if (empty($full_name) || empty($email) || empty($password) || empty($role)) {
        $error = 'Please fill in all required fields';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format';
    } elseif (strlen($password) < PASSWORD_MIN_LENGTH) {
        $error = 'Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } elseif (!$agree_terms) {
        $error = 'Please agree to the Terms of Service';
    } else {
        $database = new Database();
        $db = $database->getConnection();
        $user = new User($db);
        
        $user->email = $email;
        
        if ($user->emailExists()) {
            $error = 'An account with this email already exists';
        } else {
            $user->full_name = $full_name;
            $user->password = $password;
            $user->role = $role;
            $user->student_id = $student_id;
            $user->department = $department;
            $user->phone = $phone;
            
            if ($user->register()) {
                // Auto login
                $_SESSION['user_id'] = $user->id;
                $_SESSION['user_name'] = $user->full_name;
                $_SESSION['user_email'] = $user->email;
                $_SESSION['user_role'] = $user->role;
                $_SESSION['last_activity'] = time();
                
                redirect('onboarding.php');
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}

$pageTitle = 'Sign Up';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Campus Navigator</title>
    <link rel="stylesheet" href="css/signup.css">
</head>
<body>
    <div class="page-container">
        <div class="signup-content">
            <button class="back-btn" onclick="window.location.href='login.php'">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </button>

            <div class="header-section">
                <div class="logo-container">
                    <svg class="app-logo" width="60" height="60" viewBox="0 0 24 24" fill="none">
                        <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" stroke="#2196F3" stroke-width="2"/>
                    </svg>
                </div>
                <h1>Create Account</h1>
                <p>Join Campus Navigator today</p>
            </div>

            <?php if ($error): ?>
                <div class="error-message show" role="alert">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form class="signup-form" method="POST" action="signup.php">
                <div class="form-group">
                    <label for="full_name">Full Name *</label>
                    <div class="input-wrapper">
                        <svg class="input-icon" width="20" height="20" viewBox="0 0 24 24" fill="none">
                            <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2" stroke="currentColor" stroke-width="2"/>
                            <circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="2"/>
                        </svg>
                        <input 
                            type="text" 
                            id="full_name" 
                            name="full_name"
                            placeholder="Enter your full name"
                            value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>"
                            required
                        >
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">Email *</label>
                    <div class="input-wrapper">
                        <svg class="input-icon" width="20" height="20" viewBox="0 0 24 24" fill="none">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" stroke="currentColor" stroke-width="2"/>
                            <path d="M22 6l-10 7L2 6" stroke="currentColor" stroke-width="2"/>
                        </svg>
                        <input 
                            type="email" 
                            id="email" 
                            name="email"
                            placeholder="Enter your email"
                            value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                            required
                        >
                    </div>
                </div>

                <div class="form-group">
                    <label for="role">I am a *</label>
                    <div class="input-wrapper">
                        <select id="role" name="role" required onchange="toggleRoleFields()">
                            <option value="">Select your role</option>
                            <option value="student" <?php echo (isset($_POST['role']) && $_POST['role'] == 'student') ? 'selected' : ''; ?>>Student</option>
                            <option value="staff" <?php echo (isset($_POST['role']) && $_POST['role'] == 'staff') ? 'selected' : ''; ?>>Staff</option>
                            <option value="visitor" <?php echo (isset($_POST['role']) && $_POST['role'] == 'visitor') ? 'selected' : ''; ?>>Visitor</option>
                        </select>
                    </div>
                </div>

                <div class="form-group role-specific" id="studentFields" style="display: none;">
                    <label for="student_id">Student ID</label>
                    <div class="input-wrapper">
                        <input 
                            type="text" 
                            id="student_id" 
                            name="student_id"
                            placeholder="Enter your student ID"
                            value="<?php echo isset($_POST['student_id']) ? htmlspecialchars($_POST['student_id']) : ''; ?>"
                        >
                    </div>
                </div>

                <div class="form-group role-specific" id="departmentFields" style="display: none;">
                    <label for="department">Department</label>
                    <div class="input-wrapper">
                        <input 
                            type="text" 
                            id="department" 
                            name="department"
                            placeholder="Enter your department"
                            value="<?php echo isset($_POST['department']) ? htmlspecialchars($_POST['department']) : ''; ?>"
                        >
                    </div>
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <div class="input-wrapper">
                        <input 
                            type="tel" 
                            id="phone" 
                            name="phone"
                            placeholder="Enter your phone number"
                            value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>"
                        >
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password *</label>
                    <div class="input-wrapper">
                        <svg class="input-icon" width="20" height="20" viewBox="0 0 24 24" fill="none">
                            <rect x="3" y="11" width="18" height="11" rx="2" stroke="currentColor" stroke-width="2"/>
                            <path d="M7 11V7a5 5 0 0110 0v4" stroke="currentColor" stroke-width="2"/>
                        </svg>
                        <input 
                            type="password" 
                            id="password" 
                            name="password"
                            placeholder="Create a password"
                            required
                            oninput="checkPasswordStrength()"
                        >
                        <button type="button" class="toggle-password" onclick="togglePassword('password')">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" stroke="currentColor" stroke-width="2"/>
                                <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/>
                            </svg>
                        </button>
                    </div>
                    <div class="password-strength" id="passwordStrength">
                        <div class="strength-bar">
                            <div class="strength-fill" id="strengthFill"></div>
                        </div>
                        <span class="strength-text" id="strengthText"></span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm Password *</label>
                    <div class="input-wrapper">
                        <svg class="input-icon" width="20" height="20" viewBox="0 0 24 24" fill="none">
                            <rect x="3" y="11" width="18" height="11" rx="2" stroke="currentColor" stroke-width="2"/>
                            <path d="M7 11V7a5 5 0 0110 0v4" stroke="currentColor" stroke-width="2"/>
                        </svg>
                        <input 
                            type="password" 
                            id="confirm_password" 
                            name="confirm_password"
                            placeholder="Confirm your password"
                            required
                        >
                    </div>
                </div>

                <label class="checkbox-container">
                    <input type="checkbox" name="agree_terms" id="agree_terms" required>
                    <span>I agree to the <a href="terms.php" class="link">Terms of Service</a> and <a href="privacy.php" class="link">Privacy Policy</a></span>
                </label>

                <button type="submit" class="btn-signup">
                    <span>Create Account</span>
                </button>
            </form>

            <div class="login-prompt">
                <p>Already have an account? <a href="login.php">Sign In</a></p>
            </div>
        </div>
    </div>

    <script src="js/signup.js"></script>
</body>
</html>
