<?php
include_once "../middleware/auth.php";
include_once "../config/database.php";

if ($userData->role !== "admin") {
    http_response_code(403);
    echo json_encode(["message" => "Akses ditolak"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"));
$db = new Database();
$conn = $db->connect();

$query = "INSERT INTO users (nama,email,password,role)
          VALUES (:nama,:email,:password,:role)";
$stmt = $conn->prepare($query);

$password = password_hash($data->password, PASSWORD_DEFAULT);

$stmt->bindParam(":nama", $data->nama);
$stmt->bindParam(":email", $data->email);
$stmt->bindParam(":password", $password);
$stmt->bindParam(":role", $data->role);

if ($stmt->execute()) {
    echo json_encode(["message" => "User berhasil dibuat"]);
}
