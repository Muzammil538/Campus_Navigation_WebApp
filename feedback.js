function goBack() {
    window.history.back();
}

function skipFeedback() {
    window.location.href = 'map.html';
}

let selectedRating = 0;
let selectedCategory = 'general';

// Star rating functionality
document.querySelectorAll('.star').forEach(star => {
    star.addEventListener('click', function() {
        selectedRating = parseInt(this.dataset.rating);
        updateStars();
    });
    
    star.addEventListener('mouseenter', function() {
        const rating = parseInt(this.dataset.rating);
        highlightStars(rating);
    });
});

document.getElementById('starRating').addEventListener('mouseleave', function() {
    updateStars();
});

function highlightStars(rating) {
    document.querySelectorAll('.star').forEach((star, index) => {
        if (index < rating) {
            star.classList.add('active');
        } else {
            star.classList.remove('active');
        }
    });
}

function updateStars() {
    highlightStars(selectedRating);
}

// Category selection
document.querySelectorAll('.category-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.category-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        selectedCategory = this.dataset.category;
    });
});

// File input
document.getElementById('fileInput').addEventListener('change', function(e) {
    if (e.target.files.length > 0) {
        const fileName = e.target.files[0].name;
        document.querySelector('.choose-file-btn').textContent = fileName;
    }
});

// Submit feedback
document.getElementById('submitBtn').addEventListener('click', function() {
    const comments = document.getElementById('comments').value;
    const name = document.getElementById('name').value;
    const email = document.getElementById('email').value;
    
    if (!comments.trim() && selectedRating === 0) {
        alert('Please provide a rating or comments');
        return;
    }
    
    // Simulate submission
    this.disabled = true;
    this.textContent = 'Submitting...';
    
    setTimeout(() => {
        alert('Thank you for your feedback!');
        window.location.href = 'map.html';
    }, 1000);
});
