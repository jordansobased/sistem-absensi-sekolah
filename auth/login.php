<?php
header("Content-Type: application/json");
include_once "../config/database.php";
include_once "../config/jwt.php";
require "../vendor/autoload.php";
require __DIR__ . '/../vendor/autoload.php';

use Firebase\JWT\JWT;

//ambil body request
$data = json_decode(file_get_contents("php://input"));

//koneksi database
$db = new Database();
$conn = $db->connect();

//ambil user berdasarkan email
$query = "SELECT * FROM users WHERE email = :email";
$stmt = $conn->prepare($query);
$stmt->bindParam(":email", $data->email);
$stmt->execute();

$user = $stmt->fetch(PDO::FETCH_ASSOC);

//verifikasi user dan password
if ($user && password_verify($data->password, $user['password'])) {

    //payload jwt
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

    //generate token jwt
    $jwt = JWT::encode($payload, $secret_key, 'HS256');

    //response berhasil
    echo json_encode([
        "status" => true,
        "token" => $jwt
    ]);

} else {

    //login gagal
    http_response_code(401);
    echo json_encode(["message" => "Login gagal"]);
}
