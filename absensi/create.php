<?php
header("Content-Type: application/json");

include_once "../middleware/auth.php";
include_once "../config/database.php";

//cek role 
if ($userData->role !== "guru") {
    http_response_code(403);
    echo json_encode(["message" => "Hanya guru yang bisa input absensi"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"));

//validasi sederhana
if (
    empty($data->siswa_id) ||
    empty($data->tanggal) ||
    empty($data->status)
) {
    http_response_code(400);
    echo json_encode(["message" => "Data tidak lengkap"]);
    exit;
}

$db = new Database();
$conn = $db->connect();

$query = "INSERT INTO absensi 
          (siswa_id, guru_id, tanggal, status)
          VALUES 
          (:siswa_id, :guru_id, :tanggal, :status)";

$stmt = $conn->prepare($query);

$stmt->bindParam(":siswa_id", $data->siswa_id);
$stmt->bindParam(":guru_id", $userData->id);
$stmt->bindParam(":tanggal", $data->tanggal);
$stmt->bindParam(":status", $data->status);

$stmt->execute();

echo json_encode([
    "status" => true,
    "message" => "Absensi berhasil disimpan"
]);

