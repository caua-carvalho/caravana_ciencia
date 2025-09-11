<?php
// Gerado pelo Copilot

// Inclui o arquivo de conexão
require_once 'conn.php';

/**
 * Testa a conexão com o banco de dados.
 */
function testarConexao()
{
    // Verifica se a variável $conn está definida
    if (!isset($conn)) {
        die('Erro: A conexão não foi encontrada. Certifique-se de que o arquivo conn.php está configurado corretamente.');
    }

    // Testa a conexão
    if ($conn) {
        die('Falha na conexão: ');
    }

    echo 'Conexão bem-sucedida!';
}

// Executa o teste de conexão
testarConexao();
?>