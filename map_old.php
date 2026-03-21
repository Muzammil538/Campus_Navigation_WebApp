<?php
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'models/Building.php';
require_once 'models/Bookmark.php';

requireLogin();

$database = new Database();
$db = $database->getConnection();
$buildingModel = new Building($db);
$bookmarkModel = new Bookmark($db);

$buildings = $buildingModel->getAll();
$userBookmarks = $bookmarkModel->getUserBookmarks($_SESSION['user_id']);

$pageTitle = 'Campus Map';
$customCSS = 'map.css';
?>
<?php include 'includes/header.php'; ?>

<div class="page-container">
    <header class="map-header">
        <div class="header-content">
            <svg class="map-icon" width="24" height="24" viewBox="0 0 24 24" fill="none">
                <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" stroke="currentColor" stroke-width="2"/>
            </svg>
            <div class="header-text">
                <h1>Campus Guide</h1>
                <p>Find buildings, rooms, services</p>
            </div>
        </div>
        <button class="location-btn" onclick="centerOnUserLocation()" aria-label="Center on my location">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                <circle cx="12" cy="12" r="3" fill="currentColor"/>
            </svg>
        </button>
    </header>

    <div class="map-container" id="map" style="width: 100%; height: 350px; background: #e8f0e8; position: relative;">
        <div id="mapLoading" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; color: #666;">
            <p>Loading map...</p>
            <div style="margin-top: 10px; font-size: 12px; color: #999;">If map doesn't load, please check your API key</div>
        </div>
    </div>

    <!-- Map Controls -->
    <div class="map-controls">
        <button class="control-btn" onclick="if(map) map.zoomIn(); else alert('Map loading...');" aria-label="Zoom in">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                <circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2"/>
                <path d="M11 8v6M8 11h6M21 21l-4.35-4.35" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </button>
        <button class="control-btn" onclick="if(map) map.zoomOut(); else alert('Map loading...');" aria-label="Zoom out">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                <circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2"/>
                <path d="M8 11h6M21 21l-4.35-4.35" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </button>
    </div>

    <!-- Building Info Card -->
    <div class="building-info-card" id="buildingInfoCard" style="display: none;">
        <button class="close-card" onclick="closeBuildingCard()">&times;</button>
        <div class="card-content">
            <h3 id="cardBuildingName"></h3>
            <p id="cardBuildingDescription"></p>
            <div class="card-actions">
                <button class="btn-action" onclick="navigateToBuilding()">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                        <path d="M9 18l6-6-6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    Navigate
                </button>
                <button class="btn-action" onclick="viewBuildingDetails()">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                        <path d="M12 16v-4M12 8h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    Details
                </button>
                <button class="btn-action" onclick="toggleBookmark()" id="bookmarkBtn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                        <path d="M19 21l-7-5-7 5V5a2 2 0 012-2h10a2 2 0 012 2v16z" stroke="currentColor" stroke-width="2"/>
                    </svg>
                    Save
                </button>
            </div>
        </div>
    </div>

    <?php include 'includes/bottom-nav.php'; ?>
</div>

<script>
console.log('=== Map Script Starting ===');

let map = null;
let markers = [];
let userMarker = null;
let selectedBuildingId = null;
let buildings = <?php echo json_encode($buildings); ?>;
let userBookmarks = <?php echo json_encode($userBookmarks); ?>;

console.log('Buildings loaded:', buildings.length);
console.log('Leaflet available:', typeof L !== 'undefined');

function initMap() {
    console.log('initMap() called');
    
    const mapElement = document.getElementById('map');
    if (!mapElement) {
        console.error('❌ Map element not found!');
        return;
    }
    
    if (typeof L === 'undefined') {
        console.error('❌ Leaflet not loaded!');
        return;
    }
    
    try {
        console.log('Creating Leaflet map...');
        
        // Create map
        map = L.map('map', {
            center: [28.545000, 77.192500],
            zoom: 16,
            layers: []
        });
        
        console.log('✓ Map object created');
        
        // Add tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19,
            minZoom: 1
        }).addTo(map);
        
        console.log('✓ Tile layer added');
        
        // Hide loading message
        const loadingMsg = document.getElementById('mapLoading');
        if (loadingMsg) loadingMsg.remove();
        
        // Add building markers
        console.log('Adding', buildings.length, 'building markers...');
        buildings.forEach((building, index) => {
            addBuildingMarker(building);
        });
        
        console.log('✓ All markers added');
        
        // Get user location
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    console.log('✓ User location:', lat, lng);
                    
                    userMarker = L.circleMarker([lat, lng], {
                        radius: 8,
                        fillColor: '#4285F4',
                        color: '#ffffff',
                        weight: 2,
                        opacity: 1,
                        fillOpacity: 0.8
                    }).addTo(map).bindPopup('Your Location');
                    
                    map.setView([lat, lng], 17);
                },
                function(error) {
                    console.warn('Geolocation denied:', error.message);
                }
            );
        }
        
    } catch (error) {
        console.error('❌ Error initializing map:', error);
        mapElement.innerHTML = '<div style="padding: 20px; color: red;"><strong>Error:</strong> ' + error.message + '</div>';
    }
}

