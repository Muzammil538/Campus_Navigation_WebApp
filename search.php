<?php
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'models/Building.php';

requireLogin();

$database = new Database();
$db = $database->getConnection();
$buildingModel = new Building($db);

$searchResults = [];
$searchTerm = '';

if (isset($_GET['q']) && !empty($_GET['q'])) {
    $searchTerm = sanitizeInput($_GET['q']);
    $searchResults = $buildingModel->search($searchTerm);
}

$categories = $buildingModel->getCategoriesWithCount();
$recentSearches = $_SESSION['recent_searches'] ?? [];

$pageTitle = 'Search';
$customCSS = 'search.css';
?>
<?php include 'includes/header.php'; ?>

<div class="page-container">
    <header class="search-header">
        <form class="search-bar" method="GET" action="search.php">
            <svg class="search-icon" width="20" height="20" viewBox="0 0 24 24" fill="none">
                <circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2"/>
                <path d="M21 21l-4.35-4.35" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
            <input 
                type="text" 
                name="q" 
                placeholder="Search buildings, rooms, services" 
                value="<?php echo htmlspecialchars($searchTerm); ?>"
                id="searchInput"
                autocomplete="off"
            >
            <button type="button" class="voice-search-btn" onclick="startVoiceSearch()" aria-label="Voice search">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                    <path d="M12 1a3 3 0 013 3v8a3 3 0 11-6 0V4a3 3 0 013-3z" stroke="currentColor" stroke-width="2"/>
                    <path d="M19 10v2a7 7 0 01-14 0v-2M12 19v4M8 23h8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </button>
        </form>
    </header>

    <div class="content">
        <?php if (!empty($searchTerm)): ?>
            <!-- Search Results -->
            <div class="section">
                <h2>Search Results for "<?php echo htmlspecialchars($searchTerm); ?>"</h2>
                <?php if (empty($searchResults)): ?>
                    <p class="no-results">No buildings found matching your search.</p>
                <?php else: ?>
                    <div class="locations-list">
                        <?php foreach ($searchResults as $building): ?>
                            <a href="building-detail.php?id=<?php echo $building['id']; ?>" class="location-item">
                                <svg class="building-icon" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" stroke="currentColor" stroke-width="2"/>
                                </svg>
                                <div class="location-info">
                                    <h3><?php echo htmlspecialchars($building['name']); ?></h3>
                                    <p><?php echo htmlspecialchars($building['code']); ?> • <?php echo ucfirst($building['category']); ?></p>
                                </div>
                                <button class="navigate-btn" onclick="event.preventDefault(); navigateTo(<?php echo $building['id']; ?>)">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                                        <path d="M9 18l6-6-6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    </svg>
                                </button>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <!-- Recent Searches -->
            <?php if (!empty($recentSearches)): ?>
                <div class="section">
                    <div class="section-header">
                        <h2>Recent searches</h2>
                        <button onclick="clearRecentSearches()">Clear</button>
                    </div>
                    <div class="recent-searches">
                        <?php foreach ($recentSearches as $search): ?>
                            <div class="search-item">
                                <svg class="clock-icon" width="20" height="20" viewBox="0 0 24 24" fill="none">
                                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                                    <path d="M12 6v6l4 2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                                <a href="search.php?q=<?php echo urlencode($search); ?>">
                                    <?php echo htmlspecialchars($search); ?>
                                </a>
                                <button class="remove-btn" onclick="removeRecentSearch('<?php echo htmlspecialchars($search); ?>')">&times;</button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Categories -->
            <div class="section">
                <h2>Categories</h2>
                <div class="categories">
                    <?php foreach ($categories as $cat): ?>
                        <a href="categories.php?category=<?php echo $cat['category']; ?>" class="category-chip">
                            <?php echo getCategoryIcon($cat['category']); ?>
                            <?php echo ucfirst($cat['category']); ?>
                            <span class="count"><?php echo $cat['count']; ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Popular Locations -->
            <div class="section">
                <h2>Popular locations</h2>
                <div class="locations-list">
                    <?php
                    $popularBuildings = array_slice($buildingModel->getAll(), 0, 5);
                    foreach ($popularBuildings as $building):
                    ?>
                        <a href="building-detail.php?id=<?php echo $building['id']; ?>" class="location-item">
                            <svg class="building-icon" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" stroke="currentColor" stroke-width="2"/>
                            </svg>
                            <div class="location-info">
                                <h3><?php echo htmlspecialchars($building['name']); ?></h3>
                                <p><?php echo htmlspecialchars($building['address']); ?></p>
                            </div>
                            <span class="distance">0.<?php echo rand(2, 9); ?> mi</span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php include 'includes/bottom-nav.php'; ?>
</div>

<script>
function startVoiceSearch() {
    if (!recognition) {
        alert('Voice search is not supported in your browser');
        return;
    }
    
    speak('Listening for your search query');
    recognition.start();
    
    recognition.onresult = function(event) {
        const query = event.results[0][0].transcript;
        document.getElementById('searchInput').value = query;
        speak('Searching for ' + query);
        document.querySelector('.search-bar').submit();
    };
}

function navigateTo(buildingId) {
    window.location.href = `map.php?navigate=${buildingId}`;
}

function clearRecentSearches() {
    fetch('api/clear-recent-searches.php', {
        method: 'POST'
    })
    .then(() => {
        location.reload();
    });
}

function removeRecentSearch(search) {
    fetch('api/remove-recent-search.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ search: search })
    })
    .then(() => {
        location.reload();
    });
}

// Save search term to recent searches
<?php if (!empty($searchTerm)): ?>
    fetch('api/save-recent-search.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ search: '<?php echo addslashes($searchTerm); ?>' })
    });
<?php endif; ?>
</script>

<?php include 'includes/footer.php'; ?>

<?php
function getCategoryIcon($category) {
    $icons = [
        'academic' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" stroke="currentColor" stroke-width="2"/></svg>',
        'library' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M4 19.5A2.5 2.5 0 016.5 17H20" stroke="currentColor" stroke-width="2"/></svg>',
        'laboratory' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/></svg>',
        'cafeteria' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M3 9h18v10a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" stroke="currentColor" stroke-width="2"/></svg>',
        'sports' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/></svg>',
        'administration' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M19 3H5a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2V5a2 2 0 00-2-2z" stroke="currentColor" stroke-width="2"/></svg>',
        'dormitory' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" stroke="currentColor" stroke-width="2"/></svg>',
        'parking' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" stroke="currentColor" stroke-width="2"/></svg>'
    ];
    return $icons[$category] ?? $icons['academic'];
}
?>
