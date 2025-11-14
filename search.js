const searchInput = document.getElementById('searchInput');

searchInput.addEventListener('input', function(e) {
    const query = e.target.value.toLowerCase();
    const locationItems = document.querySelectorAll('.location-item');
    
    locationItems.forEach(item => {
        const text = item.textContent.toLowerCase();
        if (text.includes(query)) {
            item.style.display = 'flex';
        } else {
            item.style.display = 'none';
        }
    });
});

// Remove recent search
document.querySelectorAll('.remove-btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.stopPropagation();
        this.parentElement.remove();
    });
});

// Voice search
document.querySelector('.voice-btn').addEventListener('click', function() {
    alert('Voice search activated');
});
