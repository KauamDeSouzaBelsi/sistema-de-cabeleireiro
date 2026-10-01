<?php 
require_once  __DIR__ . '/../include/functions.php';
require_once __DIR__ . '/../login/verifica.php';
require_once __DIR__ . '/../database/connect.php';


?>
<!-- erros:

da pra adicionar um corte sem precisar colocar o preço

ta sendo criado uma pasta .json


-->
<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$arquivoJson = __DIR__ . '/cortes.json';


// LER OS CORTES
function ler()
{
    global $arquivoJson;

    if (!file_exists($arquivoJson)) {
        return [];
    }

    $conteudo = file_get_contents($arquivoJson);

    if ($conteudo == '') {
        return [];
    }

    $dados = json_decode($conteudo, true);

    if (!is_array($dados)) {
        return [];
    }

    return $dados;
}


// SALVAR OS CORTES
function salvar($cortes)
{
    global $arquivoJson;

    file_put_contents(
        $arquivoJson,
        json_encode($cortes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
    );
}


$erro = '';


// RECEBER FORMULÁRIO
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $acao = $_POST['acao'] ?? '';

    $id = $_POST['id'] ?? '';
    $nome = trim($_POST['nome'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $preco = $_POST['preco'] ?? '';


    // VERIFICAR SE O PREÇO POSSUI APENAS NÚMEROS
    if (!is_numeric($preco)) {

        $erro = "O preço deve conter apenas números.";

    } elseif ($preco < 0) {

        $erro = "O preço não pode ser negativo.";

    } else {

        $preco = (float)$preco;

        $cortes = ler();


        // ADICIONAR
        if ($acao == 'adicionar') {

            if ($nome == '' || $descricao == '') {

                $erro = "Preencha todos os campos.";

            } else {

                $novoCorte = [
                    'id' => uniqid(),
                    'nome' => $nome,
                    'descricao' => $descricao,
                    'preco' => $preco
                ];

                $cortes[] = $novoCorte;

                salvar($cortes);

                header("Location: " . basename($_SERVER['PHP_SELF']));
                exit;
            }
        }


        // ATUALIZAR
        elseif ($acao == 'atualizar') {

            foreach ($cortes as &$corte) {

                if ($corte['id'] == $id) {

                    $corte['nome'] = $nome;
                    $corte['descricao'] = $descricao;
                    $corte['preco'] = $preco;

                    break;
                }
            }

            unset($corte);

            salvar($cortes);

            header("Location: " . basename($_SERVER['PHP_SELF']));
            exit;
        }


        // EXCLUIR
        elseif ($acao == 'excluir') {

            $novoArray = [];

            foreach ($cortes as $corte) {

                if ($corte['id'] != $id) {

                    $novoArray[] = $corte;
                }
            }

            salvar($novoArray);

            header("Location: " . basename($_SERVER['PHP_SELF']));
            exit;
        }
    }
}


// PEGAR OS CORTES
$cortes = ler();


// VERIFICAR SE ESTÁ EDITANDO
$editarId = $_GET['editar'] ?? '';

$corteEdicao = null;

foreach ($cortes as $corte) {

    if ($corte['id'] == $editarId) {

        $corteEdicao = $corte;

        break;
    }
}

?>

<!DOCTYPE html>

<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Cortes</title>

</head>

<body>


<h1>Cadastro de Cortes</h1>


<?php if ($erro != ''): ?>

    <p style="color: red;">
        <?= htmlspecialchars($erro) ?>
    </p>

<?php endif; ?>


<h2>

<?php

if ($corteEdicao != null) {
    echo "Editar corte";
} else {
    echo "Cadastrar corte";
}

?>

</h2>


<form method="POST">


<?php if ($corteEdicao != null): ?>

    <input type="hidden" name="acao" value="atualizar">

    <input
        type="hidden"
        name="id"
        value="<?= htmlspecialchars($corteEdicao['id']) ?>"
    >

<?php else: ?>

    <input type="hidden" name="acao" value="adicionar">

<?php endif; ?>


<p>

    <label>Nome:</label>

    <input
        type="text"
        name="nome"
        required
        value="<?= htmlspecialchars($corteEdicao['nome'] ?? '') ?>"
    >

</p>


<p>

    <label>Descrição:</label>

    <input
        type="text"
        name="descricao"
        required
        value="<?= htmlspecialchars($corteEdicao['descricao'] ?? '') ?>"
    >

</p>


<p>

    <label>Preço:</label>

    <input
        type="number"
        name="preco"
        min="0"
        step="0.01"
        required
        value="<?= htmlspecialchars($corteEdicao['preco'] ?? '') ?>"
    >

</p>


<button type="submit">

<?php

if ($corteEdicao != null) {
    echo "Atualizar";
} else {
    echo "Cadastrar";
}

?>

</button>


<?php if ($corteEdicao != null): ?>

    <a href="create.php">
        Cancelar
    </a>

<?php endif; ?>


</form>


<hr>


<h2>Cortes cadastrados</h2>


<?php if (count($cortes) == 0): ?>

    <p>Nenhum corte cadastrado.</p>

<?php else: ?>


<table border="1" cellpadding="8">

    <tr>

        <th>Nome</th>

        <th>Descrição</th>

        <th>Preço</th>

        <th>Ações</th>

    </tr>


<?php foreach ($cortes as $corte): ?>

    <tr>

        <td>
            <?= htmlspecialchars($corte['nome']) ?>
        </td>

        <td>
            <?= htmlspecialchars($corte['descricao']) ?>
        </td>

        <td>
            R$ <?= number_format($corte['preco'], 2, ',', '.') ?>
        </td>

        <td>

            <a href="?editar=<?= urlencode($corte['id']) ?>">
                Editar
            </a>


            <form method="POST" style="display:inline;">

                <input
                    type="hidden"
                    name="acao"
                    value="excluir"
                >

                <input
                    type="hidden"
                    name="id"
                    value="<?= htmlspecialchars($corte['id']) ?>"
                >

                <button type="submit">
                    Excluir
                </button>

            </form>

        </td>

    </tr>

<?php endforeach; ?>


</table>


<?php endif; ?>


</body>

</html>