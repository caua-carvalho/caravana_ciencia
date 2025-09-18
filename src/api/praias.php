<?php
require '../conn.php';

// Gerado pelo Copilot

const HEADER_JSON = 'Content-Type: application/json';

/**
 * Controla o fluxo principal da API de praias.
 */
function processarRequisicaoPraias(): void {
    $praias = buscarPraiasNoBanco();
    enviarRespostaJson($praias);
}

/**
 * Busca as praias no banco de dados, trazendo apenas a turbidez mais recente de cada praia.
 */
function buscarPraiasNoBanco(): array {
    global $pdo;

    $sql = "
        SELECT 
            p.*, 
            t.valor AS turbidez_valor, 
            t.data_medicao AS turbidez_data
        FROM praias p
        LEFT JOIN (
            SELECT 
                turbidez.*
            FROM turbidez
            INNER JOIN (
                SELECT praia_id, MAX(data_medicao) AS max_data
                FROM turbidez
                GROUP BY praia_id
            ) ultimas
            ON turbidez.praia_id = ultimas.praia_id AND turbidez.data_medicao = ultimas.max_data
        ) t ON t.praia_id = p.id
        ORDER BY p.id
    ";

    try {
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        registrarErro($e->getMessage());
        return [];
    }
}

/**
 * Envia os dados como resposta JSON.
 */
function enviarRespostaJson(array $dados): void {
    header(HEADER_JSON);
    echo json_encode($dados);
    exit;
}

/**
 * Registra erros no log.
 */
function registrarErro(string $mensagem): void {
    error_log("Erro ao buscar praias: " . $mensagem);
}

processarRequisicaoPraias();