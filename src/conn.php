<?php
// Configuração do banco de dados
define('DB_HOST', 'mysql.railway.internal');
define('DB_USER', 'root');
define('DB_PASS', 'aUHjWxtaJwslkOVdYPVrqrgnXFCNHsQF');
define('DB_NAME', 'railway');

$conexao = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conexao->connect_error) {
    die('Erro na conexão com o banco de dados: ' . $conexao->connect_error);
}