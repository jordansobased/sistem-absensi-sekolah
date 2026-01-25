<?php
header("Content-Type: application/json");

include_once "../middleware/auth.php";
include_once "../config/database.php";

//cek role siswa
if ($userData->role !== 'siswa') {
    http_response_code(403);
    echo json_encode(["message" => "Hanya siswa yang boleh upload izin"]);
    exit;
}

//validasi input
if (!isset($_POST['tanggal']) || !isset($_POST['keterangan']) || !isset($_FILES['file'])) {
    http_response_code(400);
    echo json_encode(["message" => "Data tidak lengkap"]);
    exit;
}

$siswa_id = $userData->id;
$tanggal = $_POST['tanggal'];
$keterangan = $_POST['keterangan'];
$file = $_FILES['file'];

//validasi ekstensi
$allowed = ['pdf', 'jpg', 'jpeg', 'png'];
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

if (!in_array($ext, $allowed)) {
    http_response_code(400);
    echo json_encode(["message" => "File harus PDF / JPG / PNG"]);
    exit;
}

//folder upload
$uploadDir = "../uploads/surat_izin/";
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$filename = time() . "_" . basename($file['name']);
$path = $uploadDir . $filename;

if (!move_uploaded_file($file['tmp_name'], $path)) {
    http_response_code(500);
    echo json_encode(["message" => "Gagal upload file"]);
    exit;
}

$db = new Database();
$conn = $db->connect();

//simpan izin
$stmt = $conn->prepare(
    "INSERT INTO izin (siswa_id, tanggal, keterangan, file)
     VALUES (:siswa_id, :tanggal, :keterangan, :file)"
);
$stmt->execute([
    ":siswa_id" => $siswa_id,
    ":tanggal" => $tanggal,
    ":keterangan" => $keterangan,
    ":file" => $filename
]);

$status_absen = strtolower($keterangan) === 'sakit' ? 'sakit' : 'izin';

//cek absensi
$cek = $conn->prepare(
    "SELECT id FROM absensi WHERE siswa_id = :siswa_id AND tanggal = :tanggal"
);
$cek->execute([
    ":siswa_id" => $siswa_id,
    ":tanggal" => $tanggal
]);

if ($cek->rowCount() > 0) {
    $conn->prepare(
        "UPDATE absensi SET status = :status
         WHERE siswa_id = :siswa_id AND tanggal = :tanggal"
    )->execute([
        ":status" => $status_absen,
        ":siswa_id" => $siswa_id,
        ":tanggal" => $tanggal
    ]);
} else {
    $conn->prepare(
        "INSERT INTO absensi (siswa_id, tanggal, status)
         VALUES (:siswa_id, :tanggal, :status)"
    )->execute([
        ":siswa_id" => $siswa_id,
        ":tanggal" => $tanggal,
        ":status" => $status_absen
    ]);
}

echo json_encode([
    "status" => true,
    "message" => "Surat izin berhasil diupload"
]);
