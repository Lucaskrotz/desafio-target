# Desafio Técnico - Target Sistemas

Solução em **PHP 8.1+**, só com recursos nativos (sem Composer, sem framework).

| # | Arquivo | Como rodar |
|---|---------|------------|
| 1 | `comissao.php` (lê `vendas.json`) | `php comissao.php` |
| 2 | `estoque.php` (lê `estoque.json`) | `php estoque.php` |
| 3 | `juros.php` | `php juros.php` |

Testes: `php testes.php`

Os valores em dinheiro são calculados em **centavos inteiros**, para não ter erro de arredondamento de `float`.

## 1. Comissão de vendedores
A regra vale para **cada venda**, e as comissões são somadas por vendedor:
- abaixo de R$ 100,00 → sem comissão
- de R$ 100,00 até abaixo de R$ 500,00 → 1%
- a partir de R$ 500,00 → 5%

Resultado:
```
João Silva               R$ 495,68
Maria Souza              R$ 465,95
Carlos Oliveira          R$ 379,37
Ana Lima                 R$ 404,98
```

## 2. Movimentação de estoque
Menu interativo no terminal: escolha o produto, o tipo (E = entrada / S = saída), a quantidade e uma descrição
(ex.: "Compra de fornecedor", "Venda balcão", "Devolução"). Cada movimentação recebe um **ID único sequencial**
e o programa mostra o **estoque final** do produto. Ao sair, lista todas as movimentações feitas.

Validações: produto inexistente, tipo inválido, quantidade não inteira ou ≤ 0 e saída maior que o estoque
disponível (o estoque nunca fica negativo). Os dados ficam em memória durante a execução.

## 3. Juros por atraso
Informe o valor (`1500,00`, `1.500,00` ou `1500.00`) e a data de vencimento (`dd/mm/aaaa`).
Calcula juros simples de **2,5% ao dia** sobre o valor, pelos dias de atraso até hoje:

`juros = valor × 0,025 × dias_de_atraso`

Se ainda não venceu, os juros são zero. Ex.: R$ 1.000,00 com 10 dias de atraso → R$ 250,00 de juros.
Datas inexistentes (ex.: 31/02) são rejeitadas.
