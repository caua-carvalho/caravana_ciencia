<?php
// Configuração do banco de dados
define('DB_HOST', 'sql103.infinityfree.com');
define('DB_USER', 'if0_39917407');
define('DB_PASS', '9H5pGekYWdHUF');
define('DB_NAME', 'if0_39917407_caravana');

$conexao = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conexao->connect_error) {
    die('Erro na conexão com o banco de dados: ' . $conexao->connect_error);
}