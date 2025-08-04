<?php
header('Content-Type: application/json');
require_once 'db.php';

$data = json_decode(file_get_contents("php://input"), true);
$department = $data['department'] ?? '';

if (!$department) {
    http_response_code(400);
    echo json_encode(["error" => "Missing department"]);
    exit;
}

$mysqli->query("UPDATE tickets SET status = 'skipped' WHERE type = '$department' AND status = 'called'");

$stmt = $mysqli->prepare("SELECT id FROM tickets WHERE type = ? AND status = 'waiting' ORDER BY created_at ASC LIMIT 1");
$stmt->bind_param("s", $department);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $ticketId = $row['id'];
    $update = $mysqli->prepare("UPDATE tickets SET status = 'called', called_at = NOW() WHERE id = ?");
    $update->bind_param("i", $ticketId);
    $update->execute();
}

echo json_encode(["success" => true]);
?>
