<?php
$mysqli = new mysqli("localhost", "root", "", "queue-system", 3307);

$result = $mysqli->query("
    SELECT cashier_id, type, number, called_at 
    FROM tickets 
    WHERE status = 'serving' 
    ORDER BY cashier_id ASC
");

$status = [];
while ($row = $result->fetch_assoc()) {
    $status[] = $row;
}

echo json_encode($status);
?>
