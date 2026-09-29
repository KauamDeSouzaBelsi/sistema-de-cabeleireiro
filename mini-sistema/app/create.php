<?php 
require_once  __DIR__ . '/../include/functions.php';
require_once __DIR__ . '/../login/verifica.php';
?>
<!-- erros:

da pra adicionar um corte sem precisar colocar o preço

ta sendo criado uma pasta .json


-->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>cortes</title>
<?php
// index.php — CRUD de cortes de cabelo em PHP puro (sem extensões)
// Os dados ficam no arquivo cortes.json, criado automaticamente na mesma pasta.
// Como usar: php -S localhost:8000  e abra http://localhost:8000/

const ARQUIVO = __DIR__ . '/cortes.json';

function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

function ler(): array {
    if (!file_exists(ARQUIVO)) return [];
    $dados = json_decode(file_get_contents(ARQUIVO), true);
    return is_array($dados) ? $dados : [];
}

function salvar(array $cortes): void {
    // LOCK_EX evita que dois acessos escrevam ao mesmo tempo
    file_put_contents(
        ARQUIVO,
        json_encode(array_values($cortes), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
        LOCK_EX
    );
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao      = $_POST['acao'] ?? '';
    $id        = (int)($_POST['id'] ?? 0);
    $nome      = trim($_POST['nome'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $preco     = (float)str_replace(',', '.', $_POST['preco'] ?? '0');

    $cortes = ler();

    if ($acao === 'adicionar' || $acao === 'atualizar') {
        if ($nome === '') {
            $erro = 'Informe o nome do corte.';
        } elseif ($acao === 'adicionar') {
            $novoId = $cortes ? max(array_column($cortes, 'id')) + 1 : 1;
            $cortes[] = ['id' => $novoId, 'nome' => $nome, 'descricao' => $descricao, 'preco' => $preco];
            salvar($cortes);
        } else {
            foreach ($cortes as &$c) {
                if ($c['id'] === $id) {
                    $c['nome'] = $nome;
                    $c['descricao'] = $descricao;
                    $c['preco'] = $preco;
                }
            }
            unset($c);
            salvar($cortes);
        }
    } elseif ($acao === 'excluir') {
        $cortes = array_filter($cortes, fn($c) => $c['id'] !== $id);
        salvar($cortes);
    }

    if ($erro === '') {
        header('Location: ' . basename($_SERVER['PHP_SELF'])); // evita reenvio do formulário ao recarregar
        exit;
    }
}

$cortes = ler();
usort($cortes, fn($a, $b) => strcasecmp($a['nome'], $b['nome']));

// Se veio ?editar=ID, carrega o corte para o formulário
$edicao = null;
if (isset($_GET['editar'])) {
    foreach ($cortes as $c) {
        if ($c['id'] === (int)$_GET['editar']) { $edicao = $c; break; }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Cortes de Cabelo</title>
<style>
    * { box-sizing: border-box; }
    body { font-family: system-ui, sans-serif; background: #f4f4f6; margin: 0; padding: 24px; color: #222; }
    .container { max-width: 820px; margin: 0 auto; }
    h1 { margin-top: 0; }
    .card { background: #fff; border-radius: 10px; padding: 20px; margin-bottom: 20px; box-shadow: 0 1px 4px rgba(0,0,0,.08); }
    label { display: block; font-size: 14px; margin: 10px 0 4px; }
    input, textarea { width: 100%; padding: 9px; border: 1px solid #ccc; border-radius: 6px; font: inherit; }
    button, .btn { display: inline-block; padding: 8px 14px; border: 0; border-radius: 6px; cursor: pointer;
        font: inherit; text-decoration: none; color: #fff; background: #2f6fed; }
    .btn-edit { background: #f0a020; }
    .btn-del  { background: #d64545; }
    .btn-cancel { background: #777; }
    table { width: 100%; border-collapse: collapse; }
    th, td { text-align: left; padding: 10px 8px; border-bottom: 1px solid #eee; vertical-align: top; }
    td.acoes { white-space: nowrap; }
    td.acoes form { display: inline; }
    .erro { color: #d64545; margin-bottom: 8px; }
    .vazio { color: #888; text-align: center; padding: 20px; }

    
</style>
</head>
<body>
<div class="container">

    <?php include __DIR__ . '/../include/header.php';?>

    <h1>Cortes de Cabelo</h1>

    <div class="card">
        <h2><?= $edicao ? 'Atualizar corte' : 'Adicionar corte' ?></h2>
        <?php if ($erro): ?><div class="erro"><?= h($erro) ?></div><?php endif; ?>
        <form method="post" action="">
            <input type="hidden" name="acao" value="<?= $edicao ? 'atualizar' : 'adicionar' ?>">
            <?php if ($edicao): ?><input type="hidden" name="id" value="<?= (int)$edicao['id'] ?>"><?php endif; ?>

            <label for="nome">Nome</label>
            <input id="nome" name="nome" required value="<?= h($edicao['nome'] ?? '') ?>">

            <label for="descricao">Descrição</label>
            <textarea id="descricao" name="descricao" rows="3"><?= h($edicao['descricao'] ?? '') ?></textarea>

            <label for="preco">Preço (R$)</label>
            <input id="preco" name="preco" type="number" step="0.01" min="0" value="<?= h($edicao['preco'] ?? '0') ?>">

            <p>
                <button type="submit"><?= $edicao ? 'Salvar alterações' : 'Adicionar' ?></button>
                <?php if ($edicao): ?><a class="btn btn-cancel" href="?">Cancelar</a><?php endif; ?>
            </p>
        </form>
    </div>

    <div class="card">
        <h2>Lista de cortes</h2>
        <?php if (!$cortes): ?>
            <div class="vazio">Nenhum corte cadastrado ainda.</div>
        <?php else: ?>
        <table>
            <tr><th>Nome</th><th>Descrição</th><th>Preço</th><th>Ações</th></tr>
            <?php foreach ($cortes as $c): ?>
            <tr>
                <td><?= h($c['nome']) ?></td>
                <td><?= h($c['descricao']) ?></td>
                <td>R$ <?= number_format($c['preco'], 2, ',', '.') ?></td>
                <td class="acoes">
                    <a class="btn btn-edit" href="?editar=<?= (int)$c['id'] ?>">Editar</a>
                    <form method="post" action="" onsubmit="return confirm('Excluir este corte?');">
                        <input type="hidden" name="acao" value="excluir">
                        <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
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
    <?php 
    
    if ($_SERVER['REQUEST_METHOD']== "POST"){
    var_dump($_POST);
    cadastrar($conexao, $_POST['nome'], $_POST['nasc'], $_POST['turma'], $_POST['ativo']);
    }
    ?>
</body>
</html>