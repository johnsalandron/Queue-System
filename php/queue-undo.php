<?php
header('Content-Type: application/json');
require_once 'db.php';

$data = json_decode(file_get_contents("php://input"), true);
$fullTicket = $data['ticket_number'] ?? '';
$department = $data['department'] ?? '';

if (!$fullTicket || !$department) {
    http_response_code(400);
    echo json_encode(["error" => "Missing ticket number or department"]);
    exit;
}

// Normalize ticket format (e.g., S018 → S-018)
if (strpos($fullTicket, '-') === false && strlen($fullTicket) >= 2) {
    $fullTicket = substr($fullTicket, 0, 1) . '-' . substr($fullTicket, 1);
}

list($type, $number) = explode('-', $fullTicket);
$number = ltrim($number, '0'); // remove leading zeros if your DB doesn't store them

$stmt = $mysqli->prepare("
    UPDATE tickets 
    SET status = 'waiting', called_at = NULL 
    WHERE type = ? AND number = ?
");
$stmt->bind_param("si", $type, $number);
$stmt->execute();

if ($stmt->error) {
    echo json_encode(["success" => false, "error" => $stmt->error]);
    exit;
}

if ($stmt->affected_rows > 0) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Ticket not found or already waiting",
        "type" => $type,
        "number" => $number
    ]);
}
?>
