<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>tela de inicio</title>
</head>
<body>
    <?php include __DIR__ . '/./include/header.php'; ?>

    
    
    <?php
    header("Location: login/login.php");
    exit;
    ?>

    <h1>Seja Bem Vindo</h1>

    <img src="imagens/salao.jpg" alt="fundo de um salão" width="200" height="100" >
    <?php include __DIR__ . '/./include/footer.php'; ?>
</body>
</html>