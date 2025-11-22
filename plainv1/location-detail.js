function goBack() {
    window.history.back();
}

document.getElementById('favoriteBtn').addEventListener('click', function() {
    this.classList.toggle('active');
});

document.querySelectorAll('.floor-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.floor-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
    });
});

document.getElementById('navigateBtn').addEventListener('click', function() {
    window.location.href = 'directions.html';
});

document.getElementById('addToFavoritesBtn').addEventListener('click', function() {
    alert('Added to favorites!');
    document.getElementById('favoriteBtn').classList.add('active');
});

document.querySelector('.share-btn').addEventListener('click', function() {
    if (navigator.share) {
        navigator.share({
            title: 'Engineering Complex A',
            text: 'Check out this location on Campus Navigator',
            url: window.location.href
        });
    } else {
        alert('Share: Engineering Complex A');
    }
});
