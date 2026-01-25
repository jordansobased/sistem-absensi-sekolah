<?php
header("Content-Type: application/json");
include_once "../middleware/auth.php";
include_once "../config/database.php";

//cek role harus admin
if ($userData->role !== "admin") {
    http_response_code(403);
    echo json_encode(["message" => "Hanya admin"]);
    exit;
}

//ambil body request
$data = json_decode(file_get_contents("php://input"));

//validasi id users
if (empty($data->id)) {
    http_response_code(400);
    echo json_encode(["message" => "ID user wajib diisi"]);
    exit;
}

//koneksi ke database
$db = new Database();
$conn = $db->connect();

//query delete 
$query = "DELETE FROM users WHERE id = :id";
$stmt = $conn->prepare($query);
$stmt->bindParam(":id", $data->id);
$stmt->execute();

echo json_encode([
    "status" => true,
    "message" => "User berhasil dihapus"
]);
