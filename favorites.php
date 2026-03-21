<?php
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'models/Bookmark.php';

requireLogin();

$database = new Database();
$db = $database->getConnection();
$bookmarkModel = new Bookmark($db);

$bookmarks = $bookmarkModel->getUserBookmarks($_SESSION['user_id']);
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

if ($filter !== 'all') {
    $bookmarks = array_filter($bookmarks, function($b) use ($filter) {
        return $b['category'] === $filter;
    });
}

$pageTitle = 'My Bookmarks';
$customCSS = 'favorites.css';
?>
<?php include 'includes/header.php'; ?>

<div class="page-container">
    <header class="top-bar">
        <h1>My Bookmarks</h1>
        <button class="filter-btn" onclick="showFilterMenu()">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                <path d="M3 6h18M8 12h8M11 18h2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </button>
    </header>

    <div class="tabs">
        <a href="?filter=all" class="tab <?php echo $filter === 'all' ? 'active' : ''; ?>">All</a>
        <a href="?filter=academic" class="tab <?php echo $filter === 'academic' ? 'active' : ''; ?>">Buildings</a>
        <a href="?filter=cafeteria" class="tab <?php echo $filter === 'cafeteria' ? 'active' : ''; ?>">Facilities</a>
    </div>

    <div class="content">
        <?php if (empty($bookmarks)): ?>
            <div class="empty-state">
                <svg width="80" height="80" viewBox="0 0 24 24" fill="none">
                    <path d="M19 21l-7-5-7 5V5a2 2 0 012-2h10a2 2 0 012 2v16z" stroke="#ccc" stroke-width="2"/>
                </svg>
                <h3>No bookmarks yet</h3>
                <p>Save your favorite locations for quick access</p>
                <a href="search.php" class="btn-primary">Explore Campus</a>
            </div>
        <?php else: ?>
            <div class="bookmarks-grid">
                <?php foreach ($bookmarks as $bookmark): ?>
                    <div class="bookmark-card" data-type="<?php echo $bookmark['category']; ?>">
                        <div class="card-image">
                            <img src="<?php echo $bookmark['image_url'] ?? 'images/placeholder-building.jpg'; ?>" alt="<?php echo htmlspecialchars($bookmark['name']); ?>">
                            <button class="favorite-btn active" onclick="removeBookmark(<?php echo $bookmark['id']; ?>)">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="#E74C3C">
                                    <path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/>
                                </svg>
                            </button>
                        </div>
                        <div class="card-content" onclick="window.location.href='building-detail.php?id=<?php echo $bookmark['id']; ?>'">
                            <h3><?php echo htmlspecialchars($bookmark['name']); ?></h3>
                            <div class="card-meta">
                                <span class="type-badge"><?php echo ucfirst($bookmark['category']); ?></span>
                                <span class="distance"><?php echo $bookmark['code']; ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php include 'includes/bottom-nav.php'; ?>
</div>

<script>
function removeBookmark(buildingId) {
    if (!confirm('Remove this bookmark?')) return;
    
    fetch('api/toggle-bookmark.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ building_id: buildingId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}

function showFilterMenu() {
    const filters = ['All', 'Academic Buildings', 'Cafeteria', 'Libraries', 'Sports'];
    const choice = prompt('Filter by:\n1. All\n2. Academic Buildings\n3. Cafeteria\n4. Libraries\n5. Sports\n\nEnter your choice:');
    
    const filterMap = {
        '1': 'all',
        '2': 'academic',
        '3': 'cafeteria',
        '4': 'library',
        '5': 'sports'
    };
    
    if (filterMap[choice]) {
        window.location.href = `?filter=${filterMap[choice]}`;
    }
}

<?php if ($_SESSION['voice_navigation']): ?>
speak('You have <?php echo count($bookmarks); ?> saved bookmarks');
<?php endif; ?>
</script>

<?php include 'includes/footer.php'; ?>
