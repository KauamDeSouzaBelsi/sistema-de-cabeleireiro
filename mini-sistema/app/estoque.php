<?php

require_once __DIR__ . "/../login/verifica.php";
require_once __DIR__ . "/../database/connect.php";
const ESTOQUE_BAIXO = 5;

function h($texto) {
    return htmlspecialchars((string)$texto, ENT_QUOTES, 'UTF-8');
}

$sql = "SELECT * FROM estoque ORDER BY id DESC";

$comando = $conexao->query($sql);

$produtos = $comando->fetchAll(PDO::FETCH_ASSOC);

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao     = $_POST['acao'] ?? '';
    $id       = (int)($_POST['id'] ?? 0);


    if ($acao === 'adicionar') {
        $nome  = trim($_POST['nome'] ?? '');
        $qtd   = max(0, (int)($_POST['quantidade'] ?? 0));
        $preco = (float)str_replace(',', '.', $_POST['preco'] ?? '0');

        if ($nome === '') {
            $erro = 'Informe o nome do produto.';
        } else {
            $sql = "INSERT INTO estoque
(nome, categoria, fornecedor, quantidade, preco_unitario)
VALUES (?, ?, ?, ?, ?)";

$comando = $conexao->prepare($sql);

$comando->execute([
    $nome,
    $_POST['categoria'] ?? '',
    $_POST['fornecedor'] ?? '',
    $qtd,
    $preco
]);
        }
    } elseif ($acao === 'ajustar') {
        // tipo = entrada (soma) ou saida (subtrai, nunca abaixo de zero)
        $tipo = $_POST['tipo'] ?? '';
        $qtd  = max(1, (int)($_POST['qtd'] ?? 1));

        $sql = "SELECT quantidade FROM estoque WHERE id = ?";
        $comando = $conexao->prepare($sql);
        $comando->execute([$id]);
        $produto = $comando->fetch(PDO::FETCH_ASSOC);

        if ($produto) {
            $novaQuantidade = (int)$produto['quantidade'];

            if ($tipo === 'entrada') {
                $novaQuantidade += $qtd;
            } elseif ($tipo === 'saida') {
                $novaQuantidade = max(0, $novaQuantidade - $qtd);
            }

            $sql = "UPDATE estoque SET quantidade = ? WHERE id = ?";
            $comando = $conexao->prepare($sql);
            $comando->execute([$novaQuantidade, $id]);
        }
    } elseif ($acao === 'excluir') {
        $sql = "DELETE FROM estoque WHERE id = ?";

        $comando = $conexao->prepare($sql);

        $comando->execute([$id]);
    }

    if ($erro === '') {
        header('Location: /app/estoque.php'); // evita reenvio ao recarregar
        exit;
    }
}

$sql = "SELECT * FROM estoque ORDER BY id DESC";

$comando = $conexao->query($sql);

$produtos = $comando->fetchAll(PDO::FETCH_ASSOC);
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
    <input type="text" id="nome" name="nome" required
           placeholder="Ex.: Shampoo 1L, Pomada modeladora...">

    <label for="categoria">Categoria</label>
    <input type="text" id="categoria" name="categoria" required
           placeholder="Ex.: Higiene, Cosméticos">

    <label for="fornecedor">Fornecedor</label>
    <input type="text" id="fornecedor" name="fornecedor" required
           placeholder="Ex.: Empresa X">

    <label for="quantidade">Quantidade</label>
    <input type="number" id="quantidade" name="quantidade" min="0" required>

    <label for="preco">Preço</label>
    <input type="number" id="preco" name="preco" min="0" step="0.01" required>

    <button type="submit">Adicionar produto</button>
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
                <td>R$ <?= number_format($p['preco_unitario'], 2, ',', '.') ?></td>
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

