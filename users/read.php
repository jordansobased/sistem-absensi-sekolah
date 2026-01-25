<?php
header("Content-Type: application/json");
include_once "../middleware/auth.php";
include_once "../config/database.php";

//cek role harus admin
if ($userData->role !== 'admin') {
    http_response_code(403);
    echo json_encode(["message" => "Hanya admin"]);
    exit;
}

//ambil data users
$db = new Database();
$conn = $db->connect();

$query = "SELECT id, nama, email, role, created_at FROM users";
$stmt = $conn->prepare($query);
$stmt->execute();

$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    "status" => true,
    "data" => $data
]);
