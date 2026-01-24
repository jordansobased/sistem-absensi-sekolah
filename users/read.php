<?php
header("Content-Type: application/json");
include_once "../config/database.php";
include_once "../config/jwt.php";
require "../vendor/autoload.php";

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

/* ========== CEK TOKEN ========== */
$headers = getallheaders();

if (!isset($headers['Authorization'])) {
    http_response_code(401);
    echo json_encode(["message" => "Token tidak ada"]);
    exit;
}

$token = str_replace("Bearer ", "", $headers['Authorization']);

try {
    $decoded = JWT::decode($token, new Key($secret_key, 'HS256'));

    if ($decoded->data->role !== 'admin') {
        http_response_code(403);
        echo json_encode(["message" => "Akses ditolak"]);
        exit;
    }

} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(["message" => "Token tidak valid"]);
    exit;
}

/* ========== AMBIL DATA USERS ========== */
$db = new Database();
$conn = $db->connect();

$query = "SELECT id, nama, email, role, created_at FROM users";
$stmt = $conn->prepare($query);
$stmt->execute();

$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    "status" => true,
    "data" => $data
]);
