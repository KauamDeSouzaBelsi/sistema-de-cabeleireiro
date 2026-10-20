<?php

session_start();

if (!isset($_SESSION["id"])) {
    header("Location: /login/login.php");
    exit();
}
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
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="style/tela-inicio.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Barbearia</title>

    <link rel="stylesheet" href="style/tela-inicio.css">

</head>

<body>

<h1>Bem-vindo ao salão!</h1>




<?php if (isset($_SESSION["tipo"]) && $_SESSION["tipo"] == "admin"): ?>

    <p>Você está conectado como administrador.</p>

    <nav class="menu-admin">
        <a href="/app/cortes.php">Cadastrar novo corte</a>
        <a href="/app/estoque.php">Estoque</a>
        <a href="/app/agenda.php">Ver agenda</a>
    </nav>

<?php else: ?>

    <p>Escolha um corte para marcar seu horário.</p>

<?php endif; ?>


<hr>


<h2>Cortes disponíveis</h2>


<?php if (count($cortes) == 0): ?>

    <p>
        Nenhum corte foi cadastrado ainda.
    </p>

<?php else: ?>


    <?php foreach ($cortes as $corte): ?>

        <div class="card-corte">

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


            <?php if (isset($_SESSION["tipo"]) && $_SESSION["tipo"] == "cliente"): ?>

                <a href="/app/agenda.php?corte=<?= $corte["id"] ?>">

                    <button type="button">
                        Agendar este corte
                    </button>

                </a>

            <?php endif; ?>


        </div>

        

    <?php endforeach; ?>


<?php endif; ?>


<br>


<a class="link-sair" href="/login/logout.php">
    Sair
</a>


</body>

</html>