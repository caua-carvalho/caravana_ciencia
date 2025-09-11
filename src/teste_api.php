<?php
// Endpoint para validar a turbidez recebida via POST
function validarTurbidez(string $jsonInput): array {
    $dados = json_decode($jsonInput, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        return ['status' => 'erro', 'mensagem' => 'JSON inválido'];
    }

    if (!isset($dados['turbidez']) || !is_int($dados['turbidez'])) {
        return ['status' => 'erro', 'mensagem' => 'Chave "turbidez" ausente ou valor inválido'];
    }

    return ['status' => 'ok', 'mensagem' => 'Valor recebido com sucesso'];
}

// Verifica se a requisição é POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jsonInput = file_get_contents('php://input'); // Lê o corpo da requisição
    $resultado = validarTurbidez($jsonInput);

    header('Content-Type: application/json');
    echo json_encode($resultado);
} else {
    // Retorna erro se não for uma requisição POST
    header('Content-Type: application/json');
    echo json_encode(['status' => 'erro', 'mensagem' => 'Método não suportado']);
}

// Gerado pelo Copilot
?>