// Password strength checker
document.getElementById('signupPassword').addEventListener('input', function() {
    const password = this.value;
    const strengthFill = document.getElementById('strengthFill');
    const strengthText = document.getElementById('strengthText');
    
    let strength = 0;
    
    if (password.length >= 8) strength++;
    if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
    if (password.match(/[0-9]/)) strength++;
    if (password.match(/[^a-zA-Z0-9]/)) strength++;
    
    strengthFill.className = 'strength-fill';
    strengthText.className = 'strength-text';
    
    if (strength === 0 || strength === 1) {
        strengthFill.classList.add('weak');
        strengthText.classList.add('weak');
        strengthText.textContent = 'Weak password';
    } else if (strength === 2 || strength === 3) {
        strengthFill.classList.add('medium');
        strengthText.classList.add('medium');
        strengthText.textContent = 'Medium password';
    } else {
        strengthFill.classList.add('strong');
        strengthText.classList.add('strong');
        strengthText.textContent = 'Strong password';
    }
});

// Toggle password visibility
document.getElementById('togglePassword').addEventListener('click', function() {
    const passwordInput = document.getElementById('signupPassword');
    const type = passwordInput.type === 'password' ? 'text' : 'password';
    passwordInput.type = type;
});

// Form submission
document.getElementById('signupForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const fullName = document.getElementById('fullName').value;
    const email = document.getElementById('signupEmail').value;
    const password = document.getElementById('signupPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    const agreeTerms = document.getElementById('agreeTerms').checked;
    const errorMessage = document.getElementById('errorMessage');
    const signupBtn = document.getElementById('signupBtn');
    
    // Clear previous error
    errorMessage.classList.remove('show');
    
    // Validation
    if (!fullName || !email || !password || !confirmPassword) {
        showError('Please fill in all fields');
        return;
    }
    
    if (password !== confirmPassword) {
        showError('Passwords do not match');
        return;
    }
    
    if (password.length < 8) {
        showError('Password must be at least 8 characters');
        return;
    }
    
    if (!agreeTerms) {
        showError('Please agree to the Terms of Service and Privacy Policy');
        return;
    }
    
    // Show loading state
    signupBtn.classList.add('loading');
    signupBtn.innerHTML = '<span>Creating account...</span>';
    
    // Simulate signup process
    setTimeout(() => {
        // Get existing users or create new array
        let users = JSON.parse(localStorage.getItem('users')) || [];
        
        // Check if user already exists
        const existingUser = users.find(u => u.email === email);
        if (existingUser) {
            signupBtn.classList.remove('loading');
            signupBtn.innerHTML = '<span>Create Account</span>';
            showError('An account with this email already exists');
            return;
        }
        
        // Create new user
        const newUser = {
            name: fullName,
            email: email,
            password: password, // In production, this should be hashed
            createdAt: new Date().toISOString()
        };
        
        users.push(newUser);
        localStorage.setItem('users', JSON.stringify(users));
        
        // Auto login
        localStorage.setItem('isLoggedIn', 'true');
        localStorage.setItem('currentUser', JSON.stringify({
            email: newUser.email,
            name: newUser.name,
            loginTime: new Date().toISOString()
        }));
        
        // Redirect to onboarding
        window.location.href = 'onboarding.html';
    }, 1500);
});

function showError(message) {
    const errorMessage = document.getElementById('errorMessage');
    errorMessage.textContent = message;
    errorMessage.classList.add('show');
}

// Social signup buttons
document.querySelectorAll('.btn-social').forEach(btn => {
    btn.addEventListener('click', function() {
        const provider = this.classList.contains('google') ? 'Google' : 'Apple';
        alert(`${provider} signup will be implemented with OAuth 2.0`);
    });
});
