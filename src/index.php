<?php
require 'conn_pg.php'; // Conexão PDO com PostgreSQL

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
function buscarPraias(): array {
    global $pdo;
    $sql = "SELECT * FROM praias ORDER BY id";
    try {
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Erro ao buscar praias: " . $e->getMessage();
        return [];
    }
}

// Exibe as informações de uma praia
function exibirInfoPraia(array $praia): void {
    $nome = htmlspecialchars($praia['nome'], ENT_QUOTES, 'UTF-8');
    $descricao = htmlspecialchars($praia['descricao'], ENT_QUOTES, 'UTF-8');
    $turbidez = htmlspecialchars($praia['taxa_turbidez'], ENT_QUOTES, 'UTF-8');
    $foto = htmlspecialchars($praia['foto'], ENT_QUOTES, 'UTF-8');

    echo <<<HTML
<div style="border:1px solid #ccc; padding:10px; margin:10px; border-radius:8px;">
    <h2>{$nome}</h2>
    <img src="{$foto}" alt="{$nome}" style="max-width:300px; display:block; margin-bottom:10px;">
    <p><strong>Turbidez:</strong> {$turbidez}</p>
    <p><strong>Descrição:</strong> {$descricao}</p>
</div>
HTML;
}

// Executa a função principal
exibirPraias();
