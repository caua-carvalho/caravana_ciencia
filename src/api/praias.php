<?php
require '../conn.php'; // Conexão PDO com PostgreSQL

// Constante para o header de JSON
const HEADER_JSON = 'Content-Type: application/json';

// Função de alto nível que processa a requisição da API
// Retorna as praias em formato JSON
function processarRequisicaoPraias(): void {
    $praias = buscarPraiasNoBanco();
    enviarRespostaJson($praias);
}

// Função que busca as praias no banco de dados
function buscarPraiasNoBanco(): array {
    global $pdo;
    $sql = "SELECT * FROM praias ORDER BY id";
    try {
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        registrarErro($e->getMessage());
        return [];
    }
}

// Função que envia o JSON como resposta
function enviarRespostaJson(array $dados): void {
    header(HEADER_JSON);
    echo json_encode($dados);
    exit;
}

// Função para registrar erros (poderia salvar em log, por exemplo)
function registrarErro(string $mensagem): void {
    error_log("Erro ao buscar praias: " . $mensagem);
}

// Executa a API quando o arquivo é chamado diretamente
processarRequisicaoPraias();