<?php
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>

<nav class="bottom-nav">
    <a href="map.php" class="nav-item <?php echo $currentPage === 'map' ? 'active' : ''; ?>">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
            <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" stroke="currentColor" stroke-width="2"/>
        </svg>
        <span>Map</span>
    </a>
    <a href="search.php" class="nav-item <?php echo $currentPage === 'search' ? 'active' : ''; ?>">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
            <circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2"/>
            <path d="M21 21l-4.35-4.35" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>
        <span>Search</span>
    </a>
    <a href="favorites.php" class="nav-item <?php echo $currentPage === 'favorites' ? 'active' : ''; ?>">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
            <path d="M19 21l-7-5-7 5V5a2 2 0 012-2h10a2 2 0 012 2v16z" stroke="currentColor" stroke-width="2"/>
        </svg>
        <span>Favorites</span>
    </a>
    <a href="settings.php" class="nav-item <?php echo $currentPage === 'settings' ? 'active' : ''; ?>">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
            <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/>
            <path d="M12 1v6m0 6v6M1 12h6m6 0h6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>
        <span>Profile</span>
    </a>
</nav>

<style>
.bottom-nav {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    max-width: 480px;
    margin: 0 auto;
    background: white;
    border-top: 1px solid #e0e0e0;
    display: flex;
    justify-content: space-around;
    padding: 8px 0 max(8px, env(safe-area-inset-bottom));
    z-index: 1000;
}

.nav-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-decoration: none;
    color: #999;
    gap: 4px;
    padding: 4px 16px;
    transition: color 0.2s;
}

.nav-item svg {
    stroke: currentColor;
}

.nav-item span {
    font-size: 12px;
}

.nav-item.active {
    color: #2196F3;
}
</style>
