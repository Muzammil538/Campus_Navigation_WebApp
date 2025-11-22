// Authentication utility functions

// Check if user is logged in
function isAuthenticated() {
    return localStorage.getItem('isLoggedIn') === 'true';
}

// Get current user
function getCurrentUser() {
    const userStr = localStorage.getItem('currentUser');
    return userStr ? JSON.parse(userStr) : null;
}

// Logout function
function logout() {
    localStorage.removeItem('isLoggedIn');
    localStorage.removeItem('currentUser');
    localStorage.removeItem('rememberMe');
    window.location.href = 'login.html';
}

// Protect pages that require authentication
function requireAuth() {
    if (!isAuthenticated()) {
        window.location.href = 'login.html';
    }
}

// Auto logout after inactivity (optional)
let inactivityTimer;
function resetInactivityTimer() {
    clearTimeout(inactivityTimer);
    // Auto logout after 30 minutes of inactivity
    inactivityTimer = setTimeout(() => {
        if (isAuthenticated() && !localStorage.getItem('rememberMe')) {
            logout();
        }
    }, 30 * 60 * 1000);
}

// Track user activity
if (isAuthenticated()) {
    ['mousedown', 'keypress', 'scroll', 'touchstart'].forEach(event => {
        document.addEventListener(event, resetInactivityTimer, true);
    });
    resetInactivityTimer();
}
