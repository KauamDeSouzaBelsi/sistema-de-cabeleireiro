<?php 

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . "/../include/functions.php";
require_once __DIR__ . '/../login/verifica.php';
?>

<?php
// estoque.php — Estoque de produtos de cabeleireiro (PHP puro, sem extensões)
// Os dados ficam em estoque.json, criado automaticamente na mesma pasta.
// Coloque este arquivo na mesma pasta do index.php.

const ARQUIVO = __DIR__ . '/estoque.json';
const ESTOQUE_BAIXO = 5; // a partir dessa quantidade (ou menos) o produto fica destacado

function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

function ler(): array {
    if (!file_exists(ARQUIVO)) return [];
    $dados = json_decode(file_get_contents(ARQUIVO), true);
    return is_array($dados) ? $dados : [];
}

function salvar(array $produtos): void {
    file_put_contents(
        ARQUIVO,
        json_encode(array_values($produtos), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
        LOCK_EX
    );
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao     = $_POST['acao'] ?? '';
    $id       = (int)($_POST['id'] ?? 0);
    $produtos = ler();

    if ($acao === 'adicionar') {
        $nome  = trim($_POST['nome'] ?? '');
        $qtd   = max(0, (int)($_POST['quantidade'] ?? 0));
        $preco = (float)str_replace(',', '.', $_POST['preco'] ?? '0');

        if ($nome === '') {
            $erro = 'Informe o nome do produto.';
        } else {
            $novoId = $produtos ? max(array_column($produtos, 'id')) + 1 : 1;
            $produtos[] = ['id' => $novoId, 'nome' => $nome, 'quantidade' => $qtd, 'preco' => $preco];
            salvar($produtos);
        }
    } elseif ($acao === 'ajustar') {
        // tipo = entrada (soma) ou saida (subtrai, nunca abaixo de zero)
        $tipo = $_POST['tipo'] ?? '';
        $qtd  = max(1, (int)($_POST['qtd'] ?? 1));

        foreach ($produtos as &$p) {
            if ($p['id'] === $id) {
                if ($tipo === 'entrada') {
                    $p['quantidade'] += $qtd;
                } elseif ($tipo === 'saida') {
                    $p['quantidade'] = max(0, $p['quantidade'] - $qtd);
                }
            }
        }
        unset($p);
        salvar($produtos);
    } elseif ($acao === 'excluir') {
        $produtos = array_filter($produtos, fn($p) => $p['id'] !== $id);
        salvar($produtos);
    }

    if ($erro === '') {
        header('Location: ' . basename($_SERVER['PHP_SELF'])); // evita reenvio ao recarregar
        exit;
    }
}

$produtos = ler();
usort($produtos, fn($a, $b) => strcasecmp($a['nome'], $b['nome']));
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Estoque</title>
<style>
    * { box-sizing: border-box; }
    body { font-family: system-ui, sans-serif; background: #f4f4f6; margin: 0; padding: 24px; color: #222; }
    .container { max-width: 900px; margin: 0 auto; }
    h1 { margin-top: 0; }
    nav { margin-bottom: 16px; }
    nav a { margin-right: 14px; color: #2f6fed; text-decoration: none; font-weight: 600; }
    .card { background: #fff; border-radius: 10px; padding: 20px; margin-bottom: 20px; box-shadow: 0 1px 4px rgba(0,0,0,.08); }
    label { display: block; font-size: 14px; margin: 10px 0 4px; }
    input { width: 100%; padding: 9px; border: 1px solid #ccc; border-radius: 6px; font: inherit; }
    button, .btn { display: inline-block; padding: 8px 14px; border: 0; border-radius: 6px; cursor: pointer;
        font: inherit; text-decoration: none; color: #fff; background: #2f6fed; }
    .btn-mais { background: #2e9e5b; }
    .btn-menos { background: #f0a020; }
    .btn-del { background: #d64545; }
    table { width: 100%; border-collapse: collapse; }
    th, td { text-align: left; padding: 10px 8px; border-bottom: 1px solid #eee; vertical-align: middle; }
    td.acoes { white-space: nowrap; }
    td.acoes form { display: inline-flex; gap: 4px; align-items: center; }
    td.acoes input[type=number] { width: 70px; padding: 7px; }
    .erro { color: #d64545; margin-bottom: 8px; }
    .vazio { color: #888; text-align: center; padding: 20px; }
    .qtd { font-weight: 700; }
    .baixo { color: #d64545; }
    .aviso { font-size: 12px; color: #d64545; }
    .linha { display: flex; gap: 12px; }
    .linha > div { flex: 1; }
    @media (max-width: 640px) { .linha { flex-direction: column; gap: 0; } }
</style>
</head>
<body>
<div class="container">
    <h1>📦 Estoque</h1>
    <nav>
        <a href="./">✂️ Cortes</a>
        <a href="?">📦 Estoque</a>
    </nav>

    <div class="card">
        <h2>Adicionar produto</h2>
        <?php if ($erro): ?><div class="erro"><?= h($erro) ?></div><?php endif; ?>
        <form method="post" action="">
            <input type="hidden" name="acao" value="adicionar">

            <label for="nome">Nome do produto</label>
            <input id="nome" name="nome" required placeholder="Ex.: Shampoo 1L, Pomada modeladora...">

            <div class="linha">
                <div>
                    <label for="quantidade">Quantidade inicial</label>
                    <input id="quantidade" name="quantidade" type="number" min="0" value="0">
                </div>
                <div>
                    <label for="preco">Preço unitário (R$)</label>
                    <input id="preco" name="preco" type="number" step="0.01" min="0" value="0">
                </div>
            </div>

            <p><button type="submit">Adicionar produto</button></p>
        </form>
    </div>

    <div class="card">
        <h2>Produtos em estoque</h2>
        <?php if (!$produtos): ?>
            <div class="vazio">Nenhum produto cadastrado ainda.</div>
        <?php else: ?>
        <table>
            <tr><th>Produto</th><th>Qtd.</th><th>Preço</th><th>Ajustar quantidade</th><th></th></tr>
            <?php foreach ($produtos as $p): $baixo = $p['quantidade'] <= ESTOQUE_BAIXO; ?>
            <tr>
                <td><?= h($p['nome']) ?></td>
                <td class="qtd <?= $baixo ? 'baixo' : '' ?>">
                    <?= (int)$p['quantidade'] ?>
                    <?php if ($baixo): ?><div class="aviso"><?= $p['quantidade'] === 0 ? 'Sem estoque' : 'Estoque baixo' ?></div><?php endif; ?>
                </td>
                <td>R$ <?= number_format($p['preco'], 2, ',', '.') ?></td>
                <td class="acoes">
                    <form method="post" action="">
                        <input type="hidden" name="acao" value="ajustar">
                        <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                        <input type="number" name="qtd" min="1" value="1" aria-label="Quantidade a ajustar">
                        <button type="submit" name="tipo" value="entrada" class="btn-mais" title="Adicionar ao estoque">+ Adicionar</button>
                        <button type="submit" name="tipo" value="saida" class="btn-menos" title="Remover do estoque">− Remover</button>
                    </form>
                </td>
                <td>
                    <form method="post" action="" onsubmit="return confirm('Excluir este produto?');">
                        <input type="hidden" name="acao" value="excluir">
                        <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                        <button type="submit" class="btn-del">Excluir</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
</div>
</body>
</html>

