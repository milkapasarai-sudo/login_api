<?php
header("Content-Type: application/json");
require "koneksi.php";

// ambil header Authorization
$headers = function_exists('apache_request_headers') ? apache_request_headers() : [];

$authHeader = "";

if (isset($headers["Authorization"])) {
    $authHeader = $headers["Authorization"];
} elseif (isset($_SERVER["HTTP_AUTHORIZATION"])) {
    $authHeader = $_SERVER["HTTP_AUTHORIZATION"];
} elseif (isset($_SERVER["REDIRECT_HTTP_AUTHORIZATION"])) {
    $authHeader = $_SERVER["REDIRECT_HTTP_AUTHORIZATION"];
}

// jika Authorization tidak ditemukan
if (!$authHeader) {
    echo json_encode([
        "status" => "error",
        "message" => "Authorization header tidak ditemukan"
    ]);
    exit;
}

// pecah format "Bearer token"
$parts = explode(" ", $authHeader);

if (count($parts) !== 2 || strtolower($parts[0]) !== "bearer") {
    echo json_encode([
        "status" => "error",
        "message" => "Format token salah (Gunakan: Bearer <token>)"
    ]);
    exit;
}

$token = $parts[1];

// cek token di database
$stmt = $conn->prepare("SELECT * FROM tokens WHERE token = ?");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode([
        "status" => "error",
        "message" => "Token tidak valid"
    ]);
    exit;
}

echo json_encode([
    "status" => "success",
    "message" => "Token valid"
]);
