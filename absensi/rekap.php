<?php
require "../vendor/autoload.php";
include_once "../config/database.php";
include_once "../config/jwt.php";

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

header("Content-Type: application/json");

//ambil header request
$headers = getallheaders();

//cek token
if (!isset($headers['Authorization'])) {
    http_response_code(401);
    echo json_encode(["message" => "Token tidak ada"]);
    exit;
}

//ambil token
$token = str_replace("Bearer ", "", $headers['Authorization']);

try {
    //decode token
    $decoded = JWT::decode($token, new Key($secret_key, 'HS256'));

    //cek role
    if (!in_array($decoded->data->role, ["siswa", "guru", "admin"])) {
        http_response_code(403);
        echo json_encode(["message" => "Akses ditolak"]);
        exit;
    }

    //simpan data user
    $role = $decoded->data->role;
    $user_id = $decoded->data->id;

} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(["message" => "Token tidak valid"]);
    exit;
}

//koneksi database
$db = new Database();
$conn = $db->connect();

//query untuk siswa
if ($role === "siswa") {
    $query = "SELECT 
                absensi.id,
                absensi.tanggal,
                absensi.status,
                users.nama AS nama_siswa
              FROM absensi
              JOIN users ON absensi.siswa_id = users.id
              WHERE absensi.siswa_id = :id
              ORDER BY absensi.tanggal ASC";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(":id", $user_id);

//query untuk guru dan admin
} else {
    $query = "SELECT 
                absensi.id,
                absensi.siswa_id,
                absensi.tanggal,
                absensi.status,
                users.nama AS nama_siswa
              FROM absensi
              JOIN users ON absensi.siswa_id = users.id
              ORDER BY users.nama, absensi.tanggal ASC";

    $stmt = $conn->prepare($query);
}

//eksekusi query
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

//inisialisasi hasil
$result = [];

//olah data absensi
foreach ($data as $row) {

    //tentukan siswa
    $sid = $row['siswa_id'] ?? $user_id;

    //buat struktur awal siswa
    if (!isset($result[$sid])) {
        $result[$sid] = [
            "siswa_id" => $sid,
            "nama" => $row['nama_siswa'],
            "rekap" => [
                "hadir" => 0,
                "izin" => 0,
                "sakit" => 0,
                "alpha" => 0
            ],
            "detail" => []
        ];
    }

    //hitung rekap status
    if (isset($result[$sid]['rekap'][$row['status']])) {
        $result[$sid]['rekap'][$row['status']]++;
    }

    //simpan detail absensi
    $result[$sid]['detail'][] = [
        "id" => $row['id'],
        "tanggal" => $row['tanggal'],
        "status" => $row['status']
    ];
}


echo json_encode([
    "status" => true,
    "role" => $role,
    "data" => array_values($result)
]);
