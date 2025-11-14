function goBack() {
    window.history.back();
}

document.querySelector('.sos-btn').addEventListener('click', function() {
    if (confirm('Emergency services will be contacted and your location will be shared. Continue?')) {
        alert('Contacting emergency services...\nSharing your location...');
        // In a real app, this would trigger actual emergency protocols
    }
});

document.querySelectorAll('.call-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const service = this.closest('.service-card').querySelector('h3').textContent;
        alert(`Calling ${service}...`);
    });
});

document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
    });
});
