function getStarted() {
    window.location.href = 'map.html';
}

// Simple fade-in animation
document.addEventListener('DOMContentLoaded', function() {
    const content = document.querySelector('.onboarding-content');
    content.style.opacity = '0';
    content.style.transform = 'translateY(20px)';
    
    setTimeout(() => {
        content.style.transition = 'all 0.6s ease';
        content.style.opacity = '1';
        content.style.transform = 'translateY(0)';
    }, 100);
});

