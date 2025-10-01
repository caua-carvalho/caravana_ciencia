<?php
require '../conn.php';

const HEADER_JSON = 'Content-Type: application/json';
header(HEADER_JSON);

/**
 * Função genérica para responder com JSON e encerrar execução
 */
function respostaJson(array $dados, int $statusCode = 200): void {
    http_response_code($statusCode);
    echo json_encode($dados, JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Função para validar entrada
 */
function validarEntradas(?string $id_praia, ?string $data_inicial): void {
    if ($id_praia !== null && !ctype_digit((string)$id_praia)) {
        respostaJson(['status' => 'erro', 'mensagem' => 'id_praia inválido'], 400);
    }
    if ($data_inicial !== null && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $data_inicial)) {
        respostaJson(['status' => 'erro', 'mensagem' => 'data_inicial inválida'], 400);
    }
}

// Recebe dados JSON do corpo da requisição
$input = json_decode(file_get_contents("php://input"), true) ?? [];
$id_praia = $input['id_praia'] ?? null;
$data_inicial = $input['data_inicial'] ?? null;

// Valida entradas antes de consultar
validarEntradas($id_praia, $data_inicial);

/**
 * Busca médias de turbidez
 */
function buscarTurbidez($id_praia = null, $data_inicial = null): array {
    global $pdo;

    $where = [];
    $params = [];

    if ($id_praia) {
        $where[] = "t.praia_id = ?";
        $params[] = $id_praia;
    }
    if ($data_inicial) {
        $where[] = "DATE(t.data_medicao) >= ?";
        $params[] = $data_inicial;
    }

    $whereSql = $where ? ("WHERE " . implode(" AND ", $where)) : "";

    $sql = "SELECT 
                DATE(t.data_medicao) as data_medicao, 
                t.praia_id, 
                p.nome as praia_nome, 
                AVG(t.valor) as media_turbidez
            FROM turbidez t 
            JOIN praias p ON t.praia_id = p.id 
            $whereSql
            GROUP BY DATE(t.data_medicao), t.praia_id, p.nome 
            ORDER BY data_medicao DESC";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erro buscarTurbidez: " . $e->getMessage());
        return [];
    }
}

/**
 * Busca último valor de turbidez do dia atual
 */
function buscarTurbidezAtual($id_praia = null): array {
    global $pdo;

    $where = [];
    $params = [];

    if ($id_praia) {
        $where[] = "t.praia_id = ?";
        $params[] = $id_praia;
    }

    // Adiciona condições diretamente ao array
    $where[] = "t.data_medicao::date = CURRENT_DATE";
    $where[] = "t.data_medicao = (
        SELECT MAX(t2.data_medicao)
        FROM turbidez t2
        WHERE t2.praia_id = t.praia_id
          AND t2.data_medicao::date = CURRENT_DATE
    )";

    $whereSql = "WHERE " . implode(" AND ", $where);

    $sql = "SELECT 
                t.praia_id, 
                p.nome as praia_nome, 
                t.valor as turbidez_atual, 
                t.data_medicao
            FROM turbidez t 
            JOIN praias p ON t.praia_id = p.id 
            $whereSql";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erro buscarTurbidezAtual: " . $e->getMessage());
        return [];
    }
}

// Execução principal
$turbidez = buscarTurbidez($id_praia, $data_inicial);
$turbidez_atual = buscarTurbidezAtual($id_praia);

respostaJson([
    'status' => 'sucesso',
    'dados' => $turbidez,
    'turbidez_atual' => $turbidez_atual
]);
