<?php
require "../vendor/autoload.php";
include_once "../config/database.php";
include_once "../config/jwt.php";

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

header("Content-Type: application/json");

// ==========================
// CEK TOKEN
// ==========================
$headers = getallheaders();
if (!isset($headers['Authorization'])) {
    http_response_code(401);
    echo json_encode(["message" => "Token tidak ada"]);
    exit;
}

$token = str_replace("Bearer ", "", $headers['Authorization']);

try {
    $decoded = JWT::decode($token, new Key($secret_key, 'HS256'));
    if ($decoded->data->role !== 'siswa') {
        http_response_code(403);
        echo json_encode(["message" => "Hanya siswa"]);
        exit;
    }
    $siswa_id = $decoded->data->id;
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(["message" => "Token tidak valid"]);
    exit;
}

// ==========================
// PARAMETER BULAN & TAHUN
// ==========================
$bulan = $_GET['bulan'] ?? date('m');
$tahun = $_GET['tahun'] ?? date('Y');

// ==========================
// QUERY REKAP
// ==========================
$db = new Database();
$conn = $db->connect();

$query = "SELECT tanggal, status
          FROM absensi
          WHERE siswa_id = :siswa_id
          AND MONTH(tanggal) = :bulan
          AND YEAR(tanggal) = :tahun
          ORDER BY tanggal ASC";

$stmt = $conn->prepare($query);
$stmt->bindParam(":siswa_id", $siswa_id);
$stmt->bindParam(":bulan", $bulan);
$stmt->bindParam(":tahun", $tahun);
$stmt->execute();

$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ==========================
// HITUNG REKAP
// ==========================
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

// ==========================
// RESPONSE
// ==========================
echo json_encode([
    "bulan" => $bulan,
    "tahun" => $tahun,
    "total_hari" => count($data),
    "rekap" => $rekap,
    "detail" => $data
]);
