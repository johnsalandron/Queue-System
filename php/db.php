<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "queue-system";
$port = 3307;

$mysqli = new mysqli($host, $user, $password, $dbname, $port);
if ($mysqli->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed: " . $mysqli->connect_error]);
    exit;
}
?>
