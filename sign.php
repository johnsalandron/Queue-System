<?php
$data = json_decode(file_get_contents("php://input"), true)['data'];

$privateKey = openssl_pkey_get_private(file_get_contents(__DIR__ . '/private-key.pem'));

openssl_sign($data, $signature, $privateKey, OPENSSL_ALGO_SHA1);

echo base64_encode($signature);
