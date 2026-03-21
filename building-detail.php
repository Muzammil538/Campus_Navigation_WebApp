<?php
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'models/Building.php';
require_once 'models/Bookmark.php';

requireLogin();

$buildingId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($buildingId <= 0) {
    redirect('search.php');
}

$database = new Database();
$db = $database->getConnection();
$buildingModel = new Building($db);
$bookmarkModel = new Bookmark($db);

$building = $buildingModel->getById($buildingId);

if (!$building) {
    redirect('search.php');
}

$facilities = $buildingModel->getFacilities($buildingId);
$isBookmarked = $bookmarkModel->isBookmarked($_SESSION['user_id'], $buildingId);
$operatingHours = json_decode($building['operating_hours'], true);

$pageTitle = $building['name'];
$customCSS = 'building-detail.css';
?>
<?php include 'includes/header.php'; ?>

<div class="page-container">
    <div class="image-header">
        <img src="<?php echo $building['image_url'] ?? 'images/placeholder-building.jpg'; ?>" alt="<?php echo htmlspecialchars($building['name']); ?>">
        <button class="back-btn" onclick="window.history.back()">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                <path d="M15 18L9 12L15 6" stroke="white" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </button>
        <button class="share-btn" onclick="shareBuilding()">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                <path d="M4 12v8a2 2 0 002 2h12a2 2 0 002-2v-8M16 6l-4-4-4 4M12 2v13" stroke="white" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </button>
    </div>

    <div class="content">
        <div class="location-header">
            <h1><?php echo htmlspecialchars($building['name']); ?></h1>
            <button class="favorite-btn <?php echo $isBookmarked ? 'active' : ''; ?>" onclick="toggleBookmark()" id="favoriteBtn">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="<?php echo $isBookmarked ? 'currentColor' : 'none'; ?>">
                    <path d="M19 21l-7-5-7 5V5a2 2 0 012-2h10a2 2 0 012 2v16z" stroke="currentColor" stroke-width="2"/>
                </svg>
            </button>
        </div>

        <div class="building-meta">
            <span class="category-badge"><?php echo ucfirst($building['category']); ?></span>
            <span class="code"><?php echo htmlspecialchars($building['code']); ?></span>
        </div>

        <div class="description">
            <p><?php echo htmlspecialchars($building['description']); ?></p>
        </div>

        <?php if ($operatingHours): ?>
            <div class="info-section">
                <h3>Operating Hours</h3>
                <div class="hours-list">
                    <?php foreach ($operatingHours as $day => $hours): ?>
                        <div class="hours-item">
                            <span class="day"><?php echo ucfirst($day); ?></span>
                            <span class="hours"><?php echo $hours; ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($facilities)): ?>
            <div class="facilities-section">
                <h3>Available Facilities</h3>
                <div class="facility-list">
                    <?php foreach ($facilities as $facility): ?>
                        <div class="facility-item">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                                <path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33" stroke="currentColor" stroke-width="2"/>
                            </svg>
                            <span><?php echo htmlspecialchars($facility['name']); ?></span>
                            <span class="floor-badge">Floor <?php echo $facility['floor']; ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="contact-section">
            <h3>Location</h3>
            <div class="contact-item">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z" stroke="currentColor" stroke-width="2"/>
                    <circle cx="12" cy="10" r="3" stroke="currentColor" stroke-width="2"/>
                </svg>
                <span><?php echo htmlspecialchars($building['address']); ?></span>
                <button class="action-btn" onclick="copyAddress()">Copy</button>
            </div>
            <div class="map-preview" id="detailMap" style="height: 200px; border-radius: 8px; margin-top: 10px;"></div>
        </div>

        <div class="action-section">
            <button class="btn-primary" onclick="navigateHere()">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                    <path d="M9 18l6-6-6-6" stroke="white" stroke-width="2" stroke-linecap="round"/>
                </svg>
                Navigate Here
            </button>
            <button class="btn-secondary" onclick="toggleBookmark()">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                    <path d="M19 21l-7-5-7 5V5a2 2 0 012-2h10a2 2 0 012 2v16z" stroke="currentColor" stroke-width="2"/>
                </svg>
                <?php echo $isBookmarked ? 'Remove from' : 'Add to'; ?> Favorites
            </button>
        </div>
    </div>

    <?php include 'includes/bottom-nav.php'; ?>
</div>

<script>
const buildingId = <?php echo $buildingId; ?>;
const buildingData = <?php echo json_encode($building); ?>;

// Initialize detail map
function initDetailMap() {
    const position = {
        lat: parseFloat(buildingData.latitude),
        lng: parseFloat(buildingData.longitude)
    };
    
    const map = new google.maps.Map(document.getElementById('detailMap'), {
        center: position,
        zoom: 17,
        disableDefaultUI: true
    });
    
    new google.maps.Marker({
        position: position,
        map: map,
        title: buildingData.name
    });
}

function toggleBookmark() {
    fetch('api/toggle-bookmark.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            building_id: buildingId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}

function navigateHere() {
    window.location.href = `directions.php?building=${buildingId}`;
}

function shareBuilding() {
    if (navigator.share) {
        navigator.share({
            title: buildingData.name,
            text: buildingData.description,
            url: window.location.href
        });
    } else {
        copyToClipboard(window.location.href);
        alert('Link copied to clipboard');
    }
}

function copyAddress() {
    copyToClipboard(buildingData.address);
    alert('Address copied to clipboard');
}

function copyToClipboard(text) {
    const textarea = document.createElement('textarea');
    textarea.value = text;
    document.body.appendChild(textarea);
    textarea.select();
    document.execCommand('copy');
    document.body.removeChild(textarea);
}

window.addEventListener('load', initDetailMap);

// Voice announcement
<?php if ($_SESSION['voice_navigation']): ?>
speak('<?php echo addslashes($building['name']); ?>. <?php echo addslashes($building['description']); ?>');
<?php endif; ?>
</script>

<?php include 'includes/footer.php'; ?>
