<?php
include_once "../middleware/auth.php";
include_once "../config/database.php";

if ($userData->role !== "siswa") {
    http_response_code(403);
    echo json_encode(["message" => "Hanya siswa"]);
    exit;
}

$db = new Database();
$conn = $db->connect();

$query = "SELECT * FROM absensi WHERE siswa_id = :id";
$stmt = $conn->prepare($query);
$stmt->bindParam(":id", $userData->id);
$stmt->execute();

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
