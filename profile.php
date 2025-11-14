<?php
require 'vendor/autoload.php';
require 'db.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$secret_key = "MY_SECRET_KEY";
$headers = apache_request_headers();

if (!isset($headers['Authorization'])) {
    echo json_encode(["status" => false, "message" => "Token tidak ditemukan"]);
    exit;
}

$token = explode(" ", $headers['Authorization'])[1];

try {
    $decoded = JWT::decode($token, new Key($secret_key, 'HS256'));

    echo json_encode([
        "status" => true,
        "message" => "Token valid",
        "user" => $decoded->data
    ]);

} catch (Exception $e) {
    echo json_encode(["status" => false, "message" => "Token salah atau kadaluarsa"]);
}
?>
