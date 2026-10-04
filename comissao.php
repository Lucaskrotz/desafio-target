<?php
// Desafio 1 - Comissão por vendedor a partir de vendas.json.
// Valores em centavos inteiros para evitar erro de arredondamento do float.

function comissaoDaVendaEmCentesimosDeCentavo(int $centavos): int
{
    if ($centavos < 10000) return 0;          // abaixo de R$ 100,00
    return $centavos * ($centavos < 50000 ? 1 : 5); // 1% ou 5%
}

/** @return array<string, int> comissão em centavos por vendedor */
function comissoesPorVendedor(array $vendas): array
{
    $totais = [];
    foreach ($vendas as $venda) {
        $centavos = (int) round($venda['valor'] * 100);
        $totais[$venda['vendedor']] = ($totais[$venda['vendedor']] ?? 0)
            + comissaoDaVendaEmCentesimosDeCentavo($centavos);
    }
    return array_map(fn($t) => (int) round($t / 100), $totais);
}

function formatarReais(int $centavos): string
{
    return 'R$ ' . number_format($centavos / 100, 2, ',', '.');
}

if (realpath($_SERVER['SCRIPT_FILENAME']) === __FILE__) {
    $dados = json_decode(file_get_contents(__DIR__ . '/vendas.json'), true, flags: JSON_THROW_ON_ERROR);
    foreach (comissoesPorVendedor($dados['vendas']) as $nome => $centavos) {
        // str_pad conta bytes; compensa os acentos para manter o alinhamento
        echo str_pad($nome, 20 + strlen($nome) - mb_strlen($nome)) . str_pad(formatarReais($centavos), 14, ' ', STR_PAD_LEFT) . PHP_EOL;
    }
}
