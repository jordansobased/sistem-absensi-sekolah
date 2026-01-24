<?php
// 1️⃣ LOAD DEPENDENCY
require "../vendor/autoload.php";
include_once "../config/database.php";
include_once "../config/jwt.php";

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// 2️⃣ HEADER DASAR
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

// 3️⃣ AMBIL HEADER AUTHORIZATION (WAJIB DI ATAS)
$headers = getallheaders();

if (!isset($headers['Authorization'])) {
    http_response_code(401);
    echo json_encode(["message" => "Token tidak ada"]);
    exit;
}

$authHeader = $headers['Authorization'];
$token = str_replace("Bearer ", "", $authHeader);

// 4️⃣ VALIDASI TOKEN
try {
    $decoded = JWT::decode($token, new Key($secret_key, 'HS256'));

    $siswa_id = $decoded->data->id;
    $role = $decoded->data->role;

    if ($role !== 'siswa') {
        http_response_code(403);
        echo json_encode(["message" => "Hanya siswa yang boleh upload izin"]);
        exit;
    }
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(["message" => "Token tidak valid"]);
    exit;
}

// 5️⃣ VALIDASI FORM-DATA
if (!isset($_POST['tanggal']) || !isset($_POST['keterangan']) || !isset($_FILES['file'])) {
    http_response_code(400);
    echo json_encode(["message" => "Data tidak lengkap"]);
    exit;
}

// 6️⃣ AMBIL DATA
$tanggal = $_POST['tanggal'];
$keterangan = $_POST['keterangan'];
$file = $_FILES['file'];

// 7️⃣ VALIDASI FILE
$allowed = ['pdf', 'jpg', 'jpeg', 'png'];
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

if (!in_array($ext, $allowed)) {
    http_response_code(400);
    echo json_encode(["message" => "File harus PDF / JPG / PNG"]);
    exit;
}

// 8️⃣ UPLOAD FILE
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

// 9️⃣ SIMPAN DATABASE
$db = new Database();
$conn = $db->connect();

$query = "INSERT INTO izin (siswa_id, tanggal, keterangan, file)
          VALUES (:siswa_id, :tanggal, :keterangan, :file)";
$stmt = $conn->prepare($query);
$stmt->bindParam(":siswa_id", $siswa_id);
$stmt->bindParam(":tanggal", $tanggal);
$stmt->bindParam(":keterangan", $keterangan);
$stmt->bindParam(":file", $filename);
$stmt->execute();
$status_absen = strtolower($keterangan) === 'sakit' ? 'sakit' : 'izin';

// cek apakah absensi sudah ada
$cek = $conn->prepare("SELECT id FROM absensi WHERE siswa_id = :siswa_id AND tanggal = :tanggal");
$cek->bindParam(":siswa_id", $siswa_id);
$cek->bindParam(":tanggal", $tanggal);
$cek->execute();

if ($cek->rowCount() > 0) {
    // update jika sudah ada
    $update = $conn->prepare("UPDATE absensi 
                              SET status = :status 
                              WHERE siswa_id = :siswa_id AND tanggal = :tanggal");
    $update->bindParam(":status", $status_absen);
    $update->bindParam(":siswa_id", $siswa_id);
    $update->bindParam(":tanggal", $tanggal);
    $update->execute();
} else {
    // insert jika belum ada
    $insert = $conn->prepare("INSERT INTO absensi (siswa_id, tanggal, status)
                              VALUES (:siswa_id, :tanggal, :status)");
    $insert->bindParam(":siswa_id", $siswa_id);
    $insert->bindParam(":tanggal", $tanggal);
    $insert->bindParam(":status", $status_absen);
    $insert->execute();
}

// 🔟 RESPONSE SUKSES
echo json_encode([
    "status" => true,
    "message" => "Surat izin berhasil diupload"
]);
