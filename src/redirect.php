<?php
require 'conn.php'; // conexão PDO

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

    $atualizacao = atualizarTurbidezPraia($dados['id_praia'], $dados['turbidez']);
    responderJson($atualizacao);
}

function validarEntrada(string $jsonInput): array {
    $dados = json_decode($jsonInput, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        return ['status' => 'erro', 'mensagem' => 'JSON inválido'];
    }

    if (!isset($dados['id_praia']) || !is_int($dados['id_praia'])) {
        return ['status' => 'erro', 'mensagem' => 'Chave "id_praia" ausente ou valor inválido'];
    }

    if (!isset($dados['turbidez']) || !is_int($dados['turbidez'])) {
        return ['status' => 'erro', 'mensagem' => 'Chave "turbidez" ausente ou valor inválido'];
    }

    return [
        'status' => 'ok',
        'id_praia' => $dados['id_praia'],
        'turbidez' => $dados['turbidez']
    ];
}

function atualizarTurbidezPraia(int $idPraia, int $turbidez): array {
    global $pdo;
    if (!$pdo) {
        return ['status' => 'erro', 'mensagem' => 'Falha na conexão com o banco'];
    }

    $sql = "UPDATE praias SET taxa_turbidez = :turbidez WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $executou = $stmt->execute([':turbidez' => $turbidez, ':id' => $idPraia]);

    if (!$executou) {
        return ['status' => 'erro', 'mensagem' => 'Falha ao atualizar turbidez'];
    }

    return ['status' => 'ok', 'mensagem' => 'Turbidez atualizada com sucesso', 'id_praia' => $idPraia, 'turbidez' => $turbidez];
}

function responderJson(array $dados): void {
    header('Content-Type: application/json');
    echo json_encode($dados);
}

processarRequisicao();
