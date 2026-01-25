<?php
header("Content-Type: application/json"); 

include_once "../middleware/auth.php";     
include_once "../config/database.php";     

//cek role
if ($userData->role !== "guru") {
    http_response_code(403);
    echo json_encode(["message" => "Hanya guru"]);
    exit;
}

//ambil data dari body request
$data = json_decode(file_get_contents("php://input"));

// validasi data wajib
if (
    empty($data->siswa_id) ||
    empty($data->tanggal) ||
    empty($data->status)
) {
    http_response_code(400);
    echo json_encode(["message" => "Data tidak lengkap"]);
    exit;
}

//validasi status absensi
$allowedStatus = ["hadir", "izin", "sakit", "alpha"];
if (!in_array($data->status, $allowedStatus)) {
    http_response_code(400);
    echo json_encode(["message" => "Status tidak valid"]);
    exit;
}

//koneksi ke database
$db = new Database();
$conn = $db->connect();

//query simpan data absensi
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

//jika absensi berasal dari surat izin
if (!empty($data->izin_id)) {

    //jika izin atau sakit = disetujui
    if (in_array($data->status, ["izin", "sakit"])) {
        $izinStatus = "disetujui";
    } else {
        //selain itu = ditolak
        $izinStatus = "ditolak";
    }

    //update status surat izin
    $updateIzin = "UPDATE izin SET status = :status WHERE id = :izin_id";
    $stmtIzin = $conn->prepare($updateIzin);
    $stmtIzin->bindParam(":status", $izinStatus); 
    $stmtIzin->bindParam(":izin_id", $data->izin_id); 
    $stmtIzin->execute();

echo json_encode([
    "status" => true,
    "message" => "Absensi berhasil disimpan"
]);
