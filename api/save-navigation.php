<?php
require_once '../config/config.php';
require_once '../config/database.php';

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

$userId = $_SESSION['user_id'];
$buildingId = $data['building_id'] ?? 0;
$distance = $data['distance'] ?? 0;
$duration = $data['duration'] ?? 0;

if ($buildingId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid building ID']);
    exit;
}

$database = new Database();
$db = $database->getConnection();

$query = "INSERT INTO navigation_history 
          (user_id, to_building_id, distance, duration) 
          VALUES (:user_id, :building_id, :distance, :duration)";

$stmt = $db->prepare($query);
$stmt->bindParam(':user_id', $userId);
$stmt->bindParam(':building_id', $buildingId);
$stmt->bindParam(':distance', $distance);
$stmt->bindParam(':duration', $duration);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Navigation saved']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to save navigation']);
}
?>
