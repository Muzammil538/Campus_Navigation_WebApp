<?php
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'models/Building.php';

requireLogin();

$buildingId = isset($_GET['building']) ? intval($_GET['building']) : 0;

if ($buildingId <= 0) {
    redirect('map.php');
}

$database = new Database();
$db = $database->getConnection();
$buildingModel = new Building($db);
$building = $buildingModel->getById($buildingId);

if (!$building) {
    redirect('map.php');
}

$pageTitle = 'Navigation to ' . $building['name'];
$customCSS = 'directions.css';
?>
<?php include 'includes/header.php'; ?>

<div class="page-container">
    <header class="top-bar">
        <button class="back-btn" onclick="window.location.href='map.php'">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </button>
        <h1>Directions</h1>
        <button class="more-btn" onclick="showOptions()">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                <circle cx="12" cy="5" r="1.5" fill="currentColor"/>
                <circle cx="12" cy="12" r="1.5" fill="currentColor"/>
                <circle cx="12" cy="19" r="1.5" fill="currentColor"/>
            </svg>
        </button>
    </header>

    <div class="trip-info">
        <div class="time-info">
            <div class="duration">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                    <path d="M12 6v6l4 2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
                <span class="duration-text" id="durationText">--</span>
            </div>
            <p class="arrival-time" id="arrivalInfo">Calculating route...</p>
        </div>
        <p class="destination-name">To: <?php echo htmlspecialchars($building['name']); ?></p>
    </div>

    <div class="map-preview" id="directionsMap"></div>

    <div class="current-instruction" id="currentInstruction" style="display: none;">
        <div class="instruction-icon">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none">
                <path d="M12 2v10m0 0l4-4m-4 4l-4-4" stroke="#2196F3" stroke-width="2.5" stroke-linecap="round"/>
            </svg>
        </div>
        <div class="instruction-text">
            <h3 id="instructionTitle">Starting navigation...</h3>
            <p id="instructionDetails">Getting your location...</p>
        </div>
        <span class="distance-badge" id="distanceBadge">--</span>
    </div>

    <div class="navigation-controls">
        <button class="btn-control" id="voiceToggle" onclick="toggleVoice()">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                <path d="M11 5L6 9H2v6h4l5 4V5zM19.07 4.93a10 10 0 010 14.14M15.54 8.46a5 5 0 010 7.07" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
            <span>Voice: ON</span>
        </button>
        <button class="btn-control" onclick="recenterMap()">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                <circle cx="12" cy="12" r="3" fill="currentColor"/>
            </svg>
            <span>Recenter</span>
        </button>
    </div>

    <div class="direction-steps">
        <h3 class="steps-header">Route Steps</h3>
        <div id="stepsList"></div>
    </div>

    <button class="btn-end-navigation" onclick="endNavigation()">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
            <rect x="6" y="6" width="12" height="12" stroke="white" stroke-width="2" fill="white"/>
        </svg>
        End Navigation
    </button>

    <?php include 'includes/bottom-nav.php'; ?>
</div>

<script>
let map, directionsService, directionsRenderer;
let userMarker, destinationMarker;
let watchId;
let currentStepIndex = 0;
let routeSteps = [];
let voiceEnabled = <?php echo $_SESSION['voice_navigation'] ? 'true' : 'false'; ?>;
let isNavigating = false;

const destination = {
    lat: parseFloat('<?php echo $building['latitude']; ?>'),
    lng: parseFloat('<?php echo $building['longitude']; ?>'),
    name: '<?php echo addslashes($building['name']); ?>'
};

function initMap() {
    map = new google.maps.Map(document.getElementById('directionsMap'), {
        zoom: 16,
        center: destination,
        disableDefaultUI: true,
        zoomControl: true
    });

    directionsService = new google.maps.DirectionsService();
    directionsRenderer = new google.maps.DirectionsRenderer({
        map: map,
        suppressMarkers: false,
        polylineOptions: {
            strokeColor: '#2196F3',
            strokeWeight: 5
        }
    });

    // Get user location and start navigation
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            position => {
                const userPos = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };
                
                calculateRoute(userPos, destination);
                startLocationTracking();
            },
            error => {
                alert('Unable to get your location. Please enable location services.');
                window.location.href = 'map.php';
            }
        );
    }
}

function calculateRoute(origin, destination) {
    const request = {
        origin: origin,
        destination: destination,
        travelMode: google.maps.TravelMode.WALKING
    };

    directionsService.route(request, (result, status) => {
        if (status === 'OK') {
            directionsRenderer.setDirections(result);
            
            const route = result.routes[0].legs[0];
            routeSteps = route.steps;
            
            // Update UI
            document.getElementById('durationText').textContent = route.duration.text;
            document.getElementById('arrivalInfo').textContent = `${route.distance.text} • ETA ${getETA(route.duration.value)}`;
            
            // Display steps
            displaySteps(routeSteps);
            
            // Start navigation
            isNavigating = true;
            updateCurrentInstruction(0);
            
            // Voice announcement
            if (voiceEnabled) {
                speak(`Navigation started to ${destination.name}. Total distance: ${route.distance.text}. Estimated time: ${route.duration.text}`);
                setTimeout(() => {
                    announceStep(0);
                }, 3000);
            }
        } else {
            alert('Could not calculate route: ' + status);
            window.location.href = 'map.php';
        }
    });
}

