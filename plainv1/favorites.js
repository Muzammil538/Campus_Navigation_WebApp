// Tab functionality
document.querySelectorAll('.tab').forEach(tab => {
    tab.addEventListener('click', function() {
        document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
        this.classList.add('active');
        
        const filterType = this.dataset.tab;
        filterBookmarks(filterType);
    });
});

function filterBookmarks(type) {
    const cards = document.querySelectorAll('.bookmark-card');
    
    cards.forEach(card => {
        if (type === 'all') {
            card.style.display = 'block';
        } else {
            const cardType = card.dataset.type;
            card.style.display = cardType === type ? 'block' : 'none';
        }
    });
}

// Favorite button functionality
document.querySelectorAll('.favorite-btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.stopPropagation();
        this.classList.toggle('active');
        
        if (!this.classList.contains('active')) {
            setTimeout(() => {
                this.closest('.bookmark-card').style.opacity = '0';
                setTimeout(() => {
                    this.closest('.bookmark-card').remove();
                }, 300);
            }, 200);
        }
    });
});

// Card click functionality
document.querySelectorAll('.bookmark-card').forEach(card => {
    card.addEventListener('click', function() {
        window.location.href = 'location-detail.html';
    });
});

// Filter button
document.getElementById('filterBtn').addEventListener('click', function() {
    alert('Filter options:\n- Sort by distance\n- Sort by name\n- Sort by recently added');
});
