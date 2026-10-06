<?php

session_start();
require_once __DIR__ . '/../include/functions.php';

$erro = "";

if ($_SERVER['REQUEST_METHOD'] === "POST") {

    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'];
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

    $usuario = consultar_user($conexao, $email);

    if ($usuario && password_verify($senha, $usuario['senha']))
    {

        $_SESSION['id'] = $usuario['id'];
        $_SESSION['nome'] = $usuario['nome'];
        $_SESSION['tipo'] = $usuario['tipo'];

        header("Location: /index.php");
        exit;

    } else {
        $erro = "Usuário ou senha inválidos.";
    }
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

        <form action="" method="post">
            <label for="email">E-mail:</label><br>
            <input type="email" name="email" id="email" required>

            <br><br>

            <label for="senha">Senha:</label><br>
            <input type="password" name="senha" id="senha" required>

            <br><br>

            <input type="submit" value="Entrar">
        </form>

        <br>
        <a href="cadastrar.php">Cadastrar</a>
    </main>
</body>
</html>
