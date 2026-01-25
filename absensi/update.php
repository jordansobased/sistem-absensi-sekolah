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

//ambil data dari body request
$data = json_decode(file_get_contents("php://input"));

//validasi data wajib
if (
    empty($data->id_absensi) ||
    empty($data->status)
) {
    http_response_code(400);
    echo json_encode(["message" => "Data tidak lengkap"]);
    exit;
}

//validasi status 
$allowedStatus = ["hadir", "izin", "sakit", "alpha"];

if (!in_array($data->status, $allowedStatus)) {
    http_response_code(400);
    echo json_encode(["message" => "Status tidak valid"]);
    exit;
}

//koneksi ke database
$db = new Database();
$conn = $db->connect();

//query update status absensi
$query = "UPDATE absensi 
          SET status = :status
          WHERE id = :id";

$stmt = $conn->prepare($query);
$stmt->bindParam(":id", $data->id_absensi);
$stmt->bindParam(":status", $data->status);
$stmt->execute();

echo json_encode([
    "status" => true,
    "message" => "Absensi berhasil diperbarui"
]);
