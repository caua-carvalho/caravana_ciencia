<?php

require '../conn.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dados = json_decode(file_get_contents('php://input'), true);

    // Verifica se o dado 'turbidez' existe e é um número
    if (isset($dados['turbidez']) && is_numeric($dados['turbidez'])) {

        // URL do endpoint para o qual você quer enviar o JSON
        $url = "http://localhost/caua/caravana_ciencia/api/receptor.php"; // Substitua com o URL real do seu endpoint

        // Inicia a sessão cURL
        $ch = curl_init($url);

        // Configurações da requisição cURL
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json', // Tipo do conteúdo
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dados)); // Envia o JSON

        // Executa a requisição e captura a resposta
        $response = curl_exec($ch);

        // Verifica se ocorreu algum erro
        if ($response === false) {
            $error = curl_error($ch);
            echo json_encode(["error" => $error]);
        } else {
            // Sucesso, exibe a resposta
            echo $response;
        }

        // Fecha a sessão cURL
        curl_close($ch);
    } else {
        echo json_encode(["error" => "Dados inválidos."]);
    }
}
