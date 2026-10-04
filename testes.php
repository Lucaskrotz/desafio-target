<?php
// Testes dos 3 desafios. Rodar: php testes.php

require __DIR__ . '/comissao.php';
require __DIR__ . '/estoque.php';
require __DIR__ . '/juros.php';

$falhas = 0;
function check(string $nome, mixed $obtido, mixed $esperado): void
{
    global $falhas;
    $ok = $obtido === $esperado;
    $falhas += $ok ? 0 : 1;
    echo ($ok ? '[OK]   ' : '[FALHA] ') . $nome . ($ok ? '' : ' -> obtido: ' . var_export($obtido, true)) . PHP_EOL;
}

function falha(callable $fn): bool
{
    try { $fn(); return false; } catch (InvalidArgumentException) { return true; }
}

// Desafio 1
check('venda < 100 sem comissão', comissaoDaVendaEmCentesimosDeCentavo(9999), 0);
check('venda de 100 gera 1%', comissaoDaVendaEmCentesimosDeCentavo(10000), 10000);
check('venda de 500 gera 5%', comissaoDaVendaEmCentesimosDeCentavo(50000), 250000);
$vendas = json_decode(file_get_contents(__DIR__ . '/vendas.json'), true)['vendas'];
check('totais do vendas.json', comissoesPorVendedor($vendas), [
    'João Silva' => 49568,
    'Maria Souza' => 46595,
    'Carlos Oliveira' => 37937,
    'Ana Lima' => 40498,
]);

// Desafio 2
$estoque = new Estoque(json_decode(file_get_contents(__DIR__ . '/estoque.json'), true)['estoque']);
$m1 = $estoque->movimentar(101, 'E', 50, 'Compra fornecedor');
$m2 = $estoque->movimentar(101, 'S', 30, 'Venda balcão');
check('entrada soma no estoque', $m1['estoqueFinal'], 200);
check('saída subtrai do estoque', $m2['estoqueFinal'], 170);
check('ids únicos', $m1['id'] !== $m2['id'], true);
check('produto inexistente', falha(fn() => $estoque->movimentar(999, 'E', 1, 'x')), true);
check('tipo inválido', falha(fn() => $estoque->movimentar(101, 'X', 1, 'x')), true);
check('quantidade zero', falha(fn() => $estoque->movimentar(101, 'E', 0, 'x')), true);
check('saída maior que estoque', falha(fn() => $estoque->movimentar(102, 'S', 76, 'x')), true);
check('erros não alteram estoque', [$estoque->produtos[102]['estoque'], count($estoque->movimentacoes)], [75, 2]);

// Desafio 3
$d = fn($s) => new DateTimeImmutable($s);
check('10 dias de atraso', calcularJuros(100000, $d('2026-01-01'), $d('2026-01-11')), [10, 25000]);
check('ainda não venceu', calcularJuros(100000, $d('2026-01-11'), $d('2026-01-01')), [0, 0]);
check('valor 1.500,00', lerValorEmCentavos('1.500,00'), 150000);
check('valor 1500.5', lerValorEmCentavos('1500.5'), 150050);
check('valor inválido', falha(fn() => lerValorEmCentavos('abc')), true);
check('data 31/02 inválida', falha(fn() => lerData('31/02/2026')), true);

echo PHP_EOL . ($falhas ? "$falhas teste(s) falharam." : 'Todos os testes passaram.') . PHP_EOL;
exit($falhas ? 1 : 0);