function displaySteps(steps) {
    const stepsList = document.getElementById('stepsList');
    stepsList.innerHTML = '';
    
    steps.forEach((step, index) => {
        const stepDiv = document.createElement('div');
        stepDiv.className = 'step-item';
        stepDiv.innerHTML = `
            <div class="step-number">${index + 1}</div>
            <div class="step-content">
                <p class="step-instruction">${step.instructions}</p>
                <span class="step-distance">${step.distance.text}</span>
            </div>
        `;
        stepsList.appendChild(stepDiv);
    });
}

function startLocationTracking() {
    watchId = navigator.geolocation.watchPosition(
        position => {
            const userPos = {
                lat: position.coords.latitude,
                lng: position.coords.longitude
            };
            
            updateUserPosition(userPos);
            checkStepProgress(userPos);
        },
        error => {
            console.error('Location tracking error:', error);
        },
        {
            enableHighAccuracy: true,
            maximumAge: 1000,
            timeout: 5000
        }
    );
}

function updateUserPosition(position) {
    if (userMarker) {
        userMarker.setPosition(position);
    } else {
        userMarker = new google.maps.Marker({
            position: position,
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
    }
}

function checkStepProgress(userPos) {
    if (!isNavigating || currentStepIndex >= routeSteps.length) return;
    
    const currentStep = routeSteps[currentStepIndex];
    const stepEndPos = currentStep.end_location;
    
    const distance = google.maps.geometry.spherical.computeDistanceBetween(
        new google.maps.LatLng(userPos.lat, userPos.lng),
        stepEndPos
    );
    
    // Update distance badge
    document.getElementById('distanceBadge').textContent = Math.round(distance) + 'm';
    
    // If within 20 meters of step end, move to next step
    if (distance < 20 && currentStepIndex < routeSteps.length - 1) {
        currentStepIndex++;
        updateCurrentInstruction(currentStepIndex);
        
        if (voiceEnabled) {
            announceStep(currentStepIndex);
        }
    }
    
    // Check if reached destination
    const destDistance = google.maps.geometry.spherical.computeDistanceBetween(
        new google.maps.LatLng(userPos.lat, userPos.lng),
        new google.maps.LatLng(destination.lat, destination.lng)
    );
    
    if (destDistance < 10) {
        arrivedAtDestination();
    }
}

function updateCurrentInstruction(stepIndex) {
    const step = routeSteps[stepIndex];
    const instructionDiv = document.getElementById('currentInstruction');
    
    instructionDiv.style.display = 'flex';
    document.getElementById('instructionTitle').innerHTML = step.instructions;
    document.getElementById('instructionDetails').textContent = `Step ${stepIndex + 1} of ${routeSteps.length}`;
}

function announceStep(stepIndex) {
    if (!voiceEnabled) return;
    
    const step = routeSteps[stepIndex];
    const instruction = step.instructions.replace(/<[^>]*>/g, ''); // Remove HTML tags
    speak(`${instruction}. Distance: ${step.distance.text}`);
}

function arrivedAtDestination() {
    isNavigating = false;
    
    if (voiceEnabled) {
        speak(`You have arrived at ${destination.name}`);
    }
    
    // Show completion dialog
    if (confirm(`You have arrived at ${destination.name}. Would you like to view details?`)) {
        window.location.href = `building-detail.php?id=<?php echo $buildingId; ?>`;
    } else {
        window.location.href = 'map.php';
    }
}

function toggleVoice() {
    voiceEnabled = !voiceEnabled;
    const btn = document.getElementById('voiceToggle');
    btn.querySelector('span').textContent = voiceEnabled ? 'Voice: ON' : 'Voice: OFF';
    
    if (voiceEnabled) {
        speak('Voice navigation enabled');
    }
    
    // Update user settings
    fetch('api/update-voice-setting.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ voice_enabled: voiceEnabled })
    });
}

function recenterMap() {
    if (userMarker) {
        map.setCenter(userMarker.getPosition());
        map.setZoom(18);
        
        if (voiceEnabled) {
            speak('Map recentered');
        }
    }
}

function endNavigation() {
    if (confirm('Are you sure you want to end navigation?')) {
        if (watchId) {
            navigator.geolocation.clearWatch(watchId);
        }
        window.location.href = 'map.php';
    }
}

function showOptions() {
    const options = ['Report Issue', 'Share Route', 'Avoid Stairs', 'Cancel'];
    const choice = prompt('Options:\n1. Report Issue\n2. Share Route\n3. Avoid Stairs\n4. Cancel\n\nEnter your choice (1-4):');
    
    switch(choice) {
        case '1':
            window.location.href = 'feedback.php';
            break;
        case '2':
            shareRoute();
            break;
        case '3':
            alert('Recalculating route to avoid stairs...');
            break;
    }
}

function shareRoute() {
    const url = window.location.href;
    if (navigator.share) {
        navigator.share({
            title: `Route to ${destination.name}`,
            url: url
        });
    } else {
        alert('Share URL: ' + url);
    }
}

function getETA(durationInSeconds) {
    const now = new Date();
    const eta = new Date(now.getTime() + durationInSeconds * 1000);
    return eta.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
}

window.addEventListener('load', initMap);

// Prevent accidental page unload during navigation
window.addEventListener('beforeunload', (e) => {
    if (isNavigating) {
        e.preventDefault();
        e.returnValue = '';
    }
});
</script>

<?php include 'includes/footer.php'; ?>