function addBuildingMarker(building) {
    if (!map) {
        console.warn('Map not ready');
        return;
    }
    
    const lat = parseFloat(building.latitude);
    const lng = parseFloat(building.longitude);
    
    const color = getCategoryColor(building.category);
    
    const markerHtml = `<div style="background-color: ${color}; border-radius: 50%; width: 24px; height: 24px; border: 3px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>`;
    
    const marker = L.marker([lat, lng], {
        icon: L.divIcon({
            html: markerHtml,
            iconSize: [24, 24],
            className: ''
        })
    }).addTo(map);
    
    marker.bindPopup(`<strong>${building.name}</strong><br>${building.description || ''}<br><button onclick="selectBuilding(${building.id}, event)">Select</button>`);
    
    marker.on('click', function() {
        selectedBuildingId = building.id;
        showBuildingInfo(building);
    });
    
    markers.push({ marker, building });
}

function getCategoryColor(category) {
    const colors = {
        'academic': '#F39C12',
        'library': '#3498DB',
        'laboratory': '#9B59B6',
        'cafeteria': '#E74C3C',
        'sports': '#27AE60',
        'administration': '#34495E',
        'dormitory': '#16A085',
        'parking': '#95A5A6'
    };
    return colors[category] || '#2196F3';
}

function selectBuilding(buildingId, event) {
    event.stopPropagation();
    selectedBuildingId = buildingId;
    const building = buildings.find(b => b.id == buildingId);
    if (building) {
        showBuildingInfo(building);
    }
}

function showBuildingInfo(building) {
    selectedBuildingId = building.id;
    const card = document.getElementById('buildingInfoCard');
    
    document.getElementById('cardBuildingName').textContent = building.name;
    document.getElementById('cardBuildingDescription').textContent = building.description || 'No description available';
    
    const isBookmarked = userBookmarks.some(b => b.id == building.id);
    const bookmarkBtn = document.getElementById('bookmarkBtn');
    
    if (isBookmarked) {
        bookmarkBtn.innerHTML = '<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M19 21l-7-5-7 5V5a2 2 0 012-2h10a2 2 0 012 2v16z" stroke="currentColor" stroke-width="2"/></svg> Saved';
    } else {
        bookmarkBtn.innerHTML = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M19 21l-7-5-7 5V5a2 2 0 012-2h10a2 2 0 012 2v16z" stroke="currentColor" stroke-width="2"/></svg> Save';
    }
    
    card.style.display = 'block';
}

function closeBuildingCard() {
    document.getElementById('buildingInfoCard').style.display = 'none';
}

function navigateToBuilding() {
    if (selectedBuildingId) {
        window.location.href = `directions.php?building=${selectedBuildingId}`;
    }
}

function viewBuildingDetails() {
    if (selectedBuildingId) {
        window.location.href = `building-detail.php?id=${selectedBuildingId}`;
    }
}

function toggleBookmark() {
    if (!selectedBuildingId) return;
    
    fetch('api/toggle-bookmark.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ building_id: selectedBuildingId })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) location.reload();
    });
}

function centerOnUserLocation() {
    if (userMarker && map) {
        const latlng = userMarker.getLatLng();
        map.setView([latlng.lat, latlng.lng], 17);
    }
}

function saveNavigationHistory(buildingId, distance, duration) {
    fetch('api/save-navigation.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ building_id: buildingId, distance, duration })
    });
}

// Initialize on window load
console.log('Document ready state:', document.readyState);

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOMContentLoaded - initializing map');
        setTimeout(initMap, 200);
    });
} else {
    console.log('Document already loaded - initializing map immediately');
    setTimeout(initMap, 200);
}

window.addEventListener('load', function() {
    console.log('Window load event');
    if (!map) setTimeout(initMap, 200);
});
</script>

function showBuildingInfo(building) {
    selectedBuildingId = building.id;
    const card = document.getElementById('buildingInfoCard');
    
    document.getElementById('cardBuildingName').textContent = building.name;
    document.getElementById('cardBuildingDescription').textContent = building.description || 'No description available';
    
    // Check if bookmarked
    const isBookmarked = userBookmarks.some(b => b.id == building.id);
    const bookmarkBtn = document.getElementById('bookmarkBtn');
    
    if (isBookmarked) {
        bookmarkBtn.innerHTML = '<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M19 21l-7-5-7 5V5a2 2 0 012-2h10a2 2 0 012 2v16z" stroke="currentColor" stroke-width="2"/></svg> Saved';
    } else {
        bookmarkBtn.innerHTML = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M19 21l-7-5-7 5V5a2 2 0 012-2h10a2 2 0 012 2v16z" stroke="currentColor" stroke-width="2"/></svg> Save';
    }
    
    card.style.display = 'block';
    
    // Announce building for voice navigation
    <?php if ($_SESSION['voice_navigation']): ?>
    speak(`${building.name}. ${building.description || ''}`);
    <?php endif; ?>
}

function closeBuildingCard() {
    document.getElementById('buildingInfoCard').style.display = 'none';
}

function navigateToBuilding() {
    const building = buildings.find(b => b.id == selectedBuildingId);
    if (!building) {
        alert('Building not found');
        return;
    }
    
    // Go to directions page
    window.location.href = `directions.php?building=${selectedBuildingId}`;
}

function viewBuildingDetails() {
    window.location.href = `building-detail.php?id=${selectedBuildingId}`;
}

function toggleBookmark() {
    fetch('api/toggle-bookmark.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            building_id: selectedBuildingId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update UI
            const building = buildings.find(b => b.id == selectedBuildingId);
            if (data.action === 'added') {
                userBookmarks.push(building);
                speak('Building saved to favorites');
            } else {
                userBookmarks = userBookmarks.filter(b => b.id != selectedBuildingId);
                speak('Building removed from favorites');
            }
            showBuildingInfo(building);
        }
    });
}

window.addEventListener('load', function() {
    console.log('Window load event');
    if (!map) setTimeout(initMap, 200);
});
</script>

<?php include 'includes/footer.php'; ?>
