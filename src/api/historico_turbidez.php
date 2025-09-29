<?php
require '../conn.php';

const HEADER_JSON = 'Content-Type: application/json';

// Define o cabeçalho como JSON
header(HEADER_JSON);


$input = json_decode(file_get_contents("php://input"), true);
$id_praia = $input['id_praia'] ?? null;
$data_inicial = $input['data_inicial'] ?? null;


function buscarTurbidez($id_praia = null, $data_inicial = null): array {
    global $pdo;

    $where = [];
    $params = [];
    if ($id_praia) {
        $where[] = "t.praia_id = ?";
        $params[] = $id_praia;
    }
    if ($data_inicial) {
        $where[] = "t.data_medicao >= ?";
        $params[] = $data_inicial;
    }
    $whereSql = $where ? ("WHERE " . implode(" AND ", $where)) : "";

    $sql = "SELECT DATE(t.data_medicao) as data_medicao, t.praia_id, p.nome as praia_nome, AVG(t.valor) as media_turbidez FROM turbidez t JOIN praias p ON t.praia_id = p.id $whereSql GROUP BY DATE(t.data_medicao), t.praia_id, p.nome ORDER BY data_medicao DESC";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        registrarErro(mensagem: $e->getMessage());
        return [];
    }
}

// Chamada da função e retorno dos dados
$turbidez = buscarTurbidez($id_praia, $data_inicial);
echo json_encode([
    'status' => 'sucesso',
    'dados' => $turbidez
]);