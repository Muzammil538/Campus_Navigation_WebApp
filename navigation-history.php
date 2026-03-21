<?php
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'models/User.php';
requireLogin();
$database = new Database();
$db = $database->getConnection();
$userId = $_SESSION['user_id'];
$pageTitle = 'Navigation History';
$customCSS = 'settings.css';
// Fetch navigation history
$query = "SELECT nh.*, b.name AS building_name FROM navigation_history nh JOIN buildings b ON nh.to_building_id = b.id WHERE nh.user_id = :user_id ORDER BY nh.id DESC LIMIT 20";
$stmt = $db->prepare($query);
$stmt->bindParam(':user_id', $userId);
$stmt->execute();
$history = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include 'includes/header.php'; ?>
<div class="page-container">
    <header class="top-bar">
        <button class="back-btn" onclick="window.history.back()">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </button>
        <h1>Navigation History</h1>
        <div class="spacer"></div>
    </header>
    <div class="content">
        <div class="settings-list">
            <?php if (count($history) === 0): ?>
                <p>No navigation history found.</p>
            <?php else: ?>
                <?php foreach ($history as $item): ?>
                    <div class="settings-item">
                        <div class="item-content">
                            <h3><?php echo htmlspecialchars($item['building_name']); ?></h3>
                            <p>Distance: <?php echo htmlspecialchars($item['distance']); ?>m, Duration: <?php echo htmlspecialchars($item['duration']); ?>s</p>
                            <span class="date"><?php echo date('d M Y, H:i', strtotime($item['created_at'] ?? 'now')); ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <?php include 'includes/bottom-nav.php'; ?>
</div>
<?php include 'includes/footer.php'; ?>
