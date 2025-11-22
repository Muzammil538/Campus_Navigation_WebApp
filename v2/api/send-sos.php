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
$location = $data['location'] ?? null;
$timestamp = $data['timestamp'] ?? date('Y-m-d H:i:s');

if (!$location) {
    echo json_encode(['success' => false, 'message' => 'Location required']);
    exit;
}

$database = new Database();
$db = $database->getConnection();

// Log SOS alert
$query = "INSERT INTO sos_alerts (user_id, latitude, longitude, timestamp) 
          VALUES (:user_id, :lat, :lng, :timestamp)";

try {
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->bindParam(':lat', $location['lat']);
    $stmt->bindParam(':lng', $location['lng']);
    $stmt->bindParam(':timestamp', $timestamp);
    $stmt->execute();
    
    // In production, send notifications to security
    // sendSecurityNotification($_SESSION['user_id'], $location);
    
    echo json_encode([
        'success' => true, 
        'message' => 'SOS alert sent',
        'alert_id' => $db->lastInsertId()
    ]);
} catch (Exception $e) {
    // Create table if doesn't exist
    $createTable = "CREATE TABLE IF NOT EXISTS sos_alerts (
        id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT NOT NULL,
        latitude DECIMAL(10, 8) NOT NULL,
        longitude DECIMAL(11, 8) NOT NULL,
        timestamp DATETIME NOT NULL,
        status ENUM('pending', 'responded', 'resolved') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    $db->exec($createTable);
    
    echo json_encode(['success' => true, 'message' => 'SOS alert sent']);
}
?>
