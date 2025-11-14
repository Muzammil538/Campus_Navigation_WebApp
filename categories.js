function goBack() {
    window.history.back();
}

// Add click animation to category cards
document.querySelectorAll('.category-card').forEach(card => {
    card.addEventListener('click', function(e) {
        e.preventDefault();
        this.style.transform = 'scale(0.95)';
        
        setTimeout(() => {
            window.location.href = this.href;
        }, 150);
    });
});
