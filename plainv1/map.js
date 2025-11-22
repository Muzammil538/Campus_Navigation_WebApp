let scale = 1;
const mapContainer = document.getElementById('mapContainer');
const mapPlaceholder = document.querySelector('.map-placeholder');

document.getElementById('zoomIn').addEventListener('click', function() {
    if (scale < 2) {
        scale += 0.2;
        mapPlaceholder.style.transform = `scale(${scale})`;
    }
});

document.getElementById('zoomOut').addEventListener('click', function() {
    if (scale > 0.6) {
        scale -= 0.2;
        mapPlaceholder.style.transform = `scale(${scale})`;
    }
});

document.getElementById('recenterBtn').addEventListener('click', function() {
    scale = 1;
    mapPlaceholder.style.transform = `scale(1)`;
    mapPlaceholder.style.transformOrigin = 'center';
});

// Make markers clickable
document.querySelectorAll('.location-marker').forEach(marker => {
    marker.addEventListener('click', function() {
        const label = this.querySelector('.marker-label').textContent;
        alert(`Navigate to ${label}?`);
    });
});

// Pan functionality
let isDragging = false;
let startX, startY, scrollLeft, scrollTop;

mapContainer.addEventListener('mousedown', (e) => {
    isDragging = true;
    startX = e.pageX - mapContainer.offsetLeft;
    startY = e.pageY - mapContainer.offsetTop;
    scrollLeft = mapContainer.scrollLeft;
    scrollTop = mapContainer.scrollTop;
});

mapContainer.addEventListener('mouseleave', () => {
    isDragging = false;
});

mapContainer.addEventListener('mouseup', () => {
    isDragging = false;
});

mapContainer.addEventListener('mousemove', (e) => {
    if (!isDragging) return;
    e.preventDefault();
    const x = e.pageX - mapContainer.offsetLeft;
    const y = e.pageY - mapContainer.offsetTop;
    const walkX = (x - startX) * 2;
    const walkY = (y - startY) * 2;
    mapContainer.scrollLeft = scrollLeft - walkX;
    mapContainer.scrollTop = scrollTop - walkY;
});
