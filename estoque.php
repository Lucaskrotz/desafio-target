<?php
// Desafio 2 - Movimentação de estoque (entrada/saída) dos produtos de estoque.json.

class Estoque
{
    const TIPOS = ['E' => 'Entrada', 'S' => 'Saída'];

    public array $produtos = [];
    public array $movimentacoes = [];
    private int $proximoId = 1;

    public function __construct(array $produtos)
    {
        foreach ($produtos as $p) {
            $this->produtos[$p['codigoProduto']] = $p;
        }
    }

    public function movimentar(int $codigo, string $tipo, int $quantidade, string $descricao): array
    {
        if (!isset($this->produtos[$codigo])) {
            throw new InvalidArgumentException("Produto $codigo não encontrado.");
        }
        if (!isset(self::TIPOS[$tipo])) {
            throw new InvalidArgumentException('Tipo deve ser E (entrada) ou S (saída).');
        }
        if ($quantidade <= 0) {
            throw new InvalidArgumentException('Quantidade deve ser maior que zero.');
        }
        $produto = &$this->produtos[$codigo];
        if ($tipo === 'S' && $quantidade > $produto['estoque']) {
            throw new InvalidArgumentException("Estoque insuficiente: disponível {$produto['estoque']}.");
        }

        $produto['estoque'] += $tipo === 'E' ? $quantidade : -$quantidade;
        $mov = [
            'id' => $this->proximoId++,
            'codigoProduto' => $codigo,
            'tipo' => self::TIPOS[$tipo],
            'descricao' => $descricao,
            'quantidade' => $quantidade,
            'estoqueFinal' => $produto['estoque'],
        ];
        $this->movimentacoes[] = $mov;
        return $mov;
    }
}

function perguntar(string $texto): string
{
    echo $texto;
    return trim((string) fgets(STDIN));
}

function lerInteiro(string $texto): int
{
    $valor = filter_var(perguntar($texto), FILTER_VALIDATE_INT);
    if ($valor === false) throw new InvalidArgumentException('Digite um número inteiro.');
    return $valor;
}

if (realpath($_SERVER['SCRIPT_FILENAME']) === __FILE__) {
    $dados = json_decode(file_get_contents(__DIR__ . '/estoque.json'), true, flags: JSON_THROW_ON_ERROR);
    $estoque = new Estoque($dados['estoque']);

    while (true) {
        echo PHP_EOL . 'Produtos:' . PHP_EOL;
        foreach ($estoque->produtos as $p) {
            printf("  %d - %s estoque: %d\n", $p['codigoProduto'], padTexto($p['descricaoProduto'], 28), $p['estoque']);
        }

        $codigo = perguntar(PHP_EOL . 'Código do produto (Enter para sair): ');
        if ($codigo === '') break;
        try {
            $mov = $estoque->movimentar(
                filter_var($codigo, FILTER_VALIDATE_INT) ?: 0,
                strtoupper(perguntar('Tipo (E = entrada, S = saída): ')),
                lerInteiro('Quantidade: '),
                perguntar('Descrição da movimentação: '),
            );
        } catch (InvalidArgumentException $e) {
            echo 'Erro: ' . $e->getMessage() . PHP_EOL;
            continue;
        }
        $nome = $estoque->produtos[$mov['codigoProduto']]['descricaoProduto'];
        echo "Movimentação #{$mov['id']} ({$mov['tipo']}: {$mov['descricao']}) registrada." . PHP_EOL;
        echo "Estoque final de $nome: {$mov['estoqueFinal']}" . PHP_EOL;
    }

    echo PHP_EOL . 'Movimentações realizadas:' . PHP_EOL;
    foreach ($estoque->movimentacoes as $m) {
        printf("  #%d %s prod %d qtd %-5d final %-5d %s\n",
            $m['id'], padTexto($m['tipo'], 7), $m['codigoProduto'], $m['quantidade'], $m['estoqueFinal'], $m['descricao']);
    }
}

// str_pad conta bytes; com acentos o alinhamento quebraria
function padTexto(string $s, int $tamanho): string
{
    return $s . str_repeat(' ', max(0, $tamanho - mb_strlen($s)));
}
