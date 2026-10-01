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



$erro = "";


// ======================================================
// CADASTRAR / ATUALIZAR
// ======================================================

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $acao = $_POST["acao"] ?? "";

    $id = $_POST["id"] ?? "";
    $nome = trim($_POST["nome"] ?? "");
    $descricao = trim($_POST["descricao"] ?? "");
    $preco = $_POST["preco"] ?? "";


    // Verifica o preço
    if (!is_numeric($preco)) {

        $erro = "O preço deve conter apenas números.";

    } elseif ($preco < 0) {

        $erro = "O preço não pode ser negativo.";

    } elseif ($nome == "" || $descricao == "") {

        $erro = "Preencha todos os campos.";

    } else {


        // ==================================================
        // CADASTRAR
        // ==================================================

        if ($acao == "adicionar") {

            $sql = "INSERT INTO cortes
                    (nome, descricao, preco)
                    VALUES (?, ?, ?)";

            $comando = $conexao->prepare($sql);

            $comando->execute([
                $nome,
                $descricao,
                $preco
            ]);

            header("Location: create.php");
            exit;
        }


        // ==================================================
        // ATUALIZAR
        // ==================================================

        if ($acao == "atualizar") {

            $sql = "UPDATE cortes
                    SET nome = ?,
                        descricao = ?,
                        preco = ?
                    WHERE id = ?";

            $comando = $conexao->prepare($sql);

            $comando->execute([
                $nome,
                $descricao,
                $preco,
                $id
            ]);

            header("Location: create.php");
            exit;
        }
    }
}


// ======================================================
// EXCLUIR
// ======================================================

if (isset($_GET["excluir"])) {

    $id = $_GET["excluir"];

    $sql = "DELETE FROM cortes WHERE id = ?";

    $comando = $conexao->prepare($sql);

    $comando->execute([$id]);

    header("Location: create.php");
    exit;
}


// ======================================================
// BUSCAR CORTES DO BANCO
// ======================================================

$sql = "SELECT * FROM cortes ORDER BY nome";

$comando = $conexao->query($sql);

$cortes = $comando->fetchAll(PDO::FETCH_ASSOC);


// ======================================================
// VERIFICAR EDIÇÃO
// ======================================================

$corteEdicao = null;

if (isset($_GET["editar"])) {

    $id = $_GET["editar"];

    $sql = "SELECT * FROM cortes WHERE id = ?";

    $comando = $conexao->prepare($sql);

    $comando->execute([$id]);

    $corteEdicao = $comando->fetch(PDO::FETCH_ASSOC);
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


<?php if ($erro != ""): ?>

    <p style="color: red;">
        <?= htmlspecialchars($erro) ?>
    </p>

<?php endif; ?>


<?php if ($corteEdicao != null): ?>

    <h2>Editar corte</h2>

<?php else: ?>

    <h2>Cadastrar corte</h2>

<?php endif; ?>


<form method="POST">


    <?php if ($corteEdicao != null): ?>

        <input type="hidden" name="acao" value="atualizar">

        <input
            type="hidden"
            name="id"
            value="<?= $corteEdicao["id"] ?>"
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
            value="<?= htmlspecialchars($corteEdicao["nome"] ?? "") ?>"
        >

    </p>


    <p>

        <label>Descrição:</label>

        <input
            type="text"
            name="descricao"
            required
            value="<?= htmlspecialchars($corteEdicao["descricao"] ?? "") ?>"
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
            value="<?= htmlspecialchars($corteEdicao["preco"] ?? "") ?>"
        >

    </p>


    <button type="submit">

        <?php if ($corteEdicao != null): ?>

            Atualizar

        <?php else: ?>

            Cadastrar

        <?php endif; ?>

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
                <?= htmlspecialchars($corte["nome"]) ?>
            </td>

            <td>
                <?= htmlspecialchars($corte["descricao"]) ?>
            </td>

            <td>

                R$

                <?= number_format(
                    $corte["preco"],
                    2,
                    ",",
                    "."
                ) ?>

            </td>

            <td>

                <a href="?editar=<?= $corte["id"] ?>">
                    Editar
                </a>

                |

                <a
                    href="?excluir=<?= $corte["id"] ?>"
                    onclick="return confirm('Deseja excluir este corte?')"
                >
                    Excluir
                </a>

            </td>

        </tr>

    <?php endforeach; ?>


</table>


<?php endif; ?>


</body>

</html>