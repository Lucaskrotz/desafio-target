<?php
// Desafio 3 - Juros por atraso: 2,5% ao dia (juros simples) sobre o valor, até hoje.
// Valores em centavos inteiros para evitar erro de arredondamento do float.

/** @return array{int, int} [dias de atraso, juros em centavos] */
function calcularJuros(int $centavos, DateTimeImmutable $vencimento, DateTimeImmutable $hoje): array
{
    $diff = $vencimento->diff($hoje);
    $diasAtraso = $diff->invert ? 0 : $diff->days;
    return [$diasAtraso, (int) round($centavos * 25 * $diasAtraso / 1000)];
}

/** Aceita "1500", "1500.00", "1500,00" e "1.500,00". */
function lerValorEmCentavos(string $texto): int
{
    if (str_contains($texto, ',')) {
        $texto = str_replace(['.', ','], ['', '.'], $texto);
    }
    if (!preg_match('/^\d+(\.\d{1,2})?$/', $texto)) {
        throw new InvalidArgumentException('Valor inválido. Use o formato 1500,00.');
    }
    [$reais, $cents] = explode('.', $texto . '.');
    return (int) $reais * 100 + (int) str_pad($cents, 2, '0');
}

function lerData(string $texto): DateTimeImmutable
{
    $data = DateTimeImmutable::createFromFormat('!d/m/Y', $texto);
    // createFromFormat aceita 31/02 virando 03/03; comparar de volta barra isso
    if (!$data || $data->format('d/m/Y') !== $texto) {
        throw new InvalidArgumentException('Data inválida. Use o formato dd/mm/aaaa.');
    }
    return $data;
}

if (realpath($_SERVER['SCRIPT_FILENAME']) === __FILE__) {
    try {
        echo 'Valor (ex: 1500,00): ';
        $centavos = lerValorEmCentavos(trim((string) fgets(STDIN)));
        echo 'Data de vencimento (dd/mm/aaaa): ';
        $vencimento = lerData(trim((string) fgets(STDIN)));
    } catch (InvalidArgumentException $e) {
        exit('Erro: ' . $e->getMessage() . PHP_EOL);
    }

    $reais = fn(int $c) => 'R$ ' . number_format($c / 100, 2, ',', '.');
    // sem isso o PHP usa UTC e, depois das 21h, "hoje" já vira o dia seguinte
    $hoje = new DateTimeImmutable('today', new DateTimeZone('America/Sao_Paulo'));
    [$dias, $juros] = calcularJuros($centavos, $vencimento, $hoje);
    echo PHP_EOL . 'Hoje: ' . $hoje->format('d/m/Y') . " | Dias em atraso: $dias" . PHP_EOL;
    echo 'Juros: ' . $reais($juros) . PHP_EOL;
    echo 'Total a pagar: ' . $reais($centavos + $juros) . PHP_EOL;
}
