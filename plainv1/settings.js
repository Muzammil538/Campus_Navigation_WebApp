function goBack() {
    window.history.back();
}

document.getElementById('darkModeToggle').addEventListener('change', function() {
    if (this.checked) {
        document.body.classList.add('dark-mode');
        localStorage.setItem('darkMode', 'enabled');
    } else {
        document.body.classList.remove('dark-mode');
        localStorage.setItem('darkMode', 'disabled');
    }
});

// Check for saved dark mode preference
if (localStorage.getItem('darkMode') === 'enabled') {
    document.getElementById('darkModeToggle').checked = true;
    document.body.classList.add('dark-mode');
}
