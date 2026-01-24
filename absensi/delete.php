<?php
require "../vendor/autoload.php";
include_once "../config/database.php";
include_once "../config/jwt.php";

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

header("Content-Type: application/json");

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
        echo json_encode(["message" => "Hanya admin"]);
        exit;
    }
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(["message" => "Token tidak valid"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->id)) {
    http_response_code(400);
    echo json_encode(["message" => "ID tidak ada"]);
    exit;
}

$db = new Database();
$conn = $db->connect();

$stmt = $conn->prepare("DELETE FROM absensi WHERE id = :id");
$stmt->bindParam(":id", $data->id);
$stmt->execute();

echo json_encode([
    "status" => true,
    "message" => "Data absensi berhasil dihapus"
]);
