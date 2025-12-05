<?php
require_once '../config/database.php';
require_once '../config/config.php';

header('Content-Type: application/json');
if (!isLoggedIn()) {
    http_response_code(401);
    exit(json_encode(['error' => 'Unauthorized']));
}

$db = (new Database())->getConnection();
$id = (int)($_GET['dept_id'] ?? 0);

$stmt = $db->prepare("SELECT * FROM labs WHERE department_id = :id ORDER BY name");
$stmt->execute([':id' => $id]);
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
