<?php
header("Content-Type: application/json");

include_once "../middleware/auth.php";
include_once "../config/database.php";

$allowedRoles = ["siswa", "guru", "admin"];

if (!in_array($userData->role, $allowedRoles)) {
    http_response_code(403);
    echo json_encode([
        "status" => false,
        "message" => "Anda tidak memiliki hak akses"
    ]);
    exit;
}

$db = new Database();
$conn = $db->connect();

if ($userData->role === "siswa") {
    $query = "SELECT 
                absensi.id,
                absensi.siswa_id,
                users.nama AS nama_siswa,
                absensi.guru_id,
                absensi.tanggal,
                absensi.status
              FROM absensi
              JOIN users ON absensi.siswa_id = users.id
              WHERE absensi.siswa_id = :id
              ORDER BY absensi.tanggal ASC";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(":id", $userData->id);
} else {
    $query = "SELECT 
                absensi.id,
                absensi.siswa_id,
                users.nama AS nama_siswa,
                absensi.guru_id,
                absensi.tanggal,
                absensi.status
              FROM absensi
              JOIN users ON absensi.siswa_id = users.id
              ORDER BY absensi.tanggal ASC";
    $stmt = $conn->prepare($query);
}

$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

http_response_code(200);
echo json_encode([
    "status" => true,
    "role" => $userData->role,
    "data" => $data
]);
