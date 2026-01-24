<?php
header("Content-Type: application/json");

include_once "../middleware/auth.php";
include_once "../config/database.php";

/* CEK ROLE */
if ($userData->role !== "guru") {
    http_response_code(403);
    echo json_encode(["message" => "Hanya guru yang bisa melihat surat izin"]);
    exit;
}

$db = new Database();
$conn = $db->connect();

$query = "SELECT 
            izin.id,
            izin.tanggal,
            izin.keterangan,
            izin.file,
            izin.status,
            users.nama AS siswa
          FROM izin
          JOIN users ON izin.siswa_id = users.id
          ORDER BY izin.created_at DESC";

$stmt = $conn->prepare($query);
$stmt->execute();

$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    "status" => true,
    "data" => $data
]);
