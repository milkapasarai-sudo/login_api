<?php
header("Content-Type: application/json");

require "koneksi.php";

$data = json_decode(file_get_contents("php://input"), true);

$email    = $data["email"] ?? "";
$password = $data["password"] ?? "";

/* AMBIL USER BERDASARKAN EMAIL */
$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode([
        "status" => "error",
        "message" => "Email tidak ditemukan"
    ]);
    exit;
}

$user = $result->fetch_assoc();

/* CEK PASSWORD */
if (!password_verify($password, $user["password_hash"])) {
    echo json_encode([
        "status" => "error",
        "message" => "Password salah"
    ]);
    exit;
}

/* BUAT TOKEN BARU */
$token = bin2hex(random_bytes(32));

/* HAPUS TOKEN LAMA */
$stmtDel = $conn->prepare("DELETE FROM tokens WHERE user_id = ?");
$stmtDel->bind_param("i", $user["id"]);
$stmtDel->execute();

/* SIMPAN TOKEN BARU */
$stmt2 = $conn->prepare("INSERT INTO tokens (user_id, token) VALUES (?, ?)");
$stmt2->bind_param("is", $user["id"], $token);
$stmt2->execute();

/* RESPONSE */
echo json_encode([
    "status" => "success",
    "message" => "Login berhasil",
    "token" => $token
]);
?>
