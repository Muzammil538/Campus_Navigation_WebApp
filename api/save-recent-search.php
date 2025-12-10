<?php
require_once '../config/config.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$search = $data['search'] ?? '';

if (empty($search)) {
    echo json_encode(['success' => false, 'message' => 'Empty search term']);
    exit;
}

// Get existing recent searches
$recentSearches = $_SESSION['recent_searches'] ?? [];

// Remove if already exists
$recentSearches = array_filter($recentSearches, function($s) use ($search) {
    return $s !== $search;
});

// Add to beginning
array_unshift($recentSearches, $search);

// Keep only last 10
$recentSearches = array_slice($recentSearches, 0, 10);

$_SESSION['recent_searches'] = $recentSearches;

echo json_encode(['success' => true, 'message' => 'Search saved']);
?>
