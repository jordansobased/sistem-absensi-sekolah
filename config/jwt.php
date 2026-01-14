<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$secret_key = "THIS_IS_A_VERY_LONG_RANDOM_SECRET_KEY_1234567890!@#$%^&*()AN";
$issuer = "localhost";
$audience = "localhost";
$issued_at = time();
$expiration_time = $issued_at + (60 * 60); 
