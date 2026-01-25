<?php
require "../vendor/autoload.php";
include_once "../config/database.php";
include_once "../config/jwt.php";

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

header("Content-Type: application/json");

//ambil header request
$headers = getallheaders();

//cek token ada atau tidak
if (!isset($headers['Authorization'])) {
    http_response_code(401);
    echo json_encode(["message" => "Token tidak ada"]);
    exit;
}

//ambil token dari header
$token = str_replace("Bearer ", "", $headers['Authorization']);

try {
    //decode token jwt
    $decoded = JWT::decode($token, new Key($secret_key, 'HS256'));

    //cek role harus guru
    if ($decoded->data->role !== 'guru') {
        http_response_code(403);
        echo json_encode(["message" => "Hanya guru"]);
        exit;
    }
} catch (Exception $e) {
    //token tidak valid
    http_response_code(401);
    echo json_encode(["message" => "Token tidak valid"]);
    exit;
}

//ambil body request
$data = json_decode(file_get_contents("php://input"));

//cek id absensi
if (!isset($data->id_absensi)) {
    http_response_code(400);
    echo json_encode(["message" => "ID tidak ada"]);
    exit;
}

//koneksi ke database
$db = new Database();
$conn = $db->connect();

//query hapus absensi
$stmt = $conn->prepare("DELETE FROM absensi WHERE id = :id");
$stmt->bindParam(":id", $data->id_absensi);
$stmt->execute();

echo json_encode([
    "status" => true,
    "message" => "Data absensi berhasil dihapus"
]);
