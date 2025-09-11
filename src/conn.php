<?php
$host = getenv("DB_HOST") ?: "db";
$user = getenv("DB_USER") ?: "root";
$pass = getenv("DB_PASSWORD") ?: "root";
$db   = getenv("DB_NAME") ?: "minha_base";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}
echo "Conexão OK!";
