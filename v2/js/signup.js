function togglePassword(fieldId) {
    const input = document.getElementById(fieldId);
    input.type = input.type === 'password' ? 'text' : 'password';
}

function checkPasswordStrength() {
    const password = document.getElementById('password').value;
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
}

function toggleRoleFields() {
    const role = document.getElementById('role').value;
    const studentFields = document.getElementById('studentFields');
    const departmentFields = document.getElementById('departmentFields');
    
    studentFields.style.display = 'none';
    departmentFields.style.display = 'none';
    
    if (role === 'student') {
        studentFields.style.display = 'block';
        departmentFields.style.display = 'block';
    } else if (role === 'staff') {
        departmentFields.style.display = 'block';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleRoleFields();
});
