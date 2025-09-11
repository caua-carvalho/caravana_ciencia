<?php
$host = "dpg-d31ijdvdiees73bau1sg-a.abc.render.com"; // host completo
$db   = "caravana";
$user = "caravana_user";
$pass = "0mFJMBGyawH3DjYsPDiG7VLlYsrklO";
$port = "5432";

$dsn = "pgsql:host=$host;port=$port;dbname=$db";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    echo "Conexão PostgreSQL bem-sucedida!";
} catch (PDOException $e) {
    echo "Erro na conexão: " . $e->getMessage();
}
