<?php

require_once "../include/functions.php";
require_once "../database/connect.php";

session_start();

$erro = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST["email"];
    $senha = $_POST["senha"];

    // Primeiro procura o administrador
    $administrador = consultar_admin($conexao, $email);

    if ($administrador && $senha == $administrador["senha"]) {

        $_SESSION["id"] = $administrador["id"];
        $_SESSION["nome"] = $administrador["nome"];
        $_SESSION["tipo"] = "admin";

        header("Location: ../index.php");
        exit();
    }

    // Se não for administrador, procura o cliente
    // Se não for administrador, procura o cliente
$usuario = consultar_user($conexao, $email);

if ($usuario && (
        password_verify($senha, $usuario["senha"])
        || $senha === $usuario["senha"]   // aceita clientes antigos, com senha em texto puro
    )) {

    $_SESSION["id"] = $usuario["id"];
    $_SESSION["nome"] = $usuario["nome"];
    $_SESSION["tipo"] = "cliente";

    header("Location: ../index.php");
    exit();
}

    echo "Usuário ou senha inválidos";
}

?>



<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style/style.css">
    <title>Login</title>
</head>
<body>
    <main>
        <?php include __DIR__ . '/../include/header.php'; ?>

        <h1>Faça o login</h1>

        <?php if ($erro != ""): ?>
            <p><?= htmlspecialchars($erro) ?></p>
        <?php endif; ?>

        <form method="POST">

        <label>E-mail:</label>
        <input type="email" name="email">

        <br><br>

        <label>Senha:</label>
        <input type="password" name="senha">

        <br><br>

        <button type="submit">Entrar</button>

        </form>

        <br>
        <a href="cadastrar.php">Cadastrar</a>
    </main>
</body>
</html>