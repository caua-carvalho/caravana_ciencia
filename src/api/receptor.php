<?php
require '../conn.php';

// Função para inserir o dado de turbidez no banco
function inserirTurbidez($turbidez) {
    global $conexao;

    $stmt = $conexao->prepare("INSERT INTO turbidez (valor) VALUES (?)");
    if (!$stmt) {
        die('Erro ao preparar a query: ' . $conexao->error);
    }

    $stmt->bind_param('d', $turbidez);

    if (!$stmt->execute()) {
        die('Erro ao executar a query: ' . $stmt->error);
    }

    $stmt->close();
    $conexao->close();
}

// Endpoint para receber o JSON
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dados = json_decode(file_get_contents('php://input'), true);

    if (isset($dados['turbidez']) && is_numeric($dados['turbidez'])) {
        inserirTurbidez($dados['turbidez']);
        echo json_encode(['mensagem' => 'Dado inserido com sucesso']);
    } else {
        http_response_code(400);
        echo json_encode(['erro' => 'JSON inválido ou campo "turbidez" ausente']);
    }
} else {
    http_response_code(405);
    echo json_encode(['erro' => 'Método não permitido']);
}

?>