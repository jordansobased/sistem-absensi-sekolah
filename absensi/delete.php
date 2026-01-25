<?php
header("Content-Type: application/json");

include_once "../middleware/auth.php";
include_once "../config/database.php";

//cek role harus guru
if ($userData->role !== "guru") {
    http_response_code(403);
    echo json_encode(["message" => "Hanya guru"]);
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
