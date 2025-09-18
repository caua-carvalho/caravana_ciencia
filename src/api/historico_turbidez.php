<?php
require '../conn.php';

const HEADER_JSON = 'Content-Type: application/json';

// Define o cabeçalho como JSON
header(HEADER_JSON);

$input = json_decode(file_get_contents("php://input"), true);

if (isset($input['id_praia'])) {
    $id_praia = $input['id_praia'];
} else {
    echo json_encode([
        'status' => 'erro',
        'mensagem' => 'ID da praia não foi enviado.'
    ]);
    exit;
}

function buscarTurbidez($id_praia): array {
    global $pdo;

    $sql = "SELECT * FROM turbidez WHERE praia_id = ?";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute(params: [$id_praia]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        registrarErro(mensagem: $e->getMessage());
        return [];
    }
}

// Chamada da função e retorno dos dados
$turbidez = buscarTurbidez($id_praia);
echo json_encode([
    'status' => 'sucesso',
    'dados' => $turbidez
]);