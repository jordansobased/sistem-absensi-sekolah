<?php
header("Content-Type: application/json");
include_once "../middleware/auth.php";
include_once "../config/database.php";

//cek role harus admin
if ($userData->role !== "admin") {
    http_response_code(403);
    echo json_encode(["message" => "Hanya admin"]);
    exit;
}

//ambil body request
$data = json_decode(file_get_contents("php://input"));

//validasi data wajib
if (
    empty($data->id) ||
    empty($data->nama) ||
    empty($data->email) ||
    empty($data->role)
) {
    http_response_code(400);
    echo json_encode(["message" => "Data tidak lengkap"]);
    exit;
}

//koneksi ke database
$db = new Database();
$conn = $db->connect();

//query update data
if (!empty($data->password)) {
    $password = password_hash($data->password, PASSWORD_DEFAULT);
    $query = "UPDATE users 
              SET nama = :nama, email = :email, role = :role, password = :password
              WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(":password", $password);
} else {
    $query = "UPDATE users 
              SET nama = :nama, email = :email, role = :role
              WHERE id = :id";
    $stmt = $conn->prepare($query);
}

$stmt->bindParam(":id", $data->id);
$stmt->bindParam(":nama", $data->nama);
$stmt->bindParam(":email", $data->email);
$stmt->bindParam(":role", $data->role);
$stmt->execute();

echo json_encode([
    "status" => true,
    "message" => "User berhasil diupdate"
]);
