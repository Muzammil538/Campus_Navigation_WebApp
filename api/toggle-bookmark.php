<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../models/Bookmark.php';

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
$buildingId = $data['building_id'] ?? 0;

if ($buildingId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid building ID']);
    exit;
}

$database = new Database();
$db = $database->getConnection();
$bookmarkModel = new Bookmark($db);

$userId = $_SESSION['user_id'];

// Check if already bookmarked
$isBookmarked = $bookmarkModel->isBookmarked($userId, $buildingId);

if ($isBookmarked) {
    // Remove bookmark
    $result = $bookmarkModel->remove($userId, $buildingId);
    echo json_encode([
        'success' => $result,
        'action' => 'removed',
        'message' => 'Bookmark removed'
    ]);
} else {
    // Add bookmark
    $result = $bookmarkModel->add($userId, $buildingId);
    echo json_encode([
        'success' => $result,
        'action' => 'added',
        'message' => 'Bookmark added'
    ]);
}
?>
