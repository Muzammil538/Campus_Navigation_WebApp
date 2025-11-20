document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    const rememberMe = document.getElementById('rememberMe').checked;
    const errorMessage = document.getElementById('errorMessage');
    const loginBtn = document.getElementById('loginBtn');
    
    // Clear previous error
    errorMessage.classList.remove('show');
    
    // Basic validation
    if (!email || !password) {
        showError('Please fill in all fields');
        return;
    }
    
    // Show loading state
    loginBtn.classList.add('loading');
    loginBtn.innerHTML = '<span>Signing in...</span>';
    
    // Simulate login process
    setTimeout(() => {
        // Get stored users
        const users = JSON.parse(localStorage.getItem('users')) || [];
        const user = users.find(u => u.email === email && u.password === password);
        
        if (user) {
            // Successful login
            const userData = {
                email: user.email,
                name: user.name,
                loginTime: new Date().toISOString()
            };
            
            localStorage.setItem('isLoggedIn', 'true');
            localStorage.setItem('currentUser', JSON.stringify(userData));
            
            if (rememberMe) {
                localStorage.setItem('rememberMe', 'true');
            }
            
            // Check if user has seen onboarding
            const hasSeenOnboarding = localStorage.getItem('hasSeenOnboarding');
            
            if (hasSeenOnboarding === 'true') {
                window.location.href = 'map.html';
            } else {
                window.location.href = 'onboarding.html';
            }
        } else {
            // Login failed
            loginBtn.classList.remove('loading');
            loginBtn.innerHTML = '<span>Sign In</span>';
            showError('Invalid email or password');
        }
    }, 1500);
});

function showError(message) {
    const errorMessage = document.getElementById('errorMessage');
    errorMessage.textContent = message;
    errorMessage.classList.add('show');
}

// Toggle password visibility
document.getElementById('togglePassword').addEventListener('click', function() {
    const passwordInput = document.getElementById('password');
    const type = passwordInput.type === 'password' ? 'text' : 'password';
    passwordInput.type = type;
});

// Social login buttons
document.querySelectorAll('.btn-social').forEach(btn => {
    btn.addEventListener('click', function() {
        const provider = this.classList.contains('google') ? 'Google' : 'Apple';
        alert(`${provider} login will be implemented with OAuth 2.0`);
    });
});
