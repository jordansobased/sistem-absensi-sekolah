<?php
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

//koneksi database
$db = new Database();
$conn = $db->connect();

//query insert user
$query = "INSERT INTO users (nama,email,password,role)
          VALUES (:nama,:email,:password,:role)";
$stmt = $conn->prepare($query);

//hash password
$password = password_hash($data->password, PASSWORD_DEFAULT);

//binding parameter
$stmt->bindParam(":nama", $data->nama);
$stmt->bindParam(":email", $data->email);
$stmt->bindParam(":password", $password);
$stmt->bindParam(":role", $data->role);

//eksekusi query
if ($stmt->execute()) {
    echo json_encode(["message" => "User berhasil dibuat"]);
}
