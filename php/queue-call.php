<?php
header('Content-Type: application/json');
require_once 'db.php';

$data = json_decode(file_get_contents("php://input"), true);
$dept = $data['department'] ?? '';

if (!$dept) {
  http_response_code(400);
  echo json_encode(["error" => "Missing department"]);
  exit;
}

// Get the current ticket in queue (NOT called yet)
$stmt = $mysqli->prepare("SELECT number FROM tickets WHERE type = ? AND status = 'waiting' ORDER BY created_at ASC LIMIT 1");
$stmt->bind_param("s", $dept);
$stmt->execute();
$stmt->bind_result($num);
$has = $stmt->fetch();
$stmt->close();

if (!$has) {
  echo json_encode(["error" => "No ticket in queue"]);
  exit;
}

// Save it to trigger TV display
$dir = __DIR__ . '/../tv-trigger';
if (!is_dir($dir)) {
  mkdir($dir, 0777, true);
}
file_put_contents("$dir/{$dept}.txt", $num);

echo json_encode(["success" => true, "ticket" => $num]);
