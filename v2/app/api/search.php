<?php
require_once '../config/database.php';
require_once '../config/config.php';

header('Content-Type: application/json');
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$db = (new Database())->getConnection();
$q = $_GET['q'] ?? '';

$stmt = $db->prepare("SELECT * FROM departments WHERE name LIKE :q OR code LIKE :q ORDER BY name LIMIT 10");
$stmt->execute([':q' => "%{$q}%"]);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($results);
?>
