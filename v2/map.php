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

    <div class="map-container" id="map"></div>

    <!-- Map Controls -->
    <div class="map-controls">
        <button class="control-btn" onclick="map.setZoom(map.getZoom() + 1)" aria-label="Zoom in">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                <circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2"/>
                <path d="M11 8v6M8 11h6M21 21l-4.35-4.35" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </button>
        <button class="control-btn" onclick="map.setZoom(map.getZoom() - 1)" aria-label="Zoom out">
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
let map;
let markers = [];
let userMarker = null;
let selectedBuildingId = null;
let directionsService;
let directionsRenderer;
let buildings = <?php echo json_encode($buildings); ?>;
let userBookmarks = <?php echo json_encode($userBookmarks); ?>;

// Initialize map
function initMap() {
    // Default center (University campus center)
    const campusCenter = { lat: 28.545000, lng: 77.192500 };
    
    map = new google.maps.Map(document.getElementById('map'), {
        center: campusCenter,
        zoom: 16,
        mapTypeControl: true,
        streetViewControl: false,
        fullscreenControl: false,
        styles: [
            {
                featureType: "poi",
                elementType: "labels",
                stylers: [{ visibility: "off" }]
            }
        ]
    });

    directionsService = new google.maps.DirectionsService();
    directionsRenderer = new google.maps.DirectionsRenderer({
        map: map,
        suppressMarkers: false
    });

    // Add building markers
    buildings.forEach(building => {
        addBuildingMarker(building);
    });

    // Get user location
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            position => {
                const userPos = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };
                
                userMarker = new google.maps.Marker({
                    position: userPos,
                    map: map,
                    icon: {
                        path: google.maps.SymbolPath.CIRCLE,
                        scale: 8,
                        fillColor: '#4285F4',
                        fillOpacity: 1,
                        strokeColor: '#ffffff',
                        strokeWeight: 2
                    },
                    title: 'Your Location'
                });
                
                map.setCenter(userPos);
                
                <?php if ($_SESSION['voice_navigation']): ?>
                speak('Your location has been found');
                <?php endif; ?>
            },
            () => {
                console.log('Geolocation permission denied');
            }
        );
    }
}

function addBuildingMarker(building) {
    const position = {
        lat: parseFloat(building.latitude),
        lng: parseFloat(building.longitude)
    };
    
    // Custom marker icon based on category
    const markerColor = getCategoryColor(building.category);
    
    const marker = new google.maps.Marker({
        position: position,
        map: map,
        title: building.name,
        icon: {
            path: google.maps.SymbolPath.CIRCLE,
            scale: 10,
            fillColor: markerColor,
            fillOpacity: 0.9,
            strokeColor: '#ffffff',
            strokeWeight: 2
        }
    });
    
    // Add label
    const label = new google.maps.Marker({
        position: position,
        map: map,
        label: {
            text: building.name,
            color: '#333',
            fontSize: '12px',
            fontWeight: 'bold'
        },
        icon: {
            path: google.maps.SymbolPath.CIRCLE,
            scale: 0
        }
    });
    
    marker.addListener('click', () => {
        showBuildingInfo(building);
    });
    
    markers.push({ marker, label, building });
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
    directionsRenderer.setDirections({routes: []});
}

function navigateToBuilding() {
    const building = buildings.find(b => b.id == selectedBuildingId);
    if (!building || !userMarker) {
        alert('Unable to get your location');
        return;
    }
    
    const destination = {
        lat: parseFloat(building.latitude),
        lng: parseFloat(building.longitude)
    };
    
    const request = {
        origin: userMarker.getPosition(),
        destination: destination,
        travelMode: google.maps.TravelMode.WALKING
    };
    
    directionsService.route(request, (result, status) => {
        if (status === 'OK') {
            directionsRenderer.setDirections(result);
            
            // Get route info
            const route = result.routes[0].legs[0];
            const distance = route.distance.text;
            const duration = route.duration.text;
            
            <?php if ($_SESSION['voice_navigation']): ?>
            speak(`Route calculated. Distance: ${distance}. Duration: ${duration}`);
            provideStepByStepDirections(route.steps);
            <?php endif; ?>
            
            // Save to navigation history
            saveNavigationHistory(selectedBuildingId, route.distance.value, route.duration.value);
            
            // Redirect to directions page
            window.location.href = `directions.php?building=${selectedBuildingId}`;
        } else {
            alert('Could not calculate route');
        }
    });
}

function provideStepByStepDirections(steps) {
    let instructions = [];
    steps.forEach((step, index) => {
        const instruction = step.instructions.replace(/<[^>]*>/g, '');
        instructions.push(`Step ${index + 1}: ${instruction}`);
    });
    
    instructions.forEach((instruction, index) => {
        setTimeout(() => {
            speak(instruction);
        }, index * 3000);
    });
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

function centerOnUserLocation() {
    if (userMarker) {
        map.setCenter(userMarker.getPosition());
        map.setZoom(17);
        <?php if ($_SESSION['voice_navigation']): ?>
        speak('Centered on your location');
        <?php endif; ?>
    } else {
        alert('Unable to get your location');
    }
}

function saveNavigationHistory(buildingId, distance, duration) {
    fetch('api/save-navigation.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            building_id: buildingId,
            distance: distance,
            duration: duration
        })
    });
}

// Initialize map on load
window.addEventListener('load', initMap);
</script>

<?php include 'includes/footer.php'; ?>
