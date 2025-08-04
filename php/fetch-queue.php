<?php
$mysqli = new mysqli("localhost", "root", "", "queue-system", 3307);

$result = $mysqli->query("SELECT * FROM tickets WHERE status = 'waiting' ORDER BY id ASC LIMIT 10");

$tickets = [];
while ($row = $result->fetch_assoc()) {
    $tickets[] = $row;
}

echo json_encode($tickets);
?>
