<?php
require "cek_token.php";

echo json_encode([
    "status" => "success",
    "message" => "Token valid. Selamat datang!",
    "data" => [
        "user_id" => $token_data["user_id"]
    ]
]);
?>
