<?php
require_once 'config/config.php';
require_once 'config/database.php';

requireLogin();

$database = new Database();
$db = $database->getConnection();

// Get emergency contacts
$query = "SELECT * FROM emergency_contacts ORDER BY type ASC";
$stmt = $db->prepare($query);
$stmt->execute();
$emergencyContacts = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = 'Help & Emergency';
$customCSS = 'help.css';
?>
<?php include 'includes/header.php'; ?>

<div class="page-container">
    <header class="top-bar">
        <button class="back-btn" onclick="window.history.back()">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </button>
        <h1>Help</h1>
        <div class="spacer"></div>
    </header>

    <div class="content">
        <div class="emergency-banner">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none">
                <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" stroke="white" stroke-width="2"/>
                <path d="M12 9v4M12 17h.01" stroke="white" stroke-width="2" stroke-linecap="round"/>
            </svg>
            <div class="banner-content">
                <h2>Emergency Assistance</h2>
                <p>If you are in danger, use SOS below or call campus security.</p>
            </div>
        </div>

        <div class="help-services">
            <?php foreach ($emergencyContacts as $contact): ?>
                <div class="service-card">
                    <div class="service-icon <?php echo $contact['type']; ?>">
                        <?php echo getEmergencyIcon($contact['type']); ?>
                    </div>
                    <h3><?php echo htmlspecialchars($contact['name']); ?></h3>
                    <p><?php echo htmlspecialchars($contact['description']); ?></p>
                    <button class="call-btn" onclick="callEmergency('<?php echo $contact['phone']; ?>', '<?php echo addslashes($contact['name']); ?>')">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                            <path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z" stroke="currentColor" stroke-width="2"/>
                        </svg>
                        Call
                    </button>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="facilities-map">
            <h3>Nearest Emergency Facilities</h3>
            <div class="map-placeholder" id="emergencyMap" style="height: 250px;"></div>
        </div>

        <button class="sos-btn" onclick="triggerSOS()">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" stroke="white" stroke-width="2"/>
                <path d="M12 9v4M12 17h.01" stroke="white" stroke-width="2" stroke-linecap="round"/>
            </svg>
            SOS - Share Location & Call
        </button>
    </div>

    <?php include 'includes/bottom-nav.php'; ?>
</div>

<script>
let emergencyMap;
const emergencyLocations = <?php echo json_encode($emergencyContacts); ?>;

function initEmergencyMap() {
    const campusCenter = { lat: 28.545000, lng: 77.192500 };
    
    emergencyMap = new google.maps.Map(document.getElementById('emergencyMap'), {
        center: campusCenter,
        zoom: 15,
        disableDefaultUI: true
    });

    // Add emergency facility markers
    emergencyLocations.forEach(location => {
        if (location.latitude && location.longitude) {
            new google.maps.Marker({
                position: {
                    lat: parseFloat(location.latitude),
                    lng: parseFloat(location.longitude)
                },
                map: emergencyMap,
                title: location.name,
                icon: {
                    path: google.maps.SymbolPath.CIRCLE,
                    scale: 10,
                    fillColor: '#E74C3C',
                    fillOpacity: 1,
                    strokeColor: '#ffffff',
                    strokeWeight: 2
                }
            });
        }
    });

    // Get user location
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(position => {
            const userPos = {
                lat: position.coords.latitude,
                lng: position.coords.longitude
            };
            
            new google.maps.Marker({
                position: userPos,
                map: emergencyMap,
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
            
            emergencyMap.setCenter(userPos);
        });
    }
}

function callEmergency(phone, serviceName) {
    if (confirm(`Call ${serviceName}?`)) {
        <?php if ($_SESSION['voice_navigation']): ?>
        speak(`Calling ${serviceName}`);
        <?php endif; ?>
        window.location.href = `tel:${phone}`;
    }
}

function triggerSOS() {
    if (!confirm('This will share your location and call emergency services. Continue?')) {
        return;
    }
    
    <?php if ($_SESSION['voice_navigation']): ?>
    speak('Emergency SOS activated. Sharing your location and calling campus security.');
    <?php endif; ?>
    
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(position => {
            const location = {
                lat: position.coords.latitude,
                lng: position.coords.longitude
            };
            
            // Send SOS alert
            fetch('api/send-sos.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    location: location,
                    timestamp: new Date().toISOString()
                })
            })
            .then(() => {
                // Call campus security
                window.location.href = 'tel:555-123-4567';
            });
        });
    } else {
        alert('Location services not available');
    }
}

window.addEventListener('load', initEmergencyMap);
</script>

<?php include 'includes/footer.php'; ?>

<?php
function getEmergencyIcon($type) {
    $icons = [
        'security' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" stroke="currentColor" stroke-width="2"/></svg>',
        'medical' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none"><path d="M19 3H5a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2V5a2 2 0 00-2-2z" stroke="currentColor" stroke-width="2"/><path d="M12 8v8M8 12h8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>',
        'helpline' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/><path d="M9.09 9a3 3 0 015.83 1c0 2-3 3-3 3M12 17h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>',
        'fire' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none"><path d="M12 2c1.5 3 2 6 2 9a6 6 0 11-12 0c0-3 .5-6 2-9 1 3 2 6 4 7 1-2 2-4 4-7z" stroke="currentColor" stroke-width="2"/></svg>',
        'admin' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none"><path d="M19 3H5a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2V5a2 2 0 00-2-2z" stroke="currentColor" stroke-width="2"/></svg>'
    ];
    return $icons[$type] ?? $icons['helpline'];
}
?>
