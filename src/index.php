<?php
require 'conn.php';

// Gerado pelo Copilot
// Função principal para buscar e exibir as praias
function exibirPraias() {
    $praias = buscarPraias();
    if (empty($praias)) {
        echo "Nenhuma praia encontrada.";
        return;
    }
    foreach ($praias as $praia) {
        exibirInfoPraia($praia);
    }
}

// Busca todas as praias no banco de dados
function buscarPraias() {
    global $conn;
    $sql = "SELECT * FROM praias";
    $resultado = $conn->query($sql);

    if (!$resultado) {
        echo "Erro ao buscar praias: " . $conn->error;
        return [];
    }

    $praias = [];
    while ($linha = $resultado->fetch_assoc()) {
        $praias[] = $linha;
    }
    return $praias;
}

// Exibe as informações de uma praia
function exibirInfoPraia($praia) {
    echo "<div>";
    echo "<h2>" . htmlspecialchars($praia['nome']) . "</h2>";
    echo "<p>Turbidez: " . htmlspecialchars($praia['taxa_turbidez']) . "</p>";
    echo "<p>Descrição: " . htmlspecialchars($praia['descricao']) . "</p>";
    echo "</div>";
}

// Chama a função principal
exibirPraias();