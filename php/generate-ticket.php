<?php
$host = 'localhost';
$port = 3307;
$db = 'queue-system';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db, $port);
if ($conn->connect_error) {
  http_response_code(500);
  echo json_encode(['error' => 'Database connection failed']);
  exit;
}

$type = $_GET['type'] ?? null;
$validTypes = ['S', 'CI', 'FC', 'OC'];
if (!in_array($type, $validTypes)) {
  http_response_code(400);
  echo json_encode(['error' => 'Invalid ticket type']);
  exit;
}

// Reset daily
$today = date('Y-m-d');
$stmt = $conn->prepare("SELECT MAX(number) as max_num FROM tickets WHERE type = ? AND DATE(created_at) = ?");
$stmt->bind_param("ss", $type, $today);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();
$nextNum = (int)$result['max_num'] + 1;

$insert = $conn->prepare("INSERT INTO tickets (type, number) VALUES (?, ?)");
$insert->bind_param("si", $type, $nextNum);
$insert->execute();

$ticketCode = $type . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
echo json_encode(['ticket' => $ticketCode, 'date' => date('d/m/Y')]);
?>
