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
    if (!in_array($decoded->data->role, ["siswa", "guru", "admin"])) {
        http_response_code(403);
        echo json_encode(["message" => "Akses ditolak"]);
        exit;
    }
    $role = $decoded->data->role;
    $user_id = $decoded->data->id;
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(["message" => "Token tidak valid"]);
    exit;
}

if ($role === "siswa") {
    $siswa_id = $user_id;
} else {
    if (!isset($_GET['siswa_id'])) {
        http_response_code(400);
        echo json_encode(["message" => "siswa_id wajib diisi"]);
        exit;
    }
    $siswa_id = $_GET['siswa_id'];
}

$db = new Database();
$conn = $db->connect();

$query = "SELECT 
            absensi.tanggal,
            absensi.status,
            users.nama AS nama_siswa
          FROM absensi
          JOIN users ON absensi.siswa_id = users.id
          WHERE absensi.siswa_id = :siswa_id
          ORDER BY absensi.tanggal ASC";

$stmt = $conn->prepare($query);
$stmt->bindParam(":siswa_id", $siswa_id);
$stmt->execute();

$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

$rekap = [
    "hadir" => 0,
    "izin" => 0,
    "sakit" => 0,
    "alpha" => 0
];

foreach ($data as $row) {
    if (isset($rekap[$row['status']])) {
        $rekap[$row['status']]++;
    }
}

echo json_encode([
    "status" => true,
    "role" => $role,
    "siswa_id" => $siswa_id,
    "nama_siswa" => $data[0]['nama_siswa'] ?? null,
    "total_hari" => count($data),
    "rekap" => $rekap,
    "detail" => $data
]);
