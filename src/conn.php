<?php
// conn.php - Conexão com MySQL usando mysqli

$host = "dpg-d31ijdvdiees73bau1sg-a";
$user = "caravana_user";
$password = "0mFJMBGyawH3DjYsPDiG7VLlYsrklOaO";
$database = "caravana";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

echo "Conexão MySQL bem-sucedida!";
$conn->close();
