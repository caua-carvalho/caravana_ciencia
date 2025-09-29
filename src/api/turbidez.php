<?php
require '../conn.php'; // conexão PDO

function processarRequisicao(): void {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        responderJson(['status' => 'erro', 'mensagem' => 'Método não suportado']);
        return;
    }

    $jsonInput = file_get_contents('php://input');
    $dados = validarEntrada($jsonInput);

    if ($dados['status'] === 'erro') {
        responderJson($dados);
        return;
    }

    $atualizacao = atualizarTurbidezPraia($dados['praia_id'], $dados['turbidez']);
    responderJson($atualizacao);
}

function validarEntrada(string $jsonInput): array {
    $dados = json_decode($jsonInput, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        return ['status' => 'erro', 'mensagem' => 'JSON inválido'];
    }

    if (!isset($dados['praia_id']) || !is_int($dados['praia_id'])) {
        return ['status' => 'erro', 'mensagem' => 'Chave "praia_id" ausente ou valor inválido'];
    }

    if (!isset($dados['turbidez']) || !is_int($dados['turbidez'])) {
        return ['status' => 'erro', 'mensagem' => 'Chave "turbidez" ausente ou valor inválido'];
    }

    return [
        'status' => 'ok',
        'praia_id' => $dados['praia_id'],
        'turbidez' => $dados['turbidez']
    ];
}

function atualizarTurbidezPraia(int $idPraia, int $turbidez): array {
    global $pdo;
    if (!$pdo) {
        return ['status' => 'erro', 'mensagem' => 'Falha na conexão com o banco'];
    }

    $sql = "INSERT INTO turbidez (turbidez, praia_id) VALUES (:turbidez, :praia_id);";
    $stmt = $pdo->prepare($sql);
        $executou = $stmt->execute([':turbidez' => $turbidez, ':praia_id' => $idPraia]);

    if (!$executou) {
        return ['status' => 'erro', 'mensagem' => 'Falha ao atualizar turbidez'];
    }

    return ['status' => 'ok', 'mensagem' => 'Turbidez atualizada com sucesso', 'praia_id' => $idPraia, 'turbidez' => $turbidez];
}

function responderJson(array $dados): void {
    header('Content-Type: application/json');
    echo json_encode($dados);
}

processarRequisicao();
