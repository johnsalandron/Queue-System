<?php
header('Content-Type: application/json');
require_once 'db.php';

$department = $_GET['department'] ?? '';

$stmt = $mysqli->prepare("SELECT type, number, status FROM tickets WHERE type = ? AND status = 'waiting' ORDER BY created_at ASC");
$stmt->bind_param("s", $department);
$stmt->execute();

$result = $stmt->get_result();
$tickets = [];

while ($row = $result->fetch_assoc()) {
    // Combine type and padded number, e.g. "CI001"
    $ticket_number = $row['type'] . str_pad($row['number'], 3, '0', STR_PAD_LEFT);
    $tickets[] = [
        "ticket_number" => $ticket_number,
        "status" => $row['status']
    ];
}

echo json_encode($tickets);
?>
