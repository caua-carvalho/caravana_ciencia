<?php
// Gerado pelo Copilot
// Permite o uso de CORS para facilitar o desenvolvimento local
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

$host = "dpg-d31ijdvdiees73bau1sg-a.ohio-postgres.render.com"; // host completo
$db   = "caravana";
$user = "caravana_user";
$pass = "0mFJMBGyawH3DjYsPDiG7VLlYsrklOaO";
$port = "5432";

$dsn = "pgsql:host=$host;port=$port;dbname=$db";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    echo "Erro na conexão com o banco de dados: " . $e->getMessage();
    $pdo = null;
}
