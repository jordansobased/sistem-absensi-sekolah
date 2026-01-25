<?php
require "../vendor/autoload.php";
include_once "../config/database.php";
include_once "../middleware/auth.php"; 

header("Content-Type: application/json");

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

    //struktur siswa
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

    //hitung rekap
    if (isset($result[$sid]['rekap'][$row['status']])) {
        $result[$sid]['rekap'][$row['status']]++;
    }

    //detail absensi
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
