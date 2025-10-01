<?php
require '../conn.php';

header('Content-Type: application/json');

// Recebe o JSON do corpo da requisição
$input = json_decode(file_get_contents("php://input"), true);
$praia_id = $input['praia_id'] ?? null;
$valor = $input['turbidez'] ?? null;

if (!$praia_id || !$valor) {
    echo json_encode([
        'status' => 'erro',
        'mensagem' => 'Parâmetros inválidos'
    ]);
    exit;
}

try {
    $sql = "INSERT INTO turbidez (praia_id, valor, data_medicao) VALUES (?, ?, NOW())";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$praia_id, $valor]);

    echo json_encode([
        'status' => 'sucesso',
        'mensagem' => 'Turbidez registrada com sucesso',
        'registro' => [
            'praia_id' => $praia_id,
            'turbidez' => $valor,
            'data_medicao' => date('Y-m-d H:i:s')
        ]
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'status' => 'erro',
        'mensagem' => 'Falha ao inserir turbidez',
        'detalhe' => $e->getMessage()
    ]);
}
