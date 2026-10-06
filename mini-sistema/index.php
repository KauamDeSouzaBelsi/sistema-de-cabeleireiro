<?php

session_start();

include  "./database/connect.php";


// Verificar se o usuário está logado

if (!isset($_SESSION["id"])) {

    header("Location: /login/login.php");
    exit;
}


// Buscar os cortes cadastrados no banco

$sql = "SELECT id, nome, preco FROM cortes ORDER BY nome";

$comando = $conexao->query($sql);

$cortes = $comando->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>

<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <title>Cortes de cabelo</title>

</head>

<body>

<h1>Bem-vindo ao salão!</h1>


<?php if ($_SESSION["tipo"] == "admin"): ?>

    <p>
        Você está conectado como administrador.
    </p>

    <a href="/app/cortes.php">
        Cadastrar novo corte
    </a>

    <br><br>

    <a href="/app/estoque.php">
        Estoque
    </a>

    <br><br>

    <a href="/app/agenda.php">
        Ver agenda
    </a>

<?php else: ?>

    <p>
        Escolha um corte para marcar seu horário.
    </p>

<?php endif; ?>


<hr>


<h2>Cortes disponíveis</h2>


<?php if (count($cortes) == 0): ?>

    <p>
        Nenhum corte foi cadastrado ainda.
    </p>

<?php else: ?>


    <?php foreach ($cortes as $corte): ?>

        <div>

            <h3>
                <?= htmlspecialchars($corte["nome"]) ?>
            </h3>


            <p>

                Preço:

                R$

                <?= number_format(
                    $corte["preco"],
                    2,
                    ",",
                    "."
                ) ?>

            </p>


            <?php if ($_SESSION["tipo"] == "cliente"): ?>

                <a href="/app/agenda.php?corte=<?= $corte["id"] ?>">

                    <button type="button">
                        Agendar este corte
                    </button>

                </a>

            <?php endif; ?>


        </div>

        <hr>

    <?php endforeach; ?>


<?php endif; ?>


<br>


<a href="/login/logout.php">
    Sair
</a>


</body>

</html>