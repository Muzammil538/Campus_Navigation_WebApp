function goBack() {
    window.history.back();
}

document.getElementById('recenterMapBtn').addEventListener('click', function() {
    alert('Map recentered to current location');
});

document.querySelector('.btn-mute').addEventListener('click', function() {
    const isMuted = this.classList.toggle('muted');
    this.innerHTML = isMuted 
        ? '<svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M11 5L6 9H2v6h4l5 4V5zM23 9l-6 6M17 9l6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg> Unmute Voice'
        : '<svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M11 5L6 9H2v6h4l5 4V5zM19.07 4.93a10 10 0 010 14.14M15.54 8.46a5 5 0 010 7.07" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg> Mute Voice';
});

document.querySelector('.btn-reroute').addEventListener('click', function() {
    alert('Calculating new route...');
});

document.querySelector('.btn-end-navigation').addEventListener('click', function() {
    if (confirm('Are you sure you want to end navigation?')) {
        window.location.href = 'map.html';
    }
});
