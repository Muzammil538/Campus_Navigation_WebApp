<?php
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'models/Building.php';

requireLogin();

$database = new Database();
$db = $database->getConnection();
$buildingModel = new Building($db);

$category = isset($_GET['category']) ? sanitizeInput($_GET['category']) : null;

if ($category) {
    $buildings = $buildingModel->getByCategory($category);
    $pageTitle = ucfirst($category);
} else {
    $categories = $buildingModel->getCategoriesWithCount();
    $pageTitle = 'Choose Category';
}

$customCSS = 'categories.css';
?>
<?php include 'includes/header.php'; ?>

<div class="page-container">
    <header class="top-bar">
        <button class="back-btn" onclick="window.history.back()">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </button>
        <h1><?php echo $pageTitle; ?></h1>
        <div class="spacer"></div>
    </header>

    <?php if (!$category): ?>
        <div class="subtitle">
            <p>Select a category to browse buildings</p>
        </div>

        <div class="categories-grid">
            <?php foreach ($categories as $cat): ?>
                <a href="categories.php?category=<?php echo $cat['category']; ?>" class="category-card">
                    <div class="category-icon <?php echo $cat['category']; ?>">
                        <?php echo getCategoryIcon($cat['category']); ?>
                    </div>
                    <h3><?php echo ucfirst($cat['category']); ?></h3>
                    <span class="count"><?php echo $cat['count']; ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="content">
            <div class="locations-list">
                <?php foreach ($buildings as $building): ?>
                    <a href="building-detail.php?id=<?php echo $building['id']; ?>" class="location-item">
                        <svg class="building-icon" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" stroke="currentColor" stroke-width="2"/>
                        </svg>
                        <div class="location-info">
                            <h3><?php echo htmlspecialchars($building['name']); ?></h3>
                            <p><?php echo htmlspecialchars($building['code']); ?> • <?php echo $building['floors']; ?> floors</p>
                        </div>
                        <button class="navigate-btn" onclick="event.preventDefault(); navigateTo(<?php echo $building['id']; ?>)">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                                <path d="M9 18l6-6-6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                        </button>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <?php include 'includes/bottom-nav.php'; ?>
</div>

<script>
function navigateTo(buildingId) {
    window.location.href = `directions.php?building=${buildingId}`;
}
</script>

<?php include 'includes/footer.php'; ?>

<?php
function getCategoryIcon($category) {
    $icons = [
        'academic' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" stroke="currentColor" stroke-width="2"/></svg>',
        'library' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none"><path d="M4 19.5A2.5 2.5 0 016.5 17H20" stroke="currentColor" stroke-width="2"/></svg>',
        'laboratory' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/></svg>',
        'cafeteria' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none"><path d="M3 9h18v10a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" stroke="currentColor" stroke-width="2"/></svg>',
        'sports' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/></svg>',
        'administration' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none"><path d="M19 3H5a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2V5a2 2 0 00-2-2z" stroke="currentColor" stroke-width="2"/></svg>',
        'dormitory' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" stroke="currentColor" stroke-width="2"/></svg>',
        'parking' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" stroke="currentColor" stroke-width="2"/></svg>'
    ];
    return $icons[$category] ?? $icons['academic'];
}
?>
