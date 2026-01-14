<?php
header("Content-Type: application/json");
include_once "../config/database.php";
include_once "../config/jwt.php";
require "../vendor/autoload.php";
require __DIR__ . '/../vendor/autoload.php';
use Firebase\JWT\JWT;

$data = json_decode(file_get_contents("php://input"));

$db = new Database();
$conn = $db->connect();

$query = "SELECT * FROM users WHERE email = :email";
$stmt = $conn->prepare($query);
$stmt->bindParam(":email", $data->email);
$stmt->execute();

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($data->password, $user['password'])) {
    $payload = [
        "iss" => $issuer,
        "aud" => $audience,
        "iat" => $issued_at,
        "exp" => $expiration_time,
        "data" => [
            "id" => $user['id'],
            "nama" => $user['nama'],
            "role" => $user['role']
        ]
    ];

    $jwt = JWT::encode($payload, $secret_key, 'HS256');

    echo json_encode([
        "status" => true,
        "token" => $jwt
    ]);
} else {
    http_response_code(401);
    echo json_encode(["message" => "Login gagal"]);
}
